<?php
require 'config/function.php';
include 'layout/header.php';

$id_outlet = (int) $_GET['id'];

$outlet = query("SELECT * FROM tambah_outlet WHERE id = $id_outlet ")[0];


if (isset($_POST['ubah'])) {
    if (ubah_outlet($_POST) > 0) {
        echo "<script>
        alert('Data outlet berhasil diubah');
        document.location.href = 'tampilkan-dataoutlet.php'</script>";
    } else {
        echo "<script>
        alert('Data outlet gagal diubah');
        document.location.href = 'tampilkan-dataoutlet.php'</script>";
    }
}

?>



<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">

    <link rel="stylesheet" href="asset-penjualan/dist/css/login.css">
    <!------ Include the above in your HEAD tag ---------->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
</head>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Tambah Outlet</h4>
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
    <section class="content">
        <div class="container-fluid">
            <div class="container mt-3">
                <hr>
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $outlet['id']; ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nama Outlet</label>
                        <input type="text" name="username" value="<?= $outlet['username']; ?>" class="form-control" id="username" placeholder="Masukkan nama outlet anda" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Outlet</label>
                        <input name="deskripsi" value="<?= $outlet['deskripsi']; ?>" id="deskripsi" class="form-control" placeholder="Masukan deskripsi outlet anda"></input>
                        <small class="help-block">Deskripsi dapat berupa alamat atau keterangan lain.</small>
                    </div>
                    <div class="mb-3">
                        <label for="outlet-key" class="form-label">Outlet Key</label>
                        <input type="text" name="key" value="<?= $outlet['key']; ?>" class="form-control" id="outlet-key" readonly>
                        <small>Outlet key digenerate langsung oleh sistem dan tidak dapat anda ubah.</small>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" value="<?= $outlet['password']; ?>" class="form-control" id="password" placeholder="Masukkan password anda" required>
                        <small>Masukkan password lama atau baru</small>
                    </div>
                    <div class="custom-control custom-checkbox my-1 mr-sm-2">
                        <input type="checkbox" class="custom-control-input" id="customControlInline" onclick="lihatpassword()">
                        <label class="custom-control-label" for="customControlInline">Tampilkan Password</label>
                    </div>

                    <div class="mb-5">
                        <button type="submit" name="ubah" class="btn btn-danger mb-5" style="float: right;">Ubah</button>
                    </div>
                </form>
            </div>

        </div>
    </section>
    <!-- /.content -->
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

<?php include 'layout/footer.php'; ?>