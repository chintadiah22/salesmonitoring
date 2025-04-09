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

// Ambil data dari database
$query = "SELECT kd, nm, alamat, tlp, jmlvoucher FROM m_nasabah WHERE aktif = 'Aktif'";
$result = mysqli_query($conn, $query);

// Buat file Excel baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$headers = ['No', 'ID', 'Nama', 'Alamat', 'Telepon', 'Poin'];
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
$no = 1;
while ($data = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue("A$row", $no++);
    $sheet->setCellValue("B$row", $data['kd']);
    $sheet->setCellValue("C$row", $data['nm']);
    $sheet->setCellValue("D$row", $data['alamat']);
    $sheet->setCellValue("E$row", $data['tlp']);
    $sheet->setCellValue("F$row", $data['jmlvoucher']);
    $row++;
}

// Beri border pada seluruh tabel
$sheet->getStyle("A1:F" . ($row - 1))->applyFromArray([
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
header('Content-Disposition: attachment;filename="Data_Konsumen.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>
