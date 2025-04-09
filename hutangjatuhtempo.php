<?php
include 'layout/header.php';
include 'config/function.php';

$conn = mysqli_connect("localhost", "root", "", "salesmonitoring");

// Query untuk combobox Supplier
$query_supplier = "SELECT pk, nm FROM m_supplier ORDER BY nm";
$result_supplier = mysqli_query($conn, $query_supplier);

// Query untuk combobox Cabang
$query_cabang = "SELECT pk, nm FROM m_gudang ORDER BY nm";
$result_cabang = mysqli_query($conn, $query_cabang);

// Ambil filter dari form
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : '';
$supplier = isset($_GET['supplier']) ? $_GET['supplier'] : '';
$cabang = isset($_GET['cabang']) ? $_GET['cabang'] : '';

// Query pencarian Hutang Jatuh Tempo
$query = "SELECT t_beli_hd.notrs, t_beli_hd.gudangfk, t_beli_hd.tgl, t_beli_hd.tgljthtmp, 
                 t_beli_hd.supplierfk, m_supplier.nm AS nmsupplier, t_beli_hd.grandtotal, 
                 t_beli_hd.bayar, (t_beli_hd.grandtotal - t_beli_hd.bayar - t_beli_hd.deposit) AS hutang, 
                 IFNULL(SUM(t_bayarhutang_dt.bayar), 0) AS terbayar, 
                 ((t_beli_hd.grandtotal - t_beli_hd.bayar - t_beli_hd.jmlretur - t_beli_hd.deposit) - 
                 IFNULL(SUM(t_bayarhutang_dt.bayar), 0)) AS sisahutang, 
                 t_beli_hd.carabayar, t_beli_hd.jmlretur 
          FROM t_beli_hd
          LEFT JOIN t_bayarhutang_dt ON t_beli_hd.notrs = t_bayarhutang_dt.noref
          INNER JOIN m_supplier ON m_supplier.pk = t_beli_hd.supplierfk
          WHERE 1=1";

if (!empty($supplier)) {
    $query .= " AND t_beli_hd.supplierfk = '$supplier'";
}
if (!empty($cabang)) {
    $query .= " AND t_beli_hd.gudangfk = '$cabang'";
}
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $query .= " AND t_beli_hd.tgljthtmp BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$query .= " GROUP BY t_beli_hd.notrs, t_beli_hd.tgl, t_beli_hd.tgljthtmp, t_beli_hd.supplierfk, 
                   m_supplier.nm, t_beli_hd.grandtotal, t_beli_hd.bayar, t_beli_hd.carabayar
            HAVING t_beli_hd.carabayar = 2 AND sisahutang > 0";

$result = mysqli_query($conn, $query);
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h4 class="m-0">Hutang Jatuh Tempo</h4>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-primary"><i class="fas fa-file-alt"></i> Hutang Jatuh Tempo</h3>

                    <form method="GET">
                        <div class="mb-3">
                            <label>Tgl Jatuh Tempo:</label>
                            <div class="row">
                                <div class="col">
                                    <input type="date" class="form-control" name="tgl_awal" value="<?php echo $tgl_awal; ?>">
                                </div>
                                <div class="col">
                                    <input type="date" class="form-control" name="tgl_akhir" value="<?php echo $tgl_akhir; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Supplier:</label>
                            <select class="form-control" name="supplier">
                                <option value="">Pilih Supplier</option>
                                <?php while ($row = mysqli_fetch_assoc($result_supplier)) : ?>
                                    <option value="<?php echo $row['pk']; ?>" <?php echo ($supplier == $row['pk']) ? 'selected' : ''; ?>>
                                        <?php echo $row['nm']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Cabang:</label>
                            <select class="form-control" name="cabang">
                                <option value="">Pilih Cabang</option>
                                <?php while ($row = mysqli_fetch_assoc($result_cabang)) : ?>
                                    <option value="<?php echo $row['pk']; ?>" <?php echo ($cabang == $row['pk']) ? 'selected' : ''; ?>>
                                        <?php echo $row['nm']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </form>        

                    <br>
                    <a target="_blank" href="export_datahutang.php"> 
                        <button class="btn btn-success">Export to Excel</button>
                    </a>
                    <br>

                    <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Tgl Jth Tempo</th>
                                    <th class="text-end">Hutang</th>
                                    <th class="text-end">Terbayar</th>
                                    <th class="text-end">Sisa Hutang</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                $total_hutang = 0;
                                $total_terbayar = 0;
                                $total_sisa = 0;

                                while ($row = mysqli_fetch_assoc($result)) :
                                    $total_hutang += $row['hutang'];
                                    $total_terbayar += $row['terbayar'];
                                    $total_sisa += $row['sisahutang'];
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row['notrs']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tgl'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tgljthtmp'])); ?></td>
                                    <td class="text-end"><?php echo number_format($row['hutang'], 0, ',', '.'); ?></td>
                                    <td class="text-end"><?php echo number_format($row['terbayar'], 0, ',', '.'); ?></td>
                                    <td class="text-end"><?php echo number_format($row['sisahutang'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endwhile; ?>
                                
                                <tr class="fw-bold">
                                    <td colspan="4">TOTAL :</td>
                                    <td class="text-end"><?php echo number_format($total_hutang, 0, ',', '.'); ?></td>
                                    <td class="text-end"><?php echo number_format($total_terbayar, 0, ',', '.'); ?></td>
                                    <td class="text-end"><?php echo number_format($total_sisa, 0, ',', '.'); ?></td>
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
