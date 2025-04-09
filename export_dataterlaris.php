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

$query = "SELECT  
    t_jual_dt.itemfk, 
    m_item.nm, 
    SUM(t_jual_dt.jml) AS jmlqty, 
    SUM(t_jual_dt.total) AS total
FROM m_item 
    INNER JOIN t_jual_dt ON (m_item.pk = t_jual_dt.itemfk)
    INNER JOIN t_jual_hd ON (t_jual_hd.notrs = t_jual_dt.notrs)
WHERE date_format(t_jual_hd.tgl,'%Y%m%d') BETWEEN '$tgl_awal' AND '$tgl_akhir'
GROUP BY t_jual_dt.itemfk, m_item.nm
ORDER BY SUM(t_jual_dt.jml) DESC";

$data_laporan = query($query);

// Buat file Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header tabel
$headers = ['No', 'Nama Barang', 'Jumlah Terjual', 'Total Penjualan'];
$columnLetters = ['A', 'B', 'C', 'D'];

// Format header
$sheet->getStyle('A1:D1')->applyFromArray([
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
    $sheet->setCellValue("B$row", $laporan['nm']);
    $sheet->setCellValue("C$row", $laporan['jmlqty']);
    $sheet->setCellValue("D$row", $laporan['total']);
    $row++;
}

// Format border
$sheet->getStyle("A1:D" . ($row - 1))->applyFromArray([
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
header("Content-Disposition: attachment;filename=\"Data_Terlaris_$tgl_awal _ $tgl_akhir.xlsx\"");
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>
