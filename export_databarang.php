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

$query = "SELECT m_item.kd, m_item.nm, m_item.hargapokok, m_item.hargajl, 
                 tmp_stok.stok, m_satuan.nm AS satuan, m_gudang.nm AS cabang 
          FROM m_item 
          INNER JOIN m_satuan ON (m_item.satuanfk = m_satuan.pk) 
          LEFT JOIN tmp_stok ON (m_item.pk = tmp_stok.itemfk) 
          LEFT JOIN m_gudang ON (tmp_stok.gudangfk = m_gudang.pk) 
          WHERE m_item.aktif = 1";

$result = mysqli_query($conn, $query);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$headers = ['No', 'Kode', 'Nama', 'HPP', 'Harga Jual', 'Stok', 'Satuan', 'Cabang'];
$columnLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

// Format header dengan warna dan bold
$sheet->getStyle('A1:H1')->applyFromArray([
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
$no = 1;
while ($data = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue("A$row", $no++);
    $sheet->setCellValue("B$row", $data['kd']);
    $sheet->setCellValue("C$row", $data['nm']);
    $sheet->setCellValue("D$row", $data['hargapokok']);
    $sheet->setCellValue("E$row", $data['hargajl']);
    $sheet->setCellValue("F$row", $data['stok']);
    $sheet->setCellValue("G$row", $data['satuan']);
    $sheet->setCellValue("H$row", $data['cabang']);
    $row++;
}

// Beri border pada seluruh tabel
$sheet->getStyle("A1:H" . ($row - 1))->applyFromArray([
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
header('Content-Disposition: attachment;filename="Data_Barang.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>