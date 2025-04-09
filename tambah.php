<?php include 'layout/header.php';
include 'config/function.php';

if (isset($_POST['tambah'])) {
    if (tambah_outlet($_POST) > 0) {
        echo "<script>
        alert('Data outlet berhasil ditambah');
        document.location.href = 'tampilkan-dataoutlet.php'</script>";
    } else {
        echo "<script>
        alert('Data outlet gagal ditambah');
        document.location.href = 'tampilkan-dataoutlet.php'</script>";
    }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Tambah Data Outlet</h4>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard </li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="card">
        <section class="content">
            <div class="container-fluid">
                <div class="container mt-3">
                    <hr>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nama Outlet</label>
                            <input type="text" name="username" class="form-control" id="username" placeholder="Masukkan nama outlet anda" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Outlet</label>
                            <input name="deskripsi" id="deskripsi" class="form-control" placeholder="Masukan deskripsi outlet anda"></input>
                            <small class="help-block">Deskripsi dapat berupa alamat atau keterangan lain.</small>
                        </div>
                        <div class="mb-3">
                            <label for="outlet-key" class="form-label">Outlet Key</label>
                            <input type="text" name="key" class="form-control" id="outlet-key" value="<?= $kodeauto; ?>" readonly>
                            <small>Outlet key digenerate langsung oleh sistem dan tidak dapat anda ubah.</small>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan password anda" required>
                            <small>Password untuk settingan di program desktop masing-masing outlet</small>
                        </div>
                        <div class="custom-control custom-checkbox my-1 mr-sm-2">
                            <input type="checkbox" class="custom-control-input" id="customControlInline" onclick="lihatpassword()">
                            <label class="custom-control-label" for="customControlInline">Tampilkan Password</label>
                        </div>

                        <div class="mb-5">
                            <button type="reset" name="reset" class="btn btn-danger mb-5 ml-1" style="float: right;">Reset</button>
                            <button type="submit" name="tambah" class="btn btn-danger mb-5" style="float: right;">Tambah</button>
                        </div>
                    </form>
                </div>

            </div>
        </section>
    </div>
    <!-- /.content -->
    <?php include 'layout/footer.php'; ?>
</div>

<!-- ==== Lihat Password ===== -->
<script>
    function lihatpassword() {
        var pass = document.getElementById("password");
        if (pass.type == "password") {
            pass.type = "text";
        } else {
            pass.type = "password";
        }
    }
</script>