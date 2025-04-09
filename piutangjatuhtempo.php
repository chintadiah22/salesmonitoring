<?php
include 'layout/header.php';
include 'config/function.php';

$conn = mysqli_connect("localhost", "root", "", "salesmonitoring");

$query_konsumen = "SELECT pk, nm FROM m_nasabah ORDER BY nm";
$result_konsumen = mysqli_query($conn, $query_konsumen);

$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : '';
$konsumen = isset($_GET['konsumen']) ? $_GET['konsumen'] : '';

$query = "SELECT t_jual_hd.notrs, t_jual_hd.tgl, t_jual_hd.tgljthtmp, t_jual_hd.konsumenfk, 
                 m_nasabah.nm AS nmkonsumen, t_jual_hd.grandtotal, t_jual_hd.bayar, 
                 (t_jual_hd.grandtotal - t_jual_hd.bayar) AS piutang, 
                 IFNULL(SUM(t_bayarpiutang_dt.bayar), 0) AS terbayar, 
                 ((t_jual_hd.grandtotal - t_jual_hd.bayar) - IFNULL(SUM(t_bayarpiutang_dt.bayar), 0)) AS sisapiutang, 
                 t_jual_hd.carabayar
          FROM t_jual_hd
          LEFT JOIN t_bayarpiutang_dt ON t_jual_hd.notrs = t_bayarpiutang_dt.noref
          INNER JOIN m_nasabah ON m_nasabah.pk = t_jual_hd.konsumenfk
          WHERE 1=1";

if (!empty($konsumen)) {
    $query .= " AND t_jual_hd.konsumenfk = '$konsumen'";
}
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $query .= " AND t_jual_hd.tgljthtmp BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$query .= " GROUP BY t_jual_hd.notrs, t_jual_hd.tgl, t_jual_hd.tgljthtmp, t_jual_hd.konsumenfk, 
                   m_nasabah.nm, t_jual_hd.grandtotal, t_jual_hd.bayar, t_jual_hd.carabayar
            HAVING t_jual_hd.carabayar = 2 AND sisapiutang > 0";

$result = mysqli_query($conn, $query);
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h4 class="m-0">Piutang Jatuh Tempo</h4>
        </div>
    </div>
    
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-primary"><i class="fas fa-file-alt"></i> Piutang Jatuh Tempo</h3>

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
                            <label>Konsumen:</label>
                            <select class="form-control" name="konsumen">
                                <option value="">Pilih Konsumen</option>
                                <?php while ($row = mysqli_fetch_assoc($result_konsumen)) : ?>
                                    <option value="<?php echo $row['pk']; ?>" <?php echo ($konsumen == $row['pk']) ? 'selected' : ''; ?>>
                                        <?php echo $row['nm']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </form>        

                    <br>
                    <a target="_blank" href="export_dataPiutang.php"> 
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
                                    <th class="text-end">Piutang</th>
                                    <th class="text-end">Terbayar</th>
                                    <th class="text-end">Sisa Piutang</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                $total_piutang = 0;
                                $total_terbayar = 0;
                                $total_sisa = 0;

                                while ($row = mysqli_fetch_assoc($result)) :
                                    $total_piutang += $row['piutang'];
                                    $total_terbayar += $row['terbayar'];
                                    $total_sisa += $row['sisapiutang'];
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row['notrs']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tgl'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tgljthtmp'])); ?></td>
                                    <td class="text-end"><?php echo number_format($row['piutang'], 0, ',', '.'); ?></td>
                                    <td class="text-end"><?php echo number_format($row['terbayar'], 0, ',', '.'); ?></td>
                                    <td class="text-end"><?php echo number_format($row['sisapiutang'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endwhile; ?>
                                
                                <tr class="fw-bold">
                                    <td colspan="4">TOTAL :</td>
                                    <td class="text-end"><?php echo number_format($total_piutang, 0, ',', '.'); ?></td>
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
