<?php
require 'vendor/autoload.php';
include 'config/function.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Ambil data dari filter jika ada
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-d');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

$query = "SELECT tgl, jmldisfaktur, jmlpajak, grandtotal, tunai, kartu, sisakurang 
          FROM qry_jual WHERE tgl BETWEEN '$tgl_awal' AND '$tgl_akhir'";
$data_laporan = query($query);

// Buat file Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header tabel
$headers = ['No', 'Tanggal', 'Jumlah Disfaktur', 'Jumlah Pajak', 'Grand Total', 'Tunai', 'Kartu', 'Sisa Kurang'];
$columnLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

// Format header
$sheet->getStyle('A1:H1')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
]);

// Set header di Excel
foreach ($headers as $key => $header) {
    $sheet->setCellValue($columnLetters[$key] . '1', $header);
}

// Isi data
$row = 2;
$no = 1;
foreach ($data_laporan as $laporan) {
    $sheet->setCellValue("A$row", $no++);
    $sheet->setCellValue("B$row", date('d-m-Y', strtotime($laporan['tgl'])));
    $sheet->setCellValue("C$row", $laporan['jmldisfaktur']);
    $sheet->setCellValue("D$row", $laporan['jmlpajak']);
    $sheet->setCellValue("E$row", $laporan['grandtotal']);
    $sheet->setCellValue("F$row", $laporan['tunai']);
    $sheet->setCellValue("G$row", $laporan['kartu']);
    $sheet->setCellValue("H$row", $laporan['sisakurang']);
    $row++;
}

// Format border
$sheet->getStyle("A1:H" . ($row - 1))->applyFromArray([
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
header("Content-Disposition: attachment;filename=\"Laporan_$tgl_awal _ $tgl_akhir.xlsx\"");
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>
