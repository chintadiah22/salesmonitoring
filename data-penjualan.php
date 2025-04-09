<?php
include 'layout/header.php';
include 'config/function.php';

// Ambil filter dari form, default ke hari ini
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-d');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// Query untuk mengambil data penjualan
$query = "SELECT  
            tgl,
            jmldisfaktur,
            jmlpajak,
            grandtotal,
            tunai,
            kartu,
            sisakurang 
          FROM qry_jual 
          WHERE tgl BETWEEN '$tgl_awal' AND '$tgl_akhir'";

$data_laporan = query($query);

// Variabel total keseluruhan
$total_disfaktur = 0;
$total_pajak = 0;
$total_grand = 0;
$total_tunai = 0;
$total_kartu = 0;
$total_sisa = 0;
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h4 class="m-0">Laporan Data Penjualan</h4>
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Data Penjualan</li>
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
                                        <a href="export_datapenjualan.php?tgl_awal=<?= $tgl_awal; ?>&tgl_akhir=<?= $tgl_akhir; ?>" target="_blank" class="btn btn-success btn-block">Export ke Excel</a>
                                    </div>
                                </div>
                            </form>

                            <hr>

                            <table class="table table-bordered table-striped mt-3" id="table">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jumlah Diskon Faktur</th>
                                        <th>Jumlah Pajak</th>
                                        <th>Grand Total</th>
                                        <th>Tunai</th>
                                        <th>Kartu</th>
                                        <th>Sisa Kurang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($data_laporan as $laporan) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= date('d-m-Y', strtotime($laporan['tgl'])); ?></td>
                                            <td><?= number_format($laporan['jmldisfaktur'], 0, ',', '.'); ?></td>
                                            <td><?= number_format($laporan['jmlpajak'], 0, ',', '.'); ?></td>
                                            <td><?= number_format($laporan['grandtotal'], 0, ',', '.'); ?></td>
                                            <td><?= number_format($laporan['tunai'], 0, ',', '.'); ?></td>
                                            <td><?= number_format($laporan['kartu'], 0, ',', '.'); ?></td>
                                            <td><?= number_format($laporan['sisakurang'], 0, ',', '.'); ?></td>
                                        </tr>
                                        <?php 
                                            $total_disfaktur += $laporan['jmldisfaktur'];
                                            $total_pajak += $laporan['jmlpajak'];
                                            $total_grand += $laporan['grandtotal'];
                                            $total_tunai += $laporan['tunai'];
                                            $total_kartu += $laporan['kartu'];
                                            $total_sisa += $laporan['sisakurang'];
                                        ?>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="2" class="text-center">Total</th>
                                        <th><?= number_format($total_disfaktur, 0, ',', '.'); ?></th>
                                        <th><?= number_format($total_pajak, 0, ',', '.'); ?></th>
                                        <th><?= number_format($total_grand, 0, ',', '.'); ?></th>
                                        <th><?= number_format($total_tunai, 0, ',', '.'); ?></th>
                                        <th><?= number_format($total_kartu, 0, ',', '.'); ?></th>
                                        <th><?= number_format($total_sisa, 0, ',', '.'); ?></th>
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
