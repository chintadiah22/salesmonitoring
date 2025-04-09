<?php
include 'layout/header.php';
include 'config/function.php';

$conn = mysqli_connect("localhost", "root", "", "salesmonitoring");

// Query untuk combobox Kategori
$query_kategori = "SELECT pk, nm FROM m_kategori ORDER BY nm";
$result_kategori = mysqli_query($conn, $query_kategori);

// Query untuk combobox Cabang
$query_cabang = "SELECT pk, nm FROM m_gudang ORDER BY nm";
$result_cabang = mysqli_query($conn, $query_cabang);

// Ambil filter dari form, jika kosong maka default ke hari ini
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-d');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');
$kategori = isset($_GET['m_kategori']) ? $_GET['m_kategori'] : '';
$cabang = isset($_GET['cabang']) ? $_GET['cabang'] : '';

// Query pencarian data
$query = "SELECT nmkategori, SUM(jml) AS jml, SUM(total) AS total, SUM(totalbeli) AS totalbeli 
          FROM qry_jual_peritem WHERE 1=1";

if (!empty($kategori)) {
    $query .= " AND kategori_id = '" . mysqli_real_escape_string($conn, $kategori) . "'";
}
if (!empty($cabang)) {
    $query .= " AND cabang_id = '" . mysqli_real_escape_string($conn, $cabang) . "'";
}
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $query .= " AND tgl_penjualan BETWEEN '" . mysqli_real_escape_string($conn, $tgl_awal) . "' 
                AND '" . mysqli_real_escape_string($conn, $tgl_akhir) . "'";
}

$query .= " GROUP BY nmkategori";
$result = mysqli_query($conn, $query);
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h4 class="m-0 text-primary"><i class="fas fa-file-alt"></i> Laporan Penjualan Per Kategori</h4>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form method="GET">
                        <div class="row">
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

                            <div class="col-md-3">
                                <label>Kategori:</label>
                                <select class="form-control" name="m_kategori">
                                    <option value="">Pilih Kategori</option>
                                    <?php while ($row = mysqli_fetch_assoc($result_kategori)) : ?>
                                        <option value="<?= $row['pk']; ?>" <?= ($kategori == $row['pk']) ? 'selected' : ''; ?>>
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
                     <a target="_blank" href="export_datakategori.php">
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
                                    <th style="text-align: right;">Kategori</th>
                                    <th style="text-align: right;">Jumlah Qty</th>
                                    <th style="text-align: right;">Total</th>
                                    <th style="text-align: right;">Total Beli</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                $total_jumlah = 0;
                                $total_total = 0;
                                $total_beli = 0;
                                while ($row = mysqli_fetch_assoc($result)) :
                                    $total_jumlah += $row['jml'];
                                    $total_total += $row['total'];
                                    $total_beli += $row['totalbeli'];
                                ?>
                                <tr>
                                    <td style="text-align: right;"><?= $no++; ?></td>
                                    <td style="text-align: right;"><?= $row['nmkategori']; ?></td>
                                    <td style="text-align: right;"><?= number_format($row['jml'], 0, ',', '.'); ?></td>
                                    <td style="text-align: right;"><?= number_format($row['total'], 0, ',', '.'); ?></td>
                                    <td style="text-align: right;"><?= number_format($row['totalbeli'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endwhile; ?>
                                <tr class="fw-bold bg-light">
                                    <td colspan="2" style="text-align: right;">TOTAL:</td>
                                    <td style="text-align: right;"><?= number_format($total_jumlah, 0, ',', '.'); ?></td>
                                    <td style="text-align: right;"><?= number_format($total_total, 0, ',', '.'); ?></td>
                                    <td style="text-align: right;"><?= number_format($total_beli, 0, ',', '.'); ?></td>
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
