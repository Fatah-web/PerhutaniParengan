<?php
include 'koneksi.php';

session_start();
$aksi = $_GET['aksi'] ?? '';

/* ===================== SIMPAN ===================== */
if ($aksi == 'simpan') {

    $tahun_tanam   = $_POST['tahun_tanam'];
    $tenaga_kerja  = $_POST['tenaga_kerja'];
    $target_pohon  = $_POST['target_pohon'];
    $bkph_id       = $_POST['bkph_id'];

    $query = "INSERT INTO data_awal 
        (tahun_tanam, tenaga_kerja, target_pohon, bkph_id)
        VALUES
        ('$tahun_tanam','$tenaga_kerja', '$target_pohon', '$bkph_id')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['flash'] = [
            'icon'  => 'success',
            'title' => 'Simpan Berhasil',
            'text'  => 'Data Awal berhasil disimpan!'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Simpan Gagal',
            'text'  => 'Gagal menyimpan Data Awal!'
        ];
    }

    header("Location: " . ($_SERVER['HTTP_REFERER']));
    exit();
}


/* ===================== EDIT ===================== */
elseif ($aksi == 'edit') {

    $data_awal_id  = $_POST['data_awal_id'];
    $tahun_tanam   = $_POST['tahun_tanam'];
    $tenaga_kerja  = $_POST['tenaga_kerja'];
    $target_pohon  = $_POST['target_pohon'];
    $bkph_id       = $_POST['bkph_id'];

    $query = "UPDATE data_awal SET
                tahun_tanam  = '$tahun_tanam',
                tenaga_kerja = '$tenaga_kerja',
                target_pohon = '$target_pohon',
                bkph_id      = '$bkph_id'
              WHERE data_awal_id = '$data_awal_id'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['flash'] = [
            'icon'  => 'success',
            'title' => 'Edit Berhasil',
            'text'  => 'Data Awal berhasil diperbarui!'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Edit Gagal',
            'text'  => 'Gagal mengedit Data Awal!'
        ];
    }

    header("Location: " . ($_SERVER['HTTP_REFERER']));
    exit();
}


/* ===================== HAPUS ===================== */
elseif ($aksi == 'hapus') {

    $data_awal_id = $_GET['data_awal_id'];

    if (mysqli_query($conn, "DELETE FROM data_awal WHERE data_awal_id = '$data_awal_id'")) {
        $_SESSION['flash'] = [
            'icon'  => 'success',
            'title' => 'Hapus Berhasil',
            'text'  => 'Data Awal berhasil dihapus!'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Hapus Gagal',
            'text'  => 'Gagal menghapus Data Awal!'
        ];
    }

    header("Location: " . ($_SERVER['HTTP_REFERER']));
    exit();
}


/* ===================== AKSI TIDAK DIKENALI ===================== */
else {
    $_SESSION['flash'] = [
        'icon' => 'warning',
        'title' => 'Aksi Tidak Dikenali',
        'text' => 'Aksi yang dipilih tidak tersedia!'
    ];

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
