<?php
include 'koneksi.php';
session_start();

// aktifkan mode exception untuk mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$aksi = $_GET['aksi'] ?? '';

try {

    // =====================================================
    // SIMPAN USER
    // =====================================================
    if ($aksi == 'simpan') {

        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role']; // ambil dari form
        $email = $_POST['email'];

        $query = "INSERT INTO users (username, password, role, email) 
                  VALUES ('$username', '$password', '$role', '$email')";

        mysqli_query($conn, $query);

        $_SESSION['flash'] = [
            'icon' => 'success',
            'text' => 'Data berhasil disimpan!',
            'title' => 'Simpan Berhasil'
        ];

        // =====================================================
        // HAPUS USER
        // =====================================================
    } elseif ($aksi == 'hapus') {

        $id = $_GET['user_id'];

        $query = "DELETE FROM users WHERE user_id = '$id'";
        mysqli_query($conn, $query);

        $_SESSION['flash'] = [
            'icon' => 'success',
            'text' => 'Data berhasil dihapus!',
            'title' => 'Hapus Berhasil'
        ];

        // =====================================================
        // EDIT USER
        // =====================================================
    } elseif ($aksi == 'edit') {

        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $role_baru = $_POST['role'];
        $email = $_POST['email'];

        // Ambil role lama
        $qRole = mysqli_query($conn, "SELECT role FROM users WHERE user_id = '$user_id'");
        $dataRole = mysqli_fetch_assoc($qRole);
        $role_lama = $dataRole['role'];

        // =====================================================
        // UPDATE DATA
        // =====================================================

        if (!empty($_POST['password'])) {

            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $query = "UPDATE users SET 
                        username = '$username',
                        password = '$password',
                        role     = '$role_baru',
                        email    = '$email'
                      WHERE user_id = '$user_id'";

        } else {

            $query = "UPDATE users SET 
                        username = '$username',
                        role     = '$role_baru',
                        email    = '$email'
                      WHERE user_id = '$user_id'";
        }

        mysqli_query($conn, $query);

        $_SESSION['flash'] = [
            'icon' => 'success',
            'title' => 'Edit Berhasil',
            'text' => 'Data berhasil diedit!'
        ];

        // =====================================================
        // UPDATE PROFIL USER
        // =====================================================
    } elseif ($aksi == 'updateUser') {

        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $role = $_POST['role'];
        $email = $_POST['email'];

        $query = "UPDATE users 
                  SET username = '$username',
                      role = '$role',
                      email = '$email'
                  WHERE user_id = '$user_id'";

        mysqli_query($conn, $query);

        $_SESSION['flash'] = [
            'icon' => 'success',
            'text' => 'Profil berhasil diedit!',
            'title' => 'Edit Berhasil'
        ];

        // =====================================================
        // UPDATE PASSWORD
        // =====================================================
    } elseif ($aksi == 'updatePassword') {

        // ID user yang sedang login
        $user_id = $_SESSION['id'] ?? 0;

        $passwordlama = $_POST['password_lama'];
        $level = $_SESSION['role'] ?? '';

        $query = mysqli_query(
            $conn,
            "SELECT * FROM users WHERE user_id = '$user_id' LIMIT 1"
        );

        $cek = mysqli_fetch_assoc($query);

        // =====================================================
        // FOLDER BERDASARKAN ROLE
        // =====================================================

        switch ($level) {

            case 'superadmin':
                $folder = 'superadmin';
                break;

            case 'admin':
                $folder = 'admin';
                break;

            case 'mandor':
                $folder = 'mandor';
                break;

            case 'asper':
                $folder = 'asper';
                break;

            case 'pimpinan':
                $folder = 'pimpinan';
                break;

            default:
                $folder = '';
                break;
        }

        // =====================================================
        // CEK USER
        // =====================================================

        if (!$cek) {

            $_SESSION['flash'] = [
                'icon' => 'error',
                'text' => 'Data user tidak ditemukan.',
                'title' => 'Edit Gagal'
            ];

            // =====================================================
            // CEK PASSWORD LAMA
            // =====================================================

        } elseif (password_verify($passwordlama, $cek['password'])) {

            $passwordbaru = password_hash(
                $_POST['password_baru'],
                PASSWORD_DEFAULT
            );

            $update = mysqli_query(
                $conn,
                "UPDATE users 
             SET password = '$passwordbaru' 
             WHERE user_id = '$user_id'"
            );

            if ($update) {

                $_SESSION['flash'] = [
                    'icon' => 'success',
                    'text' => 'Password berhasil diubah!',
                    'title' => 'Edit Berhasil'
                ];

            } else {

                $_SESSION['flash'] = [
                    'icon' => 'error',
                    'text' => 'Password gagal diubah!',
                    'title' => 'Edit Gagal'
                ];
            }

        } else {

            $_SESSION['flash'] = [
                'icon' => 'error',
                'text' => 'Password lama tidak sesuai, silakan ulangi!',
                'title' => 'Edit Gagal'
            ];
        }

        // =====================================================
        // REDIRECT
        // =====================================================

        if (!empty($_SERVER['HTTP_REFERER'])) {

            header("Location: " . $_SERVER['HTTP_REFERER']);

        } else {

            header("Location: ../$folder/index.php");
        }

        exit();
    } else {

        echo "<script>alert('Aksi tidak dikenali'); history.back();</script>";
        exit();
    }

    // =====================================================
    // REDIRECT GLOBAL
    // =====================================================

    if (!empty($_SERVER['HTTP_REFERER'])) {

        header("Location: " . $_SERVER['HTTP_REFERER']);

    } else {

        header("Location: ../admin/index.php?p=users");
    }

    exit();

} catch (mysqli_sql_exception $e) {

    // =====================================================
    // ERROR DUPLIKAT USERNAME
    // =====================================================

    if ($e->getCode() == 1062) {

        $_SESSION['flash'] = [
            'icon' => 'error',
            'title' => 'Username Duplikat',
            'text' => 'Username sudah digunakan, silakan pilih yang lain.'
        ];

    } else {

        $_SESSION['flash'] = [
            'icon' => 'error',
            'title' => 'Kesalahan Database',
            'text' => 'Error: ' . $e->getMessage()
        ];
    }

    // =====================================================
    // REDIRECT SAAT ERROR
    // =====================================================

    if (!empty($_SERVER['HTTP_REFERER'])) {

        header("Location: " . $_SERVER['HTTP_REFERER']);

    } else {

        header("Location: ../admin/index.php?p=users");
    }

    exit();
}
?>