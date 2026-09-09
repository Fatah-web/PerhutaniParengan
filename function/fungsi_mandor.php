<?php
include 'koneksi.php';
include 'upload.php';
session_start();

// Aktifkan mode exception untuk MySQLi (agar try...catch berfungsi)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$aksi = $_GET['aksi'] ?? '';

if ($aksi == 'simpan') {
    // Data mandor
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $status = $_POST['status'];
    $bkph = $_POST['bkph'];
    $foto = upload(); // fungsi dari upload.php

    // Data user
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $role = 'mandor';

    try {
        // Mulai transaksi
        mysqli_begin_transaction($conn);

        // Simpan ke tabel users
        $query_user = "INSERT INTO users (username, password, email, role, created_at)
                       VALUES ('$username', '$password', '$email', '$role', NOW())";
        mysqli_query($conn, $query_user);

        // Ambil user_id terakhir
        $user_id = mysqli_insert_id($conn);

        // Simpan ke tabel mandor
        $query_mandor = "INSERT INTO mandor (user_id, foto, nama, nip, alamat, no_hp, bkph, status, created_at)
                          VALUES ('$user_id', '$foto', '$nama', '$nip', '$alamat', '$no_hp', '$bkph', '$status', NOW())";
        mysqli_query($conn, $query_mandor);

        // Commit transaksi
        mysqli_commit($conn);

        $_SESSION['flash'] = [
            'icon' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data mandor berhasil disimpan!'
        ];
    } catch (mysqli_sql_exception $e) {
        mysqli_rollback($conn);

        if ($e->getCode() == 1062) {
            $_SESSION['flash'] = [
                'icon' => 'error',
                'title' => 'Duplikat Data!',
                'text' => 'Username sudah digunakan, silakan gunakan username lain.'
            ];
        } else {
            $_SESSION['flash'] = [
                'icon' => 'error',
                'title' => 'Kesalahan Database!',
                'text' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} elseif ($aksi == 'edit') {
    $mandor_id = $_POST['mandor_id'];
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $status = $_POST['status'];
    $bkph = $_POST['bkph'];
    $fotolama = $_POST['fotolama'];

    // Jika tidak upload baru, pakai foto lama
    if ($_FILES['foto']['error'] === 4) {
        $foto = $fotolama;
    } else {
        // Hapus foto lama
        if (file_exists('../image/' . $fotolama)) {
            unlink('../image/' . $fotolama);
        }
        $foto = upload();
    }

    $query = "UPDATE mandor SET 
                foto = '$foto',
                nama = '$nama',
                nip = '$nip',
                alamat = '$alamat',
                no_hp = '$no_hp',
                bkph = '$bkph',
                status = '$status'
              WHERE mandor_id = '$mandor_id'";

    try {
        if (mysqli_query($conn, $query)) {
            $_SESSION['flash'] = [
                'icon' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Data mandor berhasil diperbarui!'
            ];
        } else {
            $_SESSION['flash'] = [
                'icon' => 'error',
                'title' => 'Gagal!',
                'text' => 'Gagal memperbarui data mandor!'
            ];
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION['flash'] = [
            'icon' => 'error',
            'title' => 'Kesalahan Database!',
            'text' => 'Error: ' . $e->getMessage()
        ];
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} elseif ($aksi == 'editprofil') {
    $mandor_id = $_POST['mandor_id'];
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $bkph = $_POST['bkph'];

    $fotolama = $_POST['fotolama'];

    // Jika tidak upload baru, pakai foto lama
    if ($_FILES['foto']['error'] === 4) {
        $foto = $fotolama;
    } else {
        // Hapus foto lama
        if (file_exists('../image/' . $fotolama)) {
            unlink('../image/' . $fotolama);
        }
        $foto = upload();
    }

    $query = "UPDATE mandor SET 
                foto = '$foto',
                nama = '$nama',
                nip = '$nip',
                alamat = '$alamat',
                no_hp = '$no_hp',
                bkph = '$bkph'
              WHERE mandor_id = '$mandor_id'";

    try {
        if (mysqli_query($conn, $query)) {
            $_SESSION['flash'] = [
                'icon' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Data mandor berhasil diperbarui!'
            ];
        } else {
            $_SESSION['flash'] = [
                'icon' => 'error',
                'title' => 'Gagal!',
                'text' => 'Gagal memperbarui data mandor!'
            ];
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION['flash'] = [
            'icon' => 'error',
            'title' => 'Kesalahan Database!',
            'text' => 'Error: ' . $e->getMessage()
        ];
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} elseif ($aksi == 'hapus') {
    $mandor_id = $_GET['mandor_id'];
    $hapus = $_GET['hapus'];

    // Ambil user_id terkait mandor
    $res = mysqli_query($conn, "SELECT user_id FROM mandor WHERE mandor_id = '$mandor_id'");
    $row = mysqli_fetch_assoc($res);
    $user_id = $row['user_id'] ?? null;

    try {
        mysqli_begin_transaction($conn);

        // Hapus data mandor
        mysqli_query($conn, "DELETE FROM mandor WHERE mandor_id = '$mandor_id'");

        // Hapus data user (jika ada)
        if ($user_id) {
            mysqli_query($conn, "DELETE FROM users WHERE user_id = '$user_id'");
        }

        // Hapus foto (jika ada)
        if (!empty($hapus) && file_exists('../image/' . $hapus)) {
            unlink('../image/' . $hapus);
        }

        mysqli_commit($conn);

        $_SESSION['flash'] = [
            'icon' => 'success',
            'title' => 'Hapus Berhasil',
            'text' => 'Data mandor berhasil dihapus!'
        ];
    } catch (mysqli_sql_exception $e) {
        mysqli_rollback($conn);

        $_SESSION['flash'] = [
            'icon' => 'error',
            'title' => 'Hapus Gagal',
            'text' => 'Gagal menghapus data mandor! Error: ' . $e->getMessage()
        ];
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} elseif ($aksi == 'updatemandor') {
    // update profil dari sisi mandor
    $user_id = $_POST['user_id'];
    $mandor_id = $_POST['mandor_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];

    try {
        mysqli_begin_transaction($conn);

        // update users
        mysqli_query($conn, "UPDATE users SET username = '$username', email = '$email' WHERE user_id = '$user_id'");

        // update mandor
        mysqli_query($conn, "UPDATE mandor SET no_hp = '$no_hp', alamat = '$alamat' WHERE mandor_id = '$mandor_id'");

        mysqli_commit($conn);

        $_SESSION['flash'] = [
            'icon' => 'success',
            'title' => 'Edit Berhasil',
            'text' => 'Profil mandor berhasil diperbarui!'
        ];
    } catch (mysqli_sql_exception $e) {
        mysqli_rollback($conn);
        $_SESSION['flash'] = [
            'icon' => 'error',
            'title' => 'Edit Gagal',
            'text' => 'Gagal memperbarui profil mandor! Error: ' . $e->getMessage()
        ];
    }

    if (!empty($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: ../mandor/index.php");
    }
    exit();
} else {
    echo "Aksi tidak dikenali.";
}
