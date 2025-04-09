<?php
include 'layout/header.php';
include 'config/function.php';

// Ambil data konsumen
$data_konsumen = query("SELECT pk, kd, nm, alamat, tlp, jmlvoucher FROM m_nasabah WHERE aktif='Aktif'");

// Tambah Data Konsumen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $kd = $_POST['kd'];
    $nm = $_POST['nm'];
    $alamat = $_POST['alamat'];
    $tlp = $_POST['tlp'];
    $jmlvoucher = $_POST['jmlvoucher'];

    $insertQuery = "INSERT INTO m_nasabah (kd, nm, alamat, tlp, jmlvoucher, aktif) 
                    VALUES ('$kd', '$nm', '$alamat', '$tlp', '$jmlvoucher', 'Aktif')";

    if (mysqli_query($db, $insertQuery)) {
        echo "<script>alert('Data berhasil ditambahkan'); window.location='data_konsumen.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data');</script>";
    }
}

// Update Data Konsumen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $kd = $_POST['kd'];
    $nm = $_POST['nm'];
    $alamat = $_POST['alamat'];
    $tlp = $_POST['tlp'];
    $jmlvoucher = $_POST['jmlvoucher'];

    $updateQuery = "UPDATE m_nasabah SET 
                    kd='$kd', nm='$nm', alamat='$alamat', tlp='$tlp', jmlvoucher='$jmlvoucher'
                    WHERE pk=$id";

    if (mysqli_query($db, $updateQuery)) {
        echo "<script>alert('Data berhasil diperbarui'); window.location='data_konsumen.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data');</script>";
    }
}

// Hapus Data Konsumen
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $deleteQuery = "DELETE FROM m_nasabah WHERE pk = $id";

    if (mysqli_query($db, $deleteQuery)) {
        echo "<script>alert('Data berhasil dihapus'); window.location='data_konsumen.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data');</script>";
    }
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h4 class="m-0">Data Konsumen</h4>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-success" onclick="openTambahModal()">Tambah Data</button>
                    <a target="_blank" href="export_datakonsumen.php" class="btn btn-primary">Export ke Excel</a>
                    <hr>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Tlp</th>
                                <th>Poin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($data_konsumen as $konsumen) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $konsumen['kd']; ?></td>
                                    <td><?= $konsumen['nm']; ?></td>
                                    <td><?= $konsumen['alamat']; ?></td>
                                    <td><?= $konsumen['tlp']; ?></td>
                                    <td><?= $konsumen['jmlvoucher']; ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" onclick="openEditModal(<?= $konsumen['pk']; ?>, '<?= $konsumen['kd']; ?>', '<?= $konsumen['nm']; ?>', '<?= $konsumen['alamat']; ?>', '<?= $konsumen['tlp']; ?>', '<?= $konsumen['jmlvoucher']; ?>')">Edit</button>
                                        <a href="?hapus=<?= $konsumen['pk']; ?>" onclick="return confirm('Yakin ingin menghapus?');" class="btn btn-danger btn-sm">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Tambah -->
<div id="tambahModal" class="modal">
    <form method="POST">
        <h4>Tambah Data Konsumen</h4>
        <label>ID</label>
        <input type="text" name="kd" class="form-control">
        <label>Nama</label>
        <input type="text" name="nm" class="form-control">
        <label>Alamat</label>
        <input type="text" name="alamat" class="form-control">
        <label>Telepon</label>
        <input type="text" name="tlp" class="form-control">
        <label>Poin</label>
        <input type="number" name="jmlvoucher" class="form-control">
        <br>
        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-secondary" onclick="closeTambahModal()">Batal</button>
    </form>
</div>

<!-- Modal Edit -->
<div id="editModal" class="modal">
    <form method="POST">
        <h4>Edit Data Konsumen</h4>
        <input type="hidden" name="id" id="edit_id">
        <label>ID</label>
        <input type="text" name="kd" id="edit_kd" class="form-control">
        <label>Nama</label>
        <input type="text" name="nm" id="edit_nm" class="form-control">
        <label>Alamat</label>
        <input type="text" name="alamat" id="edit_alamat" class="form-control">
        <label>Telepon</label>
        <input type="text" name="tlp" id="edit_tlp" class="form-control">
        <label>Poin</label>
        <input type="number" name="jmlvoucher" id="edit_jmlvoucher" class="form-control">
        <br>
        <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
    </form>
</div>

<script>
    function openTambahModal() { document.getElementById('tambahModal').style.display = 'block'; }
    function closeTambahModal() { document.getElementById('tambahModal').style.display = 'none'; }

    function openEditModal(id, kd, nm, alamat, tlp, jmlvoucher) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_kd').value = kd;
        document.getElementById('edit_nm').value = nm;
        document.getElementById('edit_alamat').value = alamat;
        document.getElementById('edit_tlp').value = tlp;
        document.getElementById('edit_jmlvoucher').value = jmlvoucher;
        document.getElementById('editModal').style.display = 'block';
    }
    
    function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }
</script>

<?php include 'layout/footer.php'; ?>
