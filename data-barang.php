<?php
include 'layout/header.php';
include 'config/function.php';

// Ambil data barang dengan pagination
$batas = 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$previous = $halaman - 1;
$next = $halaman + 1;

$queryTotal = "SELECT COUNT(*) as total FROM m_item WHERE aktif=1";
$resultTotal = mysqli_query($db, $queryTotal);
$totalRow = mysqli_fetch_assoc($resultTotal);
$total_halaman = ceil($totalRow['total'] / $batas);

$query = "SELECT m_item.pk, m_item.kd, m_item.nm, m_item.hargapokok, m_item.hargajl, tmp_stok.stok, 
                 m_satuan.nm AS satuan, m_gudang.nm AS cabang 
          FROM m_item
          INNER JOIN m_satuan ON m_item.satuanfk = m_satuan.pk
          LEFT JOIN tmp_stok ON m_item.pk = tmp_stok.itemfk
          LEFT JOIN m_gudang ON tmp_stok.gudangfk = m_gudang.pk
          WHERE aktif=1
          LIMIT $halaman_awal, $batas";

$data_barang = mysqli_query($db, $query);

// Tambah Data Barang
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $kd = $_POST['kd'];
    $nm = $_POST['nm'];
    $hargapokok = $_POST['hargapokok'];
    $hargajl = $_POST['hargajl'];
    $stok = $_POST['stok'];
    $satuan = $_POST['satuan'];
    $cabang = $_POST['cabang'];

    $insertQuery = "INSERT INTO m_item (kd, nm, hargapokok, hargajl, satuanfk, aktif) 
                    VALUES ('$kd', '$nm', '$hargapokok', '$hargajl', '$satuan', 1)";

    if (mysqli_query($db, $insertQuery)) {
        echo "<script>alert('Data berhasil ditambahkan'); window.location='data_barang.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data');</script>";
    }
}

// Update Data Barang
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $kd = $_POST['kd'];
    $nm = $_POST['nm'];
    $hargapokok = $_POST['hargapokok'];
    $hargajl = $_POST['hargajl'];
    $stok = $_POST['stok'];

    $updateQuery = "UPDATE m_item SET 
                    kd='$kd', nm='$nm', hargapokok='$hargapokok', hargajl='$hargajl' 
                    WHERE pk=$id";

    if (mysqli_query($db, $updateQuery)) {
        echo "<script>alert('Data berhasil diperbarui'); window.location='data_barang.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data');</script>";
    }
}

// Hapus Data Barang
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $deleteQuery = "DELETE FROM m_item WHERE pk = $id";

    if (mysqli_query($db, $deleteQuery)) {
        echo "<script>alert('Data berhasil dihapus'); window.location='data_barang.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data');</script>";
    }
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h4 class="m-0">Data Barang</h4>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-success" onclick="openTambahModal()">Tambah Data</button>
                    <hr>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>HPP</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                                <th>Cabang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = $halaman_awal + 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($data_barang)) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $row['kd']; ?></td>
                                    <td><?= $row['nm']; ?></td>
                                    <td><?= number_format($row['hargapokok'], 0, ',', '.'); ?></td>
                                    <td><?= number_format($row['hargajl'], 0, ',', '.'); ?></td>
                                    <td><?= $row['stok']; ?></td>
                                    <td><?= $row['satuan']; ?></td>
                                    <td><?= $row['cabang']; ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" onclick="openEditModal(<?= $row['pk']; ?>)">Edit</button>
                                        <a href="?hapus=<?= $row['pk']; ?>" onclick="return confirm('Yakin ingin menghapus?');" class="btn btn-danger btn-sm">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <nav>
                        <ul class="pagination justify-content-center">
                            <li class="page-item">
                                <a class="page-link" href="?halaman=<?= max(1, $previous); ?>">Previous</a>
                            </li>
                            <?php for ($x = 1; $x <= $total_halaman; $x++) : ?>
                                <li class="page-item"><a class="page-link" href="?halaman=<?= $x; ?>"><?= $x; ?></a></li>
                            <?php endfor; ?>
                            <li class="page-item">
                                <a class="page-link" href="?halaman=<?= min($next, $total_halaman); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Tambah -->
<div id="tambahModal" class="modal">
    <form method="POST">
        <h4>Tambah Data Barang</h4>
        <label>Kode</label>
        <input type="text" name="kd" class="form-control">
        <label>Nama</label>
        <input type="text" name="nm" class="form-control">
        <label>HPP</label>
        <input type="number" name="hargapokok" class="form-control">
        <label>Harga Jual</label>
        <input type="number" name="hargajl" class="form-control">
        <br>
        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-secondary" onclick="closeTambahModal()">Batal</button>
    </form>
</div>

<script>
    function openTambahModal() { document.getElementById('tambahModal').style.display = 'block'; }
    function closeTambahModal() { document.getElementById('tambahModal').style.display = 'none'; }
</script>

<?php include 'layout/footer.php'; ?>
