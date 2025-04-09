<?php
require 'config/function.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A2', 'No');
$sheet->setCellValue('B2', 'Tanggal');
$sheet->setCellValue('C2', 'Notrs');
$sheet->setCellValue('D2', 'Subtotal');
$sheet->setCellValue('E2', 'Jml Disfaktur');
$sheet->setCellValue('F2', 'Jml Pajak');
$sheet->setCellValue('G2', 'Grand Total');
$sheet->setCellValue('H2', 'Tunai');
$sheet->setCellValue('I2', 'Kartu');
$sheet->setCellValue('J2', 'Sisa Kurang');

$data_laporan = query("SELECT  
`tgl`
, `notrs`
, `subtotal`
, `jmldisfaktur`
, `jmlpajak`
, `grandtotal`
, `tunai`
, `kartu`
, `sisakurang` FROM qry_jual ");

$no = 1;
$start = 3;

foreach ($data_laporan as $laporan) {
    $sheet->setCellValue('A' . $start, $no++)->getColumnDimension('A')->setAutoSize(true);
    $sheet->setCellValue('B' . $start, $laporan['tgl'])->getColumnDimension('B')->setAutoSize(true);
    $sheet->setCellValue('C' . $start, $laporan['notrs'])->getColumnDimension('C')->setAutoSize(true);
    $sheet->setCellValue('D' . $start, $laporan['subtotal'])->getColumnDimension('D')->setAutoSize(true);
    $sheet->setCellValue('E' . $start, $laporan['jmldisfaktur'])->getColumnDimension('E')->setAutoSize(true);
    $sheet->setCellValue('F' . $start, $laporan['jmlpajak'])->getColumnDimension('F')->setAutoSize(true);
    $sheet->setCellValue('G' . $start, $laporan['grandtotal'])->getColumnDimension('G')->setAutoSize(true);
    $sheet->setCellValue('H' . $start, $laporan['tunai'])->getColumnDimension('H')->setAutoSize(true);
    $sheet->setCellValue('I' . $start, $laporan['kartu'])->getColumnDimension('I')->setAutoSize(true);
    $sheet->setCellValue('J' . $start, $laporan['sisakurang'])->getColumnDimension('J')->setAutoSize(true);

    $start++;
}

// Tabel Border
$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
];

$border = $start - 1;

$sheet->getStyle('A2:J' . $border)->applyFromArray($styleArray);



$writer = new Xlsx($spreadsheet);
$writer->save('Data Laporan.xlsx');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet.sheet');
header('Content-Disposition: attachment;filename="Data Laporan.xlsx"');
readfile('Data Laporan.xlsx');
unlink('Data Laporan.xlsx');
exit;
