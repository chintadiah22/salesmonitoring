<?php

require_once __DIR__ . '/vendor/autoload.php';
include 'config/function.php';

$data_laporan = query("SELECT `t_jual_dt`.`itemfk` , SUM(`t_jual_dt`.`jml`) AS `Jml` , `m_item`.`nm` FROM `t_jual_hd` INNER JOIN `t_jual_dt` ON (`t_jual_hd`.`notrs` = `t_jual_dt`.`notrs`) INNER JOIN `m_item` ON (`t_jual_dt`.`itemfk` = `m_item`.`pk`) WHERE DATE_FORMAT(tgl,'%Y%m')='202301' GROUP BY `t_jual_dt`.`itemfk`, `m_item`.`nm` ORDER BY SUM(`t_jual_dt`.`jml`) DESC LIMIT 10");

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
              <th>Item</th>
              <th>Jumlah</th>
              <th>Nama</th>              
        </tr>';

$i = 1;
foreach ($data_laporan as $laporan) {
    $html .= '<tr>
            <td>' . $i++ . '</td>            
            <td>' . $laporan["itemfk"] . '</td>
            <td>' . $laporan["Jml"] . '</td>
            <td>' . $laporan["nm"] . '</td>           
            </tr>';
}


$html .= '</table>
    
</body>
</html>';

$mpdf->WriteHTML($html);
$mpdf->Output('data-penjualan.pdf', \Mpdf\Output\Destination::INLINE);
