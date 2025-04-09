<?php
require 'vendor/autoload.php';
include 'config/function.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$conn = mysqli_connect("localhost", "root", "", "salesmonitoring");

$tgl_awal = isset($_POST['tgl_awal']) ? $_POST['tgl_awal'] : '';
$tgl_akhir = isset($_POST['tgl_akhir']) ? $_POST['tgl_akhir'] : '';
$supplier = isset($_POST['supplier']) ? $_POST['supplier'] : '';

$query = "SELECT t_beli_hd.notrs, t_beli_hd.gudangfk, t_beli_hd.tgl, t_beli_hd.tgljthtmp, t_beli_hd.supplierfk, 
m_supplier.nm AS nmsupplier, t_beli_hd.grandtotal, t_beli_hd.bayar, 
t_beli_hd.grandtotal-t_beli_hd.bayar-t_beli_hd.deposit AS hutang, 
IF(ISNULL(SUM(t_bayarhutang_dt.bayar)),0,SUM(t_bayarhutang_dt.bayar)) AS terbayar, 
(t_beli_hd.grandtotal-t_beli_hd.bayar-t_beli_hd.jmlretur-t_beli_hd.deposit) - 
IF(ISNULL(SUM(t_bayarhutang_dt.bayar)),0,SUM(t_bayarhutang_dt.bayar)) AS sisahutang, 
t_beli_hd.carabayar, t_beli_hd.jmlretur, m_supplier.tipe 
FROM m_supplier 
INNER JOIN (t_beli_hd LEFT JOIN t_bayarhutang_dt ON t_beli_hd.notrs = t_bayarhutang_dt.noref) 
ON m_supplier.pk = t_beli_hd.supplierfk 
WHERE 1=1";

if (!empty($supplier)) {
    $query .= " AND t_beli_hd.supplierfk = '$supplier'";
}

if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $query .= " AND t_beli_hd.tgljthtmp BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$query .= " GROUP BY m_supplier.tipe, t_beli_hd.jmlretur, t_beli_hd.gudangfk, t_beli_hd.notrs, 
t_beli_hd.tgl, t_beli_hd.tgljthtmp, t_beli_hd.supplierfk, m_supplier.nm, 
t_beli_hd.grandtotal, t_beli_hd.bayar, t_beli_hd.grandtotal-t_beli_hd.bayar-t_beli_hd.deposit, 
t_beli_hd.carabayar 
HAVING (((t_beli_hd.carabayar)=2) AND 
(((t_beli_hd.grandtotal-t_beli_hd.bayar-t_beli_hd.jmlretur)-IF(ISNULL(SUM(t_bayarhutang_dt.bayar)),0,SUM(t_bayarhutang_dt.bayar)))>0))";

$result = mysqli_query($conn, $query);

// Buat file Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$sheet->setCellValue('A1', 'No Transaksi');
$sheet->setCellValue('B1', 'Tanggal');
$sheet->setCellValue('C1', 'Tgl Jatuh Tempo');
$sheet->setCellValue('D1', 'Hutang');
$sheet->setCellValue('E1', 'Terbayar');
$sheet->setCellValue('F1', 'Sisa Hutang');

$row = 2; 
$total_Hutang = 0;
$total_terbayar = 0;
$total_sisa = 0;

while ($data = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue("A$row", $data['notrs']);
    $sheet->setCellValue("B$row", date('d/m/Y', strtotime($data['tgl'])));
    $sheet->setCellValue("C$row", date('d/m/Y', strtotime($data['tgljthtmp'])));
    $sheet->setCellValue("D$row", $data['Hutang']);
    $sheet->setCellValue("E$row", $data['terbayar']);
    $sheet->setCellValue("F$row", $data['Sisa Hutang']);

    // Hitung total
    $total_piutang += $data['Hutang'];
    $total_terbayar += $data['terbayar'];
    $total_sisa += $data['Sisa Hutang'];

    $row++;
}

// Tambahkan baris total di bawah tabel
$sheet->setCellValue("A$row", "TOTAL");
$sheet->mergeCells("A$row:C$row"); // Gabungkan 3 kolom untuk tulisan "TOTAL"
$sheet->setCellValue("D$row", $total_Hutang);
$sheet->setCellValue("E$row", $total_terbayar);
$sheet->setCellValue("F$row", $total_sisa);

// Simpan sebagai file Excel
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan_Hutang.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>