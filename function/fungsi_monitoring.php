<?php
include 'koneksi.php';
include 'upload_monitoring.php';

session_start();
$aksi = $_GET['aksi'] ?? '';

/* ===========================
   SIMPAN DATA MONITORING
=========================== */
if ($aksi == 'simpan') {

    $jumlah_hidup   = $_POST['jumlah_hidup'];
    $jumlah_mati    = $_POST['jumlah_mati'];
    $tinggi_diameter = $_POST['tinggi_diameter'];
    $gangguan       = $_POST['gangguan'];
    $tanggal        = $_POST['tanggal'];
    $evaluasi       = $_POST['evaluasi'];
    $catatan        = $_POST['catatan'];
    $tanam_id       = $_POST['tanam_id'];

    $foto_hidup = upload_hidup();
    $foto_mati  = upload_mati();

    $query = "INSERT INTO monitoring
        (jumlah_hidup, jumlah_mati, tinggi_diameter,
         foto_hidup, foto_mati, gangguan,
         tanggal, evaluasi, catatan, tanam_id)
        VALUES
        ('$jumlah_hidup','$jumlah_mati','$tinggi_diameter',
         '$foto_hidup','$foto_mati','$gangguan',
         '$tanggal','$evaluasi','$catatan','$tanam_id')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['flash'] = [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data monitoring berhasil disimpan'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon' => 'error',
            'title' => 'Gagal',
            'text' => 'Data monitoring gagal disimpan'
        ];
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ===========================
   EDIT DATA MONITORING
=========================== */ elseif ($aksi == 'edit') {

    $monitoring_id  = $_POST['monitoring_id'];
    $jumlah_hidup   = $_POST['jumlah_hidup'];
    $jumlah_mati    = $_POST['jumlah_mati'];
    $tinggi_diameter = $_POST['tinggi_diameter'];
    $gangguan       = $_POST['gangguan'];
    $tanggal        = $_POST['tanggal'];
    $evaluasi       = $_POST['evaluasi'];
    $catatan        = $_POST['catatan'];
    $tanam_id       = $_POST['tanam_id'];
    $status         = $_POST['status'];

    $foto_hidup_lama = $_POST['foto_hidup_lama'];
    $foto_mati_lama  = $_POST['foto_mati_lama'];

    // FOTO HIDUP
    if ($_FILES['foto_hidup']['error'] === 4) {
        $foto_hidup = $foto_hidup_lama;
    } else {
        if ($foto_hidup_lama && file_exists('../gambar_monitoring/' . $foto_hidup_lama)) {
            unlink('../gambar_monitoring/' . $foto_hidup_lama);
        }
        $foto_hidup = upload_hidup();
    }

    // FOTO MATI
    if ($_FILES['foto_mati']['error'] === 4) {
        $foto_mati = $foto_mati_lama;
    } else {
        if ($foto_mati_lama && file_exists('../gambar_monitoring/' . $foto_mati_lama)) {
            unlink('../gambar_monitoring/' . $foto_mati_lama);
        }
        $foto_mati = upload_mati();
    }

    $query = "UPDATE monitoring SET
        jumlah_hidup   = '$jumlah_hidup',
        jumlah_mati    = '$jumlah_mati',
        tinggi_diameter= '$tinggi_diameter',
        gangguan       = '$gangguan',
        tanggal        = '$tanggal',
        evaluasi       = '$evaluasi',
        catatan        = '$catatan',
        tanam_id       = '$tanam_id',
        status         = '$status',
        foto_hidup     = '$foto_hidup',
        foto_mati      = '$foto_mati'
        WHERE monitoring_id='$monitoring_id'";

    mysqli_query($conn, $query);

    $_SESSION['flash'] = [
        'icon' => 'success',
        'title' => 'Berhasil',
        'text' => 'Data monitoring berhasil diedit'
    ];

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ===========================
   HAPUS DATA MONITORING
=========================== */ elseif ($aksi == 'hapus') {

    $monitoring_id = $_GET['monitoring_id'];

    $q = mysqli_query($conn, "SELECT foto_hidup,foto_mati 
                             FROM monitoring 
                             WHERE monitoring_id='$monitoring_id'");
    $d = mysqli_fetch_assoc($q);

    if ($d['foto_hidup'] && file_exists('../gambar_monitoring/' . $d['foto_hidup'])) {
        unlink('../gambar_monitoring/' . $d['foto_hidup']);
    }

    if ($d['foto_mati'] && file_exists('../gambar_monitoring/' . $d['foto_mati'])) {
        unlink('../gambar_monitoring/' . $d['foto_mati']);
    }

    mysqli_query($conn, "DELETE FROM monitoring WHERE monitoring_id='$monitoring_id'");

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ===========================
   VERIFIKASI MONITORING
=========================== */ elseif ($aksi == 'verifikasi') {

    $monitoring_id = $_GET['monitoring_id'];

    mysqli_query($conn, "
        UPDATE monitoring 
        SET status='verified'
        WHERE monitoring_id='$monitoring_id'
    ");

    $_SESSION['flash'] = [
        'icon' => 'success',
        'title' => 'Verifikasi Berhasil',
        'text' => 'Monitoring berhasil diverifikasi'
    ];

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


/* ===========================
   REJECT MONITORING
=========================== */ elseif ($aksi == 'reject') {

    $monitoring_id = $_POST['monitoring_id'];
    $alasan_reject = $_POST['alasan_reject'];

    mysqli_query($conn, "
        UPDATE monitoring SET
        status='rejected',
        catatan=CONCAT(catatan,'\nREJECT: $alasan_reject')
        WHERE monitoring_id='$monitoring_id'
    ");

    $_SESSION['flash'] = [
        'icon' => 'error',
        'title' => 'Monitoring Ditolak',
        'text' => 'Data monitoring berhasil direject'
    ];

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} else {
    echo "Aksi tidak dikenali.";
}
