<?php
require 'config/function.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A2', 'No');
$sheet->setCellValue('B2', 'Item');
$sheet->setCellValue('C2', 'Jumlah');
$sheet->setCellValue('D2', 'Nama');

$data_laporan = query("SELECT `t_jual_dt`.`itemfk` , SUM(`t_jual_dt`.`jml`) AS `Jml` , `m_item`.`nm` FROM `t_jual_hd` INNER JOIN `t_jual_dt` ON (`t_jual_hd`.`notrs` = `t_jual_dt`.`notrs`) INNER JOIN `m_item` ON (`t_jual_dt`.`itemfk` = `m_item`.`pk`) WHERE DATE_FORMAT(tgl,'%Y%m')='202301' GROUP BY `t_jual_dt`.`itemfk`, `m_item`.`nm` ORDER BY SUM(`t_jual_dt`.`jml`) DESC LIMIT 10");

$no = 1;
$start = 3;

foreach ($data_laporan as $laporan) {
    $sheet->setCellValue('A' . $start, $no++)->getColumnDimension('A')->setAutoSize(true);
    $sheet->setCellValue('B' . $start, $laporan['itemfk'])->getColumnDimension('B')->setAutoSize(true);
    $sheet->setCellValue('C' . $start, $laporan['Jml'])->getColumnDimension('C')->setAutoSize(true);
    $sheet->setCellValue('D' . $start, $laporan['nm'])->getColumnDimension('D')->setAutoSize(true);
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

$sheet->getStyle('A2:D' . $border)->applyFromArray($styleArray);



$writer = new Xlsx($spreadsheet);
$writer->save('Data Laporan.xlsx');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet.sheet');
header('Content-Disposition: attachment;filename="Data Laporan.xlsx"');
readfile('Data Laporan.xlsx');
unlink('Data Laporan.xlsx');
exit;
