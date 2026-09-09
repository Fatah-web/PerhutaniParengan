<?php
include 'koneksi.php';
include 'upload_tanam.php';

session_start();
$aksi = $_GET['aksi'] ?? '';

/* ===========================
   SIMPAN DATA PENANAMAN
=========================== */
if ($aksi == 'simpan') {

    $jumlah_tanam = $_POST['jumlah_tanam'];
    $sumber_bibit = $_POST['sumber_bibit'];
    $tanggal      = $_POST['tanggal'];
    $catatan      = $_POST['catatan'];
    $ajir_id      = $_POST['ajir_id'];

    // Cek apakah Ajir sudah memiliki data penanaman
    $cek = mysqli_query($conn, "
        SELECT * FROM penanaman
        WHERE ajir_id = '$ajir_id'
    ");

    if (mysqli_num_rows($cek) > 0) {

        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Gagal',
            'text'  => 'Data penanaman untuk ajir tersebut sudah ada!'
        ];

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $foto = upload();

    $query = "INSERT INTO penanaman
                (jumlah_tanam, foto_tanam, sumber_bibit, tanggal, catatan, ajir_id)
              VALUES
                ('$jumlah_tanam', '$foto', '$sumber_bibit', '$tanggal', '$catatan', '$ajir_id')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['flash'] = [
            'icon'  => 'success',
            'title' => 'Berhasil',
            'text'  => 'Data penanaman berhasil disimpan'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Gagal',
            'text'  => 'Data penanaman gagal disimpan'
        ];
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ===========================
   EDIT DATA PENANAMAN
=========================== */
elseif ($aksi == 'edit') {

    $tanam_id     = $_POST['tanam_id'];
    $jumlah_tanam = $_POST['jumlah_tanam'];
    $sumber_bibit = $_POST['sumber_bibit'];
    $tanggal      = $_POST['tanggal'];
    $catatan      = $_POST['catatan'];
    $ajir_id      = $_POST['ajir_id'];
    $fotolama     = $_POST['fotolama'];
    $status       = $_POST['status'];

    // Cek apakah Ajir sudah digunakan oleh data lain
    $cek = mysqli_query($conn, "
        SELECT * FROM penanaman
        WHERE ajir_id = '$ajir_id'
        AND tanam_id != '$tanam_id'
    ");

    if (mysqli_num_rows($cek) > 0) {

        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Gagal',
            'text'  => 'Data penanaman untuk ajir tersebut sudah ada!'
        ];

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Cek upload foto baru
    if ($_FILES['foto']['error'] === 4) {
        $foto_tanam = $fotolama;
    } else {
        if ($fotolama && file_exists('../gambar_tanam/' . $fotolama)) {
            unlink('../gambar_tanam/' . $fotolama);
        }
        $foto_tanam = upload();
    }

    $query = "UPDATE penanaman SET
                jumlah_tanam = '$jumlah_tanam',
                foto_tanam   = '$foto_tanam',
                sumber_bibit = '$sumber_bibit',
                tanggal      = '$tanggal',
                catatan      = '$catatan',
                status       = '$status',
                ajir_id      = '$ajir_id'
              WHERE tanam_id = '$tanam_id'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['flash'] = [
            'icon'  => 'success',
            'title' => 'Berhasil',
            'text'  => 'Data penanaman berhasil diedit'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Gagal',
            'text'  => 'Data penanaman gagal diedit'
        ];
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ===========================
   HAPUS DATA PENANAMAN
=========================== */
elseif ($aksi == 'hapus') {

    $tanam_id = $_GET['tanam_id'];

    // Ambil foto
    $q = mysqli_query($conn, "SELECT foto_tanam FROM penanaman WHERE tanam_id='$tanam_id'");
    $data = mysqli_fetch_assoc($q);

    $foto = $data['foto_tanam'];

    if ($foto && file_exists('../gambar_tanam/' . $foto)) {
        unlink('../gambar_tanam/' . $foto);
    }

    if (mysqli_query($conn, "DELETE FROM penanaman WHERE tanam_id='$tanam_id'")) {
        $_SESSION['flash'] = [
            'icon'  => 'success',
            'title' => 'Berhasil',
            'text'  => 'Data Penanaman berhasil dihapus'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon'  => 'error',
            'title' => 'Gagal',
            'text'  => 'Data Penanaman gagal hapus'
        ];
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ============================================================
   VERIFIKASI PENANAMAN
============================================================ */
elseif ($aksi == 'verifikasi') {

    $tanam_id = $_GET['tanam_id'];

    mysqli_query($conn, "
        UPDATE penanaman
        SET status = 'verified'
        WHERE tanam_id = '$tanam_id'
    ");

    $_SESSION['flash'] = [
        'icon'  => 'success',
        'title' => 'Verifikasi Berhasil',
        'text'  => 'Penanaman berhasil diverifikasi!'
    ];

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ============================================================
   REJECT PENANAMAN
============================================================ */
elseif ($aksi == 'reject') {

    $tanam_id      = $_POST['tanam_id'];
    $alasan_reject = $_POST['alasan_reject'];

    mysqli_query($conn, "
        UPDATE penanaman
        SET status = 'rejected',
            catatan = CONCAT(catatan, '\nREJECT: $alasan_reject')
        WHERE tanam_id = '$tanam_id'
    ");

    $_SESSION['flash'] = [
        'icon'  => 'error',
        'title' => 'Penanaman Ditolak',
        'text'  => 'Data penanaman berhasil direject!'
    ];

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ============================================================
   DEFAULT
============================================================ */
else {
    $_SESSION['flash'] = [
        'icon' => 'warning',
        'title' => 'Aksi Tidak Dikenali',
        'text' => 'Aksi yang dipilih tidak tersedia!'
    ];

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();}