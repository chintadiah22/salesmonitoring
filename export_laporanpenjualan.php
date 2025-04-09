<?php
require 'vendor/autoload.php';
include 'config/function.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "salesmonitoring");

// Ambil filter dari request
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : '';
$kasir = isset($_GET['kasir']) ? $_GET['kasir'] : '';
$cabang = isset($_GET['cabang']) ? $_GET['cabang'] : '';

// Query utama
$query = "SELECT t_jual_hd.notrs, t_jual_hd.tgl, t_jual_hd.grandtotal, t_jual_hd.voucher, 
                 t_jual_hd.kartu, t_jual_hd.sisakurang, m_user.kd AS useradd 
          FROM t_jual_hd 
          LEFT JOIN m_user ON m_user.pk = t_jual_hd.addedbyfk 
          WHERE 1=1";

if (!empty($kasir)) {
    $query .= " AND m_user.pk = '$kasir'";
}
if (!empty($cabang)) {
    $query .= " AND t_jual_hd.gudangfk = '$cabang'";
}
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $query .= " AND t_jual_hd.tgl BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$result = mysqli_query($conn, $query);

// Buat objek spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$headers = ['No Transaksi', 'Tanggal', 'Kasir', 'Grand Total', 'Voucher', 'Tunai', 'Non Tunai', 'Piutang'];
$columnLetters = range('A', 'H');

$sheet->getStyle('A1:H1')->applyFromArray([
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
    $sheet->setCellValue("A$row", $data['notrs']);
    $sheet->setCellValue("B$row", date('d/m/Y', strtotime($data['tgl'])));
    $sheet->setCellValue("C$row", $data['useradd']);
    $sheet->setCellValue("D$row", $data['grandtotal']);
    $sheet->setCellValue("E$row", $data['voucher']);
    $sheet->setCellValue("F$row", $data['grandtotal'] - $data['voucher']); // Tunai
    $sheet->setCellValue("G$row", $data['kartu']); // Non Tunai
    $sheet->setCellValue("H$row", $data['sisakurang']); // Piutang

    // Terapkan format angka dengan pemisah ribuan
    foreach (['D', 'E', 'F', 'G', 'H'] as $col) {
        $sheet->getStyle("$col$row")->getNumberFormat()->setFormatCode('#,##0');
    }

    $row++;
}

// Format border
$sheet->getStyle("A1:H$row")->applyFromArray([
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
header('Content-Disposition: attachment;filename="Laporan_Penjualan.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>
