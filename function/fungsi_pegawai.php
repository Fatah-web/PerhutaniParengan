<?php
include 'koneksi.php';
include 'upload.php';

session_start();
$aksi = $_GET['aksi'] ?? '';

if ($aksi == 'simpan') {
    $foto       = upload();
    $nip        = $_POST['nip'];
    $nama       = $_POST['nama'];
    $jabatan    = $_POST['jabatan'];
    $alamat     = $_POST['alamat'];
    $no_hp      = $_POST['no_hp'];
    $status     = $_POST['status'];

    $username   = $_POST['username'];
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email      = $_POST['email'];
    $role       = $_POST['role'];

    // 🔍 Cek duplikat username/email
    $cek_duplikat = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek_duplikat) > 0) {
        $_SESSION['flash'] = [
            'icon' => 'error',
            'text' => 'Username sudah digunakan! Silakan gunakan yang lain.',
            'title' => 'Duplikat Ditemukan'
        ];
        if (!empty($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            header("Location: ../admin/index.php?p=pegawai");
        }
        exit();
    }

    // Simpan ke tabel users lalu pegawai
    $sql = "
        INSERT INTO users (username, password, email, role, created_at)
        VALUES ('$username', '$password', '$email', '$role', NOW());

        INSERT INTO pegawai (foto, nip, nama, jabatan, alamat, no_hp, status, user_id)
        VALUES ('$foto', '$nip', '$nama', '$jabatan', '$alamat', '$no_hp', '$status', LAST_INSERT_ID());
    ";

    if (mysqli_multi_query($conn, $sql)) {
        $_SESSION['flash'] = [
            'icon' => 'success',
            'text' => 'Data pegawai berhasil disimpan!',
            'title' => 'Simpan Berhasil'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon' => 'error',
            'text' => 'Gagal menyimpan data, silakan ulangi!',
            'title' => 'Simpan Gagal'
        ];
    }

    if (!empty($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: ../admin/index.php?p=pegawai");
    }
    exit();


} elseif ($aksi == 'edit') {
    $pegawai_id     = $_POST['pegawai_id'];
    $nip            = $_POST['nip'];
    $nama           = $_POST['nama'];
    $jabatan        = $_POST['jabatan'];
    $alamat         = $_POST['alamat'];
    $no_hp  = $_POST['no_hp'];
    $status         = $_POST['status'];
    $fotolama       = $_POST['fotolama'];

    // Ganti foto jika diupload ulang
    if ($_FILES['foto']['error'] === 4) {
        $foto = $fotolama;
    } else {
        if (file_exists('../image/' . $fotolama)) {
            unlink('../image/' . $fotolama);
        }
        $foto = upload();
    }

    $query = "UPDATE pegawai SET 
                foto = '$foto',
                nip = '$nip',
                nama = '$nama',
                jabatan = '$jabatan',
                alamat = '$alamat',
                no_hp = '$no_hp',
                status = '$status'
              WHERE pegawai_id = '$pegawai_id'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['flash'] = [
            'icon' => 'success',
            'text' => 'Data pegawai berhasil diedit!',
            'title' => 'Edit Berhasil'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon' => 'error',
            'text' => 'Gagal mengedit data, silakan ulangi!',
            'title' => 'Edit Gagal'
        ];
    }

    if (!empty($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: ../admin/index.php?p=pegawai");
    }
    exit();


} elseif ($aksi == 'editprofil') {
    $pegawai_id = $_POST['pegawai_id'];
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $fotolama = $_POST['fotolama'];

    // Ganti foto jika diupload ulang
    if ($_FILES['foto']['error'] === 4) {
        $foto = $fotolama;
    } else {
        if (file_exists('../image/' . $fotolama)) {
            unlink('../image/' . $fotolama);
        }
        $foto = upload();
    }

    $query = "UPDATE pegawai SET 
                foto = '$foto',
                nip = '$nip',
                nama = '$nama',
                jabatan = '$jabatan',
                alamat = '$alamat',
                no_hp = '$no_hp'
              WHERE pegawai_id = '$pegawai_id'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['flash'] = [
            'icon' => 'success',
            'text' => 'Data pegawai berhasil diedit!',
            'title' => 'Edit Berhasil'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon' => 'error',
            'text' => 'Gagal mengedit data, silakan ulangi!',
            'title' => 'Edit Gagal'
        ];
    }

    if (!empty($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: ../admin/index.php?p=pegawai");
    }
    exit();
} elseif ($aksi == 'hapus') {
    $pegawai_id = $_GET['pegawai_id'];
    $hapus = $_GET['hapus'];

    // Ambil user_id agar user terkait ikut dihapus
    $res = mysqli_query($conn, "SELECT user_id FROM pegawai WHERE pegawai_id = '$pegawai_id'");
    $row = mysqli_fetch_assoc($res);
    $user_id = $row['user_id'] ?? null;

    if (mysqli_query($conn, "DELETE FROM pegawai WHERE pegawai_id = '$pegawai_id'")) {
        if ($user_id) {
            mysqli_query($conn, "DELETE FROM users WHERE user_id = '$user_id'");
        }

        if (file_exists('../image/' . $hapus)) {
            unlink('../image/' . $hapus);
        }

        $_SESSION['flash'] = [
            'icon' => 'success',
            'text' => 'Data pegawai berhasil dihapus!',
            'title' => 'Hapus Berhasil'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon' => 'error',
            'text' => 'Gagal menghapus data, silakan ulangi!',
            'title' => 'Hapus Gagal'
        ];
    }

    if (!empty($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: ../admin/index.php?p=pegawai");
    }
    exit();


} elseif ($aksi == 'updatepegawai') {
    $user_id     = $_POST['user_id'];
    $pegawai_id  = $_POST['pegawai_id'];
    $username    = $_POST['username'];
    $no_hp       = $_POST['no_hp'];
    $email       = $_POST['email'];
    $jabatan     = $_POST['jabatan'];
    $alamat      = $_POST['alamat'];

    // 🔍 Cek duplikat username/email tapi abaikan milik sendiri
    $cek_duplikat = mysqli_query($conn, "
        SELECT * FROM users 
        WHERE (username = '$username') 
        AND user_id != '$user_id'
    ");
    if (mysqli_num_rows($cek_duplikat) > 0) {
        $_SESSION['flash'] = [
            'icon' => 'error',
            'text' => 'Username sudah digunakan oleh pengguna lain!',
            'title' => 'Duplikat Ditemukan'
        ];
        if (!empty($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            header("Location: ../pegawai/index.php");
        }
        exit();
    }

    // Update data user dan pegawai
    $query_users = "UPDATE users 
                    SET username = '$username',
                        email    = '$email'
                    WHERE user_id = '$user_id'";

    $query_pegawai = "UPDATE pegawai 
                      SET jabatan = '$jabatan',
                          alamat = '$alamat',
                          no_hp = '$no_hp'
                      WHERE pegawai_id = '$pegawai_id'";

    $update_users = mysqli_query($conn, $query_users);
    $update_pegawai = mysqli_query($conn, $query_pegawai);

    if ($update_users && $update_pegawai) {
        $_SESSION['flash'] = [
            'icon' => 'success',
            'text' => 'Profil pegawai berhasil diperbarui!',
            'title' => 'Edit Berhasil'
        ];
    } else {
        $_SESSION['flash'] = [
            'icon' => 'error',
            'text' => 'Gagal mengedit profil pegawai!',
            'title' => 'Edit Gagal'
        ];
    }

    if (!empty($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: ../pegawai/index.php");
    }
    exit();


} else {
    echo "Aksi tidak dikenali.";
}
