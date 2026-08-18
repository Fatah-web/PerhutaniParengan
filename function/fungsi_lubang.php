<?php
include 'koneksi.php';
include 'upload_lubang.php';

session_start();
$aksi = $_GET['aksi'] ?? '';

/* ===========================
   SIMPAN DATA LUBANG
=========================== */
if ($aksi == 'simpan') {

    $jumlah_lubang = $_POST['jumlah_lubang'];
    $tanggal       = $_POST['tanggal'];
    $catatan       = $_POST['catatan'];
    $data_awal_id  = $_POST['data_awal_id'];
    $foto = upload();

    $query = "INSERT INTO lubang 
                (jumlah_lubang, foto_lokasi, tanggal, catatan, data_awal_id)
              VALUES
                ('$jumlah_lubang', '$foto', '$tanggal', '$catatan', '$data_awal_id')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['flash'] = [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data lubang berhasil disimpan'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon' => 'error',
            'title' => 'Gagal',
            'text' => 'Data lubang gagal disimpan'
        ];
    }

     header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ===========================
   EDIT DATA LUBANG
=========================== */
 elseif ($aksi == 'edit') {

    $lubang_id     = $_POST['lubang_id'];
    $jumlah_lubang = $_POST['jumlah_lubang'];
    $tanggal       = $_POST['tanggal'];
    $catatan       = $_POST['catatan'];
    $data_awal_id  = $_POST['data_awal_id'];
    $fotolama      = $_POST['fotolama'];
    $status         = $_POST['status'];

    // cek upload foto baru
    if ($_FILES['foto']['error'] === 4) {
        $foto_lokasi = $fotolama;
    } else {
        if (file_exists('../gambar_lubang/' . $fotolama)) {
            unlink('../gambar_lubang/' . $fotolama);
        }
        $foto_lokasi = upload();
    }

    $query = "UPDATE lubang SET
                jumlah_lubang = '$jumlah_lubang',
                foto_lokasi   = '$foto_lokasi',
                tanggal       = '$tanggal',
                catatan       = '$catatan',
                status       = '$status',
                data_awal_id  = '$data_awal_id'
              WHERE lubang_id = '$lubang_id'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['flash'] = [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data lubang berhasil diedit'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon' => 'error',
            'title' => 'Gagal',
            'text' => 'Data lubang gagal diedit'
        ];
    }

     header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ===========================
   HAPUS DATA LUBANG
=========================== */ 
elseif ($aksi == 'hapus') {

    $lubang_id = $_GET['lubang_id'];

    // ambil nama foto dari database
    $q = mysqli_query($conn, "SELECT foto_lokasi FROM lubang WHERE lubang_id='$lubang_id'");
    $data = mysqli_fetch_assoc($q);

    $foto = $data['foto_lokasi'];

    if ($foto && file_exists('../gambar_lubang/' . $foto)) {
        unlink('../gambar_lubang/' . $foto);
    }

   if (mysqli_query($conn, "DELETE FROM lubang WHERE lubang_id='$lubang_id'"))
    {
        $_SESSION['flash'] = [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data lubang berhasil dihapus'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon' => 'error',
            'title' => 'Gagal',
            'text' => 'Data lubang gagal hapus'
        ];
    }

     header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} 
/* ============================================================
   4. VERIFIKASI lubang
============================================================ */
elseif ($aksi == 'verifikasi') {

    $lubang_id = $_GET['lubang_id'];

    $query = "
        UPDATE lubang 
        SET status = 'verified'
        WHERE lubang_id = '$lubang_id'
    ";

    mysqli_query($conn, $query);

    $_SESSION['flash'] = [
        'icon' => 'success',
        'title' => 'Verifikasi Berhasil',
        'text' => 'lubang berhasil diverifikasi!'
    ];

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();


    /* ============================================================
   5. REJECT lubang
============================================================ */
} elseif ($aksi == 'reject') {

    $lubang_id   = $_POST['lubang_id'];
    $alasan_reject = $_POST['alasan_reject'];

    $query = "
        UPDATE lubang SET 
            status = 'rejected', 
            catatan = CONCAT(catatan, '\nREJECT: $alasan_reject')
        WHERE lubang_id = '$lubang_id'
    ";

    mysqli_query($conn, $query);

    $_SESSION['flash'] = [
        'icon' => 'error',
        'title' => 'lubang Ditolak',
        'text' => 'lubang berhasil direject!'
    ];

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();


    /* ============================================================
   DEFAULT
============================================================ */
}else {
    echo "Aksi tidak dikenali.";
}
