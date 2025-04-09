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

// Ambil filter dari request
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : '';
$kategori = isset($_GET['m_kategori']) ? $_GET['m_kategori'] : '';
$cabang = isset($_GET['cabang']) ? $_GET['cabang'] : '';

// Query untuk laporan penjualan per kategori
$query = "SELECT nmkategori, SUM(jml) AS jml, SUM(total) AS total, SUM(totalbeli) AS totalbeli 
          FROM qry_jual_peritem 
          WHERE 1=1";

if (!empty($kategori)) {
    $query .= " AND m_kategori = '$kategori'";
}
if (!empty($cabang)) {
    $query .= " AND m_gudang.nm = '$cabang'";
}
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $query .= " AND t_beli_hd.tgljthtmp BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$query .= " GROUP BY nmkategori";
$result = mysqli_query($conn, $query);

// Buat spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header tabel
$headers = ['Kategori', 'Jumlah Qty', 'Total', 'Total Pembelian'];
$columnLetters = range('A', 'D');

$sheet->getStyle('A1:D1')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
]);

foreach ($headers as $key => $header) {
    $sheet->setCellValue($columnLetters[$key] . '1', $header);
}

// Isi data
$row = 2;
while ($data = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue("A$row", $data['nmkategori']);
    $sheet->setCellValue("B$row", $data['jml']);
    $sheet->setCellValue("C$row", $data['total']);
    $sheet->setCellValue("D$row", $data['totalbeli']);

    // Terapkan format angka dengan pemisah ribuan
    foreach (['B', 'C', 'D'] as $col) {
        $sheet->getStyle("$col$row")->getNumberFormat()->setFormatCode('#,##0');
    }

    $row++;
}

// Format border
$sheet->getStyle("A1:D$row")->applyFromArray([
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
    ]
]);

// Auto-size kolom
foreach ($columnLetters as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Simpan sebagai file Excel
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan_Penjualan_Per_Kategori.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>
