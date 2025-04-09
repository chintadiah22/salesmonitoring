<?php
include 'layout/header.php';
include 'config/function.php';

$data_outlet = query("SELECT * FROM tambah_outlet");

?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Data Outlet</h4>
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


    <div class="card">
        <section class="content">
            <div class="container-fluid">
                <div class="container mt-3">
                    <hr>
                    <table class="table table-bordered table-striped mb-3" id="table">
                        <thead>
                            <tr>
                                <th width="15px">No</th>
                                <th>Nama Cabang</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($data_outlet as $outlet) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $outlet['username']; ?></td>
                                    <td><?= $outlet['deskripsi']; ?></td>
                                    <td class="text-center">
                                        <a href="ubah-dataoutlet.php?id=<?= $outlet['id']; ?>" class="btn btn-success">Ubah</a>
                                        <a href="hapus-dataoutlet.php?id=<?= $outlet['id']; ?>" id="hapus" class="btn btn-danger" onclick="return confirm('Yakin Ingin Menghapus?')">Hapus</a>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <?php include 'layout/footer.php'; ?>
</div>