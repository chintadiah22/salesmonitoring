<<?php
    include 'config/function.php';

    $id_outlet = (int)$_GET['id'];

    if (hapus_outlet($id_outlet) > 0) {
        echo "<script>    
    document.location.href = 'tampilkan-dataoutlet.php';
    </script>";
    } else {
        echo "<script>
    alert('Data Barang Gagal Dihapus');
    document.location.href = 'tampilkan-dataoutlet.php';
    </script>";
    }

    ?>