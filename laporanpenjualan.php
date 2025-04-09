<?php
include 'layout/header.php';
include 'config/function.php';

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "salesmonitoring");

// Query untuk combobox Kasir
$query_kasir = "SELECT m_user.pk, m_user.nm FROM m_user WHERE aktif=1 ORDER BY m_user.nm";
$result_kasir = mysqli_query($conn, $query_kasir);

// Query untuk combobox Cabang
$query_cabang = "SELECT m_gudang.pk, m_gudang.nm FROM m_gudang ORDER BY m_gudang.nm";
$result_cabang = mysqli_query($conn, $query_cabang);

// Ambil filter dari form
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-d');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');
$kasir = isset($_GET['kasir']) ? $_GET['kasir'] : '';
$cabang = isset($_GET['cabang']) ? $_GET['cabang'] : '';

// Query utama
$query = "SELECT t_jual_hd.notrs, t_jual_hd.tgl, t_jual_hd.grandtotal, t_jual_hd.voucher, 
          t_jual_hd.kartu, t_jual_hd.sisakurang, m_user.nm AS useradd,
          IF(t_jual_hd.tunai > t_jual_hd.grandtotal - t_jual_hd.voucher, 
          t_jual_hd.grandtotal - t_jual_hd.voucher, t_jual_hd.tunai) AS jmltunai 
          FROM t_jual_hd
          LEFT JOIN m_user ON m_user.pk = t_jual_hd.addedbyfk
          LEFT JOIN m_gudang ON m_gudang.pk = t_jual_hd.gudangfk
          WHERE 1=1";

// Filter berdasarkan inputan
if (!empty($kasir)) {
    $query .= " AND m_user.pk = '$kasir'";
}
if (!empty($cabang)) {
    $query .= " AND m_gudang.pk = '$cabang'";
}
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $query .= " AND t_jual_hd.tgl BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$result = mysqli_query($conn, $query);

// Variabel untuk menghitung total
$total_grandtotal = 0;
$total_voucher = 0;
$total_tunai = 0;
$total_nontunai = 0;
$total_piutang = 0;
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h4 class="m-0 text-primary"><i class="fas fa-file-alt"></i> Laporan Penjualan Per Kasir</h4>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Kasir:</label>
                                <select class="form-control" name="kasir">
                                    <option value="">Pilih Kasir</option>
                                    <?php while ($row = mysqli_fetch_assoc($result_kasir)) : ?>
                                        <option value="<?= $row['pk']; ?>" <?= ($kasir == $row['pk']) ? 'selected' : ''; ?>>
                                            <?= $row['nm']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Cabang:</label>
                                <select class="form-control" name="cabang">
                                    <option value="">Pilih Cabang</option>
                                    <?php while ($row = mysqli_fetch_assoc($result_cabang)) : ?>
                                        <option value="<?= $row['pk']; ?>" <?= ($cabang == $row['pk']) ? 'selected' : ''; ?>>
                                            <?= $row['nm']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Tanggal Awal:</label>
                                <input type="date" class="form-control" name="tgl_awal" value="<?= $tgl_awal; ?>">
                            </div>

                            <div class="col-md-2">
                                <label>Tanggal Akhir:</label>
                                <input type="date" class="form-control" name="tgl_akhir" value="<?= $tgl_akhir; ?>">
                            </div>

                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Cari</button>
                            </div>
                        </div>
                    </form>

                    <br>
                     <a target="_blank" href="export_laporanpenjualan.php">
                        <button class="btn btn-success">Export to Excel</button>
                     </a>

                    <br>
                    <br>

                    <!-- Tampilkan Data -->
                    <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                        <table class="table table-bordered mt-3">
                            <thead class="bg-primary text-white text-right">
                                <tr>
                                    <th style="text-align: right;">No.</th>
                                    <th style="text-align: right;">No Jual</th>
                                    <th style="text-align: right;">Tanggal</th>
                                    <th style="text-align: right;">Kasir</th>
                                    <th style="text-align: right;">Grand Total</th>
                                    <th style="text-align: right;">Voucher</th>
                                    <th style="text-align: right;">Tunai</th>
                                    <th style="text-align: right;">Non Tunai</th>
                                    <th style="text-align: right;">Piutang</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)) :
                                    $total_grandtotal += $row['grandtotal'];
                                    $total_voucher += $row['voucher'];
                                    $total_tunai += $row['jmltunai'];
                                    $total_nontunai += $row['kartu'];
                                    $total_piutang += $row['sisakurang'];
                                ?>
                                <tr>
                                    <td style="text-align: right;"><?= $no++; ?></td>
                                    <td style="text-align: right;"><?= $row['notrs']; ?></td>
                                    <td style="text-align: right;"><?= date('d/m/Y', strtotime($row['tgl'])); ?></td>
                                    <td style="text-align: right;"><?= $row['useradd']; ?></td>
                                    <td style="text-align: right;"><?= number_format($row['grandtotal'], 0, ',', '.'); ?></td>
                                    <td style="text-align: right;"><?= number_format($row['voucher'], 0, ',', '.'); ?></td>
                                    <td style="text-align: right;"><?= number_format($row['jmltunai'], 0, ',', '.'); ?></td>
                                    <td style="text-align: right;"><?= number_format($row['kartu'], 0, ',', '.'); ?></td>
                                    <td style="text-align: right;"><?= number_format($row['sisakurang'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endwhile; ?>
                                <tr class="fw-bold bg-light">
                                    <td colspan="4" style="text-align: right;">TOTAL:</td>
                                    <td style="text-align: right;"><?= number_format($total_grandtotal, 0, ',', '.'); ?></td>
                                    <td style="text-align: right;"><?= number_format($total_voucher, 0, ',', '.'); ?></td>
                                    <td style="text-align: right;"><?= number_format($total_tunai, 0, ',', '.'); ?></td>
                                    <td style="text-align: right;"><?= number_format($total_nontunai, 0, ',', '.'); ?></td>
                                    <td style="text-align: right;"><?= number_format($total_piutang, 0, ',', '.'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <div class="alert alert-warning mt-3">Tidak ada data ditemukan.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'layout/footer.php';
mysqli_close($conn);
?>
