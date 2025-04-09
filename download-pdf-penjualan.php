<?php

require_once __DIR__ . '/vendor/autoload.php';
include 'config/function.php';

$data_laporan = query("SELECT
`tgl`
, `notrs`
, `subtotal`
, `jmldisfaktur`
, `jmlpajak`
, `grandtotal`
, `tunai`
, `kartu`
, `sisakurang`
FROM
`qry_jual`;");

$mpdf = new \Mpdf\Mpdf();

$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="pdfstyle.css">
    <title>Document</title>
</head>
<body>

<h4>Data Laporan</h4>
<hr>
<table border="1" cellpadding="10" cellspacing="0">
        <tr>
              <th>No</th>
              <th>Tgl</th>
              <th>Notrs</th>
              <th>Subtotal</th>
              <th>Jml Disfaktur</th>
              <th>Jml Pajak</th>
              <th>Grand Total</th>
              <th>Tunai</th>
              <th>Kartu</th>
              <th>Sisa Kurang</th>
        </tr>';

$i = 1;
foreach ($data_laporan as $laporan) {
    $html .= '<tr>
            <td>' . $i++ . '</td>            
            <td>' . $laporan["tgl"] . '</td>
            <td>' . $laporan["notrs"] . '</td>
            <td>' . $laporan["subtotal"] . '</td>
            <td>' . $laporan["jmldisfaktur"] . '</td>
            <td>' . $laporan["jmlpajak"] . '</td>
            <td>' . $laporan["grandtotal"] . '</td>
            <td>' . $laporan["tunai"] . '</td>
            <td>' . $laporan["kartu"] . '</td>
            <td>' . $laporan["sisakurang"] . '</td>
            </tr>';
}


$html .= '</table>
    
</body>
</html>';

$mpdf->WriteHTML($html);
$mpdf->Output('data-penjualan.pdf', \Mpdf\Output\Destination::INLINE);
