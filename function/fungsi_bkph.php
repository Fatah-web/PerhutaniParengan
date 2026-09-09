<?php
include 'koneksi.php';

session_start();
$aksi = $_GET['aksi'] ?? '';

/* ===================== SIMPAN ===================== */
if ($aksi == 'simpan') {

    $nama_bkph      = $_POST['nama_bkph'];
    $rph            = $_POST['rph'];
    $petak          = $_POST['petak'];
    $luas_baku      = $_POST['luas_baku'];
    $rencana_tanam  = $_POST['rencana_tanam'];
    $jenis_tanaman  = $_POST['jenis_tanaman'];
    $jarak_tanam    = $_POST['jarak_tanam'];
    $mandor_id      = $_POST['mandor_id'];

    $query = "INSERT INTO bkph 
        (nama_bkph, rph, petak, luas_baku, rencana_tanam, jenis_tanaman, jarak_tanam, mandor_id)
        VALUES
        ('$nama_bkph', '$rph', '$petak', '$luas_baku', '$rencana_tanam', '$jenis_tanaman', '$jarak_tanam', '$mandor_id')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['flash'] = [
            'icon'  => 'success',
            'title' => 'Simpan Berhasil',
            'text'  => 'Data BKPH berhasil disimpan!'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Simpan Gagal',
            'text'  => 'Gagal menyimpan data BKPH!'
        ];
    }

    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../admin/index.php?p=bkph'));
    exit();
}


/* ===================== EDIT ===================== */
elseif ($aksi == 'edit') {

    $bkph_id        = $_POST['bkph_id'];
    $nama_bkph      = $_POST['nama_bkph'];
    $rph            = $_POST['rph'];
    $petak          = $_POST['petak'];
    $luas_baku      = $_POST['luas_baku'];
    $rencana_tanam  = $_POST['rencana_tanam'];
    $jenis_tanaman  = $_POST['jenis_tanaman'];
    $jarak_tanam    = $_POST['jarak_tanam'];
    $mandor_id      = $_POST['mandor_id'];

    $query = "UPDATE bkph SET
                nama_bkph     = '$nama_bkph',
                rph           = '$rph',
                petak         = '$petak',
                luas_baku     = '$luas_baku',
                rencana_tanam = '$rencana_tanam',
                jenis_tanaman = '$jenis_tanaman',
                jarak_tanam   = '$jarak_tanam',
                mandor_id     = '$mandor_id'
              WHERE bkph_id = '$bkph_id'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['flash'] = [
            'icon'  => 'success',
            'title' => 'Edit Berhasil',
            'text'  => 'Data BKPH berhasil diperbarui!'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Edit Gagal',
            'text'  => 'Gagal mengedit data BKPH!'
        ];
    }

    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../admin/index.php?p=bkph'));
    exit();
}


/* ===================== HAPUS ===================== */
elseif ($aksi == 'hapus') {

    $bkph_id = $_GET['bkph_id'];

    if (mysqli_query($conn, "DELETE FROM bkph WHERE bkph_id = '$bkph_id'")) {
        $_SESSION['flash'] = [
            'icon'  => 'success',
            'title' => 'Hapus Berhasil',
            'text'  => 'Data BKPH berhasil dihapus!'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Hapus Gagal',
            'text'  => 'Gagal menghapus data BKPH!'
        ];
    }

    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../admin/index.php?p=bkph'));
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
