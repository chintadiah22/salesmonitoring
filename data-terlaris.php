<?php
include 'layout/header.php';
include 'config/function.php';

// Ambil filter dari form, default ke hari ini
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-d');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// Query untuk mengambil data barang terlaris
$query = "SELECT  
            t_jual_dt.itemfk,
            m_item.nm,
            SUM(t_jual_dt.jml) AS jmlqty,
            SUM(t_jual_dt.total) AS total
          FROM m_item
          INNER JOIN t_jual_dt ON m_item.pk = t_jual_dt.itemfk
          INNER JOIN t_jual_hd ON t_jual_hd.notrs = t_jual_dt.notrs
          WHERE t_jual_hd.tgl BETWEEN '$tgl_awal' AND '$tgl_akhir'
          GROUP BY t_jual_dt.itemfk, m_item.nm
          ORDER BY SUM(t_jual_dt.jml) DESC";

$data_laporan = query($query);

// Variabel untuk menghitung total keseluruhan
$total_qty = 0;
$total_penjualan = 0;
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h4 class="m-0">Laporan Barang Terlaris</h4>
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Barang Terlaris</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="GET">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Tanggal Awal</label>
                                        <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal; ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Tanggal Akhir</label>
                                        <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir; ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label>
                                        <a href="export_dataterlaris.php?tgl_awal=<?= $tgl_awal; ?>&tgl_akhir=<?= $tgl_akhir; ?>" target="_blank" class="btn btn-success btn-block">Export ke Excel</a>
                                    </div>
                                </div>
                            </form>

                            <hr>

                            <table class="table table-bordered table-striped mt-3" id="table">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah Terjual</th>
                                        <th>Total Penjualan (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($data_laporan as $laporan) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $laporan['nm']; ?></td>
                                            <td><?= number_format($laporan['jmlqty'], 0, ',', '.'); ?></td>
                                            <td><?= number_format($laporan['total'], 0, ',', '.'); ?></td>
                                        </tr>
                                        <?php 
                                            $total_qty += $laporan['jmlqty'];
                                            $total_penjualan += $laporan['total'];
                                        ?>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="2" class="text-center">Total</th>
                                        <th><?= number_format($total_qty, 0, ',', '.'); ?></th>
                                        <th><?= number_format($total_penjualan, 0, ',', '.'); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'layout/footer.php'; ?>
