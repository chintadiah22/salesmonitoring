<?php

include 'config/function.php';
include 'config/add-grafik.php';
include 'layout/header.php';

$laporan_data = query("SELECT `t_jual_dt`.`itemfk` , SUM(`t_jual_dt`.`jml`) AS `Jml` , `m_item`.`nm` FROM `t_jual_hd` INNER JOIN `t_jual_dt` ON (`t_jual_hd`.`notrs` = `t_jual_dt`.`notrs`) INNER JOIN `m_item` ON (`t_jual_dt`.`itemfk` = `m_item`.`pk`) WHERE DATE_FORMAT(tgl,'%Y%m')=DATE_FORMAT(CURRENT_DATE(),'%Y%m') GROUP BY `t_jual_dt`.`itemfk`, `m_item`.`nm` ORDER BY SUM(`t_jual_dt`.`jml`) DESC LIMIT 10");

?>
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h4 class="m-0">Dashboard Statik Penjualan Dan Lain - lain</h4>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Dashboard </li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- ======================= Main Content ========================= -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">

        <div class="col-12 col-sm-6 col-md-4">
          <small>Pembelian </small>
          <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-shopping-basket"></i></span>
            <div class="info-box-content">Jumlah Pembelian Hari Ini
              <span class="info-box-number">
                <?php echo "Rp. " . number_format($jumlah_pembelian, 0, ',', '.'); ?>
              </span>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
          <small>Penjualan</small>
          <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-map-marked-alt"></i></span>
            <div class="info-box-content">Jumlah Penjualan Hari Ini
              <span class="info-box-number"><?php echo "Rp. " . number_format($jumlah_penjualan, 0, ',', '.'); ?></span>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
          <small>Profit </small>
          <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-file-invoice-dollar"></i></span>
            <div class="info-box-content">Total Profit Penjualan
              <span class="info-box-number"><?php echo "Rp. " . number_format($jumlah_profit, 0, ',', '.'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- ======================= /. Main Content ========================= -->


      <!-- ======================= Grafik Penjualan ========================= -->
      <div class="card mb-5">
        <section class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1>Grafik Penjualan</h1>
              </div>
              <canvas id="myChart"></canvas>
            </div>
          </div><!-- /.container-fluid -->
        </section>
      </div>


      <!-- =======================/. Grafik Penjualan ========================= -->

      <div class="row">
        <!-- ======================= Trend Produk ========================= -->
        <div class="col-md-8">
          <div class="card">
            <div class="card-header mb-5">
              <h1 class="card-title">Trend Produk Per Bulan</h1><br>
              <hr>
              <table class="table table-bordered table-striped mt-3" id="table">
                <thead>
                  <tr>
                    <th>No</th>
                    <!-- <th width="120px">Item</th> -->
                    <th class="text-center">Nama</th>
                    <th width="70px">Jumlah</th>
                    
                  </tr>
                </thead>

                <tbody>
                  <?php $no = 1; ?>
                  <?php foreach ($laporan_data as $laporan) : ?>
                    <tr>
                      <td><?= $no++; ?></td>
                      <!-- <td><?= $laporan['itemfk']; ?></td> -->
                      <td><?= $laporan['nm']; ?></td>
                      <td><?= $laporan['Jml']; ?></td>
                    </tr>

                  <?php endforeach; ?>
                </tbody>
              </table>
              <div class="mt-3 text-center">
                <!-- <a href="download-pdf2.php" class="btn btn-danger" role="button" target="blank">Download PDF</a>
                <a href="download-excel2.php" class="btn btn-success" role="button">Download Excel</a> -->
              </div>

            </div>
          </div>
        </div>
        <!-- =======================/. Trend Produk ========================= -->

        <!-- =======================  Jumlah Item Perbulan ========================= -->
        <div class="col-md-4 mb-auto">
          <div class="card ">
            <div class="card-header mb-3">
              <h3 class="card-title mb-3">Gross Profit Perbulan</h3>
              <div class="info-box mb-3 bg-warning">
                <span class="info-box-icon"><i class="fas fa-tag"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Jumlah Item Yang Dijual</span>
                  <span class="info-box-number"><?php echo "Rp. " . number_format($jumlah_item, 0, ',', '.'); ?></span>
                </div>
              </div>
              <div class="info-box mb-3 bg-success">
                <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Omset Transaksi Perbulan</span>
                  <span class="info-box-number"><?php echo "Rp. " . number_format($jumlah_transaksi, 0, ',', '.'); ?></span>
                </div>
              </div>
              <div class="info-box mb-3 bg-danger">
                <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Profit Perbulan</span>
                  <span class="info-box-number"><?php echo "Rp. " . number_format($profit_penjualan, 0, ',', '.'); ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- =======================/. Jumlah Item Perbulan ========================= -->
      </div>
  </section>

  <?php include 'layout/footer.php' ?>
</div>