<?php
require 'vendor/autoload.php';
include 'config/function.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$conn = mysqli_connect("localhost", "root", "", "salesmonitoring");

$tgl_awal = isset($_POST['tgl_awal']) ? $_POST['tgl_awal'] : '';
$tgl_akhir = isset($_POST['tgl_akhir']) ? $_POST['tgl_akhir'] : '';
$konsumen = isset($_POST['konsumen']) ? $_POST['konsumen'] : '';

$query = "SELECT t_jual_hd.notrs, t_jual_hd.tgl, t_jual_hd.tgljthtmp, 
                 t_jual_hd.konsumenfk, m_nasabah.nm AS nmkonsumen, 
                 t_jual_hd.grandtotal, t_jual_hd.bayar, 
                 t_jual_hd.grandtotal - t_jual_hd.bayar AS piutang, 
                 IFNULL(SUM(t_bayarpiutang_dt.bayar), 0) AS terbayar, 
                 (t_jual_hd.grandtotal - t_jual_hd.bayar) - IFNULL(SUM(t_bayarpiutang_dt.bayar), 0) AS sisapiutang, 
                 t_jual_hd.carabayar
          FROM m_nasabah 
          INNER JOIN (t_jual_hd LEFT JOIN t_bayarpiutang_dt ON t_jual_hd.notrs = t_bayarpiutang_dt.noref) 
          ON m_nasabah.pk = t_jual_hd.konsumenfk";

if (!empty($konsumen)) {
    $query .= " AND t_jual_hd.konsumenfk = '$konsumen'";
}

if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $query .= " AND t_jual_hd.tgljthtmp BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$query .= " GROUP BY t_jual_hd.notrs, t_jual_hd.tgl, t_jual_hd.tgljthtmp, 
                   t_jual_hd.konsumenfk, m_nasabah.nm, 
                   t_jual_hd.grandtotal, t_jual_hd.bayar, 
                   t_jual_hd.grandtotal - t_jual_hd.bayar, 
                   t_jual_hd.carabayar
          HAVING t_jual_hd.carabayar = 2 AND sisapiutang > 0";

$result = mysqli_query($conn, $query);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$headers = ['No Transaksi', 'Tanggal', 'Tgl Jatuh Tempo', 'Piutang', 'Terbayar', 'Sisa Piutang'];
$columnLetters = ['A', 'B', 'C', 'D', 'E', 'F'];

// Format header dengan warna dan bold
$sheet->getStyle('A1:F1')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
]);

// Set nilai header
foreach ($headers as $key => $header) {
    $sheet->setCellValue($columnLetters[$key] . '1', $header);
}

// Isi data
$row = 2;
$total_piutang = 0;
$total_terbayar = 0;
$total_sisa = 0;

while ($data = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue("A$row", $data['notrs']);
    $sheet->setCellValue("B$row", date('d/m/Y', strtotime($data['tgl'])));
    $sheet->setCellValue("C$row", date('d/m/Y', strtotime($data['tgljthtmp'])));
    $sheet->setCellValue("D$row", $data['piutang']);
    $sheet->setCellValue("E$row", $data['terbayar']);
    $sheet->setCellValue("F$row", $data['sisapiutang']);

    // Hitung total
    $total_piutang += $data['piutang'];
    $total_terbayar += $data['terbayar'];
    $total_sisa += $data['sisapiutang'];

    $row++;
}

// Tambahkan baris total di bawah tabel
$sheet->setCellValue("A$row", "TOTAL");
$sheet->mergeCells("A$row:C$row"); // Gabungkan 3 kolom untuk tulisan "TOTAL"
$sheet->setCellValue("D$row", $total_piutang);
$sheet->setCellValue("E$row", $total_terbayar);
$sheet->setCellValue("F$row", $total_sisa);

// Format total row dengan warna background
$sheet->getStyle("A$row:F$row")->applyFromArray([
    'font' => ['bold' => true],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFD966']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
]);

// Beri border pada seluruh tabel
$sheet->getStyle("A1:F$row")->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
]);

// Auto-size kolom
foreach ($columnLetters as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Simpan sebagai file Excel
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan_Hutang.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>