<?php
include 'koneksi.php';
include 'upload_ajir.php';

session_start();
$aksi = $_GET['aksi'] ?? '';

/* ===========================
   SIMPAN DATA AJIR
=========================== */
if ($aksi == 'simpan') {

    $jumlah_ajir = $_POST['jumlah_ajir'];
    $tanggal     = $_POST['tanggal'];
    $catatan     = $_POST['catatan'];
    $lubang_id   = $_POST['lubang_id'];

    // Cek apakah lubang sudah memiliki data ajir
    $cek = mysqli_query($conn, "SELECT * FROM ajir WHERE lubang_id = '$lubang_id'");

    if (mysqli_num_rows($cek) > 0) {

        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Gagal',
            'text'  => 'Data ajir untuk lubang tersebut sudah ada.'
        ];

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $foto = upload();

    $query = "INSERT INTO ajir
                (jumlah_ajir, foto_ajir, tanggal, catatan, lubang_id)
              VALUES
                ('$jumlah_ajir', '$foto', '$tanggal', '$catatan', '$lubang_id')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['flash'] = [
            'icon'  => 'success',
            'title' => 'Berhasil',
            'text'  => 'Data ajir berhasil disimpan'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Gagal',
            'text'  => 'Data ajir gagal disimpan'
        ];
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ===========================
   EDIT DATA AJIR
=========================== */
elseif ($aksi == 'edit') {

    $ajir_id      = $_POST['ajir_id'];
    $jumlah_ajir  = $_POST['jumlah_ajir'];
    $tanggal      = $_POST['tanggal'];
    $catatan      = $_POST['catatan'];
    $lubang_id    = $_POST['lubang_id'];
    $fotolama     = $_POST['fotolama'];
    $status       = $_POST['status'];

    // Cek apakah lubang sudah digunakan oleh data lain
    $cek = mysqli_query($conn, "
        SELECT * FROM ajir
        WHERE lubang_id = '$lubang_id'
        AND ajir_id != '$ajir_id'
    ");

    if (mysqli_num_rows($cek) > 0) {

        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Gagal',
            'text'  => 'Data ajir untuk lubang tersebut sudah ada.'
        ];

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Cek upload foto baru
    if ($_FILES['foto']['error'] === 4) {
        $foto_ajir = $fotolama;
    } else {
        if ($fotolama && file_exists('../gambar_ajir/' . $fotolama)) {
            unlink('../gambar_ajir/' . $fotolama);
        }
        $foto_ajir = upload();
    }

    $query = "UPDATE ajir SET
                jumlah_ajir = '$jumlah_ajir',
                foto_ajir   = '$foto_ajir',
                tanggal     = '$tanggal',
                catatan     = '$catatan',
                status      = '$status',
                lubang_id   = '$lubang_id'
              WHERE ajir_id = '$ajir_id'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['flash'] = [
            'icon'  => 'success',
            'title' => 'Berhasil',
            'text'  => 'Data ajir berhasil diedit'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Gagal',
            'text'  => 'Data ajir gagal diedit'
        ];
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ===========================
   HAPUS DATA AJIR
=========================== */
elseif ($aksi == 'hapus') {

    $ajir_id = $_GET['ajir_id'];

    // Ambil foto
    $q = mysqli_query($conn, "SELECT foto_ajir FROM ajir WHERE ajir_id='$ajir_id'");
    $data = mysqli_fetch_assoc($q);

    $foto = $data['foto_ajir'];

    if ($foto && file_exists('../gambar_ajir/' . $foto)) {
        unlink('../gambar_ajir/' . $foto);
    }

    if (mysqli_query($conn, "DELETE FROM ajir WHERE ajir_id='$ajir_id'")) {
        $_SESSION['flash'] = [
            'icon'  => 'success',
            'title' => 'Berhasil',
            'text'  => 'Data Ajir berhasil dihapus'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Gagal',
            'text'  => 'Data Ajir gagal dihapus'
        ];
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ============================================================
   VERIFIKASI AJIR
============================================================ */
elseif ($aksi == 'verifikasi') {

    $ajir_id = $_GET['ajir_id'];

    mysqli_query($conn, "
        UPDATE ajir
        SET status = 'verified'
        WHERE ajir_id = '$ajir_id'
    ");

    $_SESSION['flash'] = [
        'icon'  => 'success',
        'title' => 'Verifikasi Berhasil',
        'text'  => 'Ajir berhasil diverifikasi!'
    ];

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ============================================================
   REJECT AJIR
============================================================ */
elseif ($aksi == 'reject') {

    $ajir_id       = $_POST['ajir_id'];
    $alasan_reject = $_POST['alasan_reject'];

    mysqli_query($conn, "
        UPDATE ajir SET
            status = 'rejected',
            catatan = CONCAT(catatan, '\nREJECT: $alasan_reject')
        WHERE ajir_id = '$ajir_id'
    ");

    $_SESSION['flash'] = [
        'icon'  => 'error',
        'title' => 'Ajir Ditolak',
        'text'  => 'Data ajir berhasil direject!'
    ];

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ============================================================
   DEFAULT
============================================================ */
else {
    echo "Aksi tidak dikenali.";
}