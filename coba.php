<?php
include 'layout/header.php';
include 'config/function.php';
?>
<div class="container">
    <h2 class="mt-5 ">Data Barang</h2>
    <table class="table table-hover table-striped mt-5 table-bordered " id="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Outlet</th>
                <th>Tgl</th>
                <th>Jenis</th>
                <th>No Transaksi</th>
                <th>Kasir</th>
                <th>Subtotal</th>
                <th>Diskon</th>
                <th>Pajak</th>
                <th>Total</th>
                <th>Tunai</th>
                <th>Kartu</th>
            </tr>

        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Cabang Singaraja</td>
                <td>20/01/2023</td>
                <td>POS</td>
                <td>111111</td>
                <td>Admin</td>
                <td>200000</td>
                <td>0</td>
                <td>0</td>
                <td>200000</td>
                <td>0</td>
                <td>0</td>
            </tr>

        </tbody>
    </table>
</div>








<?php include 'layout/footer.php' ?>