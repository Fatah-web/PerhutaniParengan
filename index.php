<?php
include 'function/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

session_start();
$useTurnstile = false; // true = aktif, false = nonaktif
/* =========================
   LOGIN
========================= */
if (isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // ====== VERIFIKASI CLOUDFLARE TURNSTILE ======
  $turnstileSecret = '0x4AAAAAAB_JV08DsR3VxKsgpqcu3G0NEWY';
  $turnstileResponse = $_POST['cf-turnstile-response'] ?? '';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://challenges.cloudflare.com/turnstile/v0/siteverify");
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'secret' => $turnstileSecret,
    'response' => $turnstileResponse,
    'remoteip' => $_SERVER['REMOTE_ADDR']
  ]));

  $verifyResponse = curl_exec($ch);
  curl_close($ch);

  $responseData = json_decode($verifyResponse);

  if (!$useTurnstile || ($responseData && $responseData->success)) {

    $stmt = $conn->prepare("
      SELECT 
                                u.*,
                                m.mandor_id,
                                p.pegawai_id
                            FROM users u
                            LEFT JOIN mandor m 
                                ON u.user_id = m.user_id
                            LEFT JOIN pegawai p
                                ON u.user_id = p.user_id
                            WHERE u.username = ?
");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $data = $result->fetch_assoc();

      if (password_verify($password, $data['password'])) {
        $_SESSION['id'] = $data['user_id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['pegawai_id'] = $data['pegawai_id'] ?? null;

        /* Simpan mandor_id jika ada */
        if ($data['role'] === 'mandor') {
          $_SESSION['mandor_id'] = $data['mandor_id'];
        } else {
          $_SESSION['mandor_id'] = null;
        }

        $_SESSION['flash'] = [
          'icon' => 'success',
          'title' => 'Selamat Datang!',
          'text' => 'Hai ' . $data['username'] . ', senang bertemu lagi.'
        ];

        if ($data['role'] == "admin")
          header("Location: admin/index.php");
        elseif ($data['role'] == "asper")
          header("Location: asper/index.php");
        elseif ($data['role'] == "mandor")
          header("Location: mandor/index.php");
        elseif ($data['role'] == "pimpinan")
          header("Location: pimpinan/index.php");
        else {
          $_SESSION['flash'] = [
            'icon' => 'warning',
            'title' => 'Role Tidak Dikenali',
            'text' => 'Role user tidak valid.'
          ];
          header("Location: index.php");
        }
        exit();
      } else {
        $_SESSION['flash'] = [
          'icon' => 'error',
          'title' => 'Password Salah',
          'text' => 'Password yang Anda masukkan tidak sesuai.'
        ];
      }
    } else {
      $_SESSION['flash'] = [
        'icon' => 'error',
        'title' => 'Username Tidak Ditemukan',
        'text' => 'Silakan periksa kembali username Anda.'
      ];
    }
    $stmt->close();
  } else {
    $_SESSION['flash'] = [
      'icon' => 'error',
      'title' => 'Verifikasi Gagal',
      'text' => 'Verifikasi keamanan gagal. Coba lagi.'
    ];
  }

  header("Location: index.php");
  exit();
}

/* =========================
   CEK EMAIL
========================= */
if (isset($_POST['cek_email'])) {
  $email = $_POST['email'];

  $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

  if (mysqli_num_rows($cek) > 0) {

    $otp = rand(100000, 999999);
    $expired = date("Y-m-d H:i:s", strtotime("+5 minutes"));

    mysqli_query($conn, "
        UPDATE users
        SET
        otp_code='$otp',
        otp_expired='$expired'
        WHERE email='$email'
    ");

    // kirim OTP menggunakan PHPMailer

    $mail = new PHPMailer(true);

    try {

      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;

      // Email pengirim
      $mail->Username = 'fatahchiliks1@gmail.com';
      $mail->Password = 'ahsgsrazccajgdsl';

      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
      $mail->Port = 465;

      $mail->SMTPOptions = [
        'ssl' => [
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true,
        ]
      ];

      $mail->CharSet = 'UTF-8';

      $mail->setFrom(
        'fatahchiliks1@gmail.com',
        'Sistem Monitoring Tanaman'
      );
      $mail->addAddress($email);

      $mail->isHTML(true);
      $mail->Subject = 'Kode OTP Reset Password';

      $mail->Body = "
        <h2>Reset Password</h2>

        <p>Halo,</p>

        <p>Kode OTP Anda adalah:</p>

        <div style='
            font-size:32px;
            font-weight:bold;
            color:#198754;
            letter-spacing:6px;
            text-align:center;
            padding:15px;
            border:1px dashed #198754;
        '>
            $otp
        </div>

        <p>Kode ini berlaku selama <b>5 menit</b>.</p>

        <p>Apabila Anda tidak meminta reset password, abaikan email ini.</p>

        <hr>

        <small>Sistem Monitoring Tanaman</small>
    ";

      $mail->send();
      $_SESSION['reset_email'] = $email;
      $_SESSION['show_otp_modal'] = true;

      $_SESSION['flash'] = [
        'icon' => 'success',
        'title' => 'OTP Berhasil Dikirim',
        'text' => 'Silakan cek email Anda.'
      ];
    } catch (Exception $e) {

      $_SESSION['flash'] = [
        'icon' => 'error',
        'title' => 'Gagal Mengirim OTP',
        'text' => 'Email tidak dapat dikirim: ' . $mail->ErrorInfo
      ];

      header("Location:index.php");
      exit();
    }

  } else {

    $_SESSION['flash'] = [
      'icon' => 'error',
      'title' => 'Email Tidak Ditemukan',
      'text' => 'Email belum terdaftar.'
    ];

  }
  header("Location: index.php");
  exit();
}

// proses baru
if (isset($_POST['cek_otp'])) {

  $otp = $_POST['otp'];
  $email = $_SESSION['reset_email'];

  $cek = mysqli_query($conn, "
        SELECT *
        FROM users
        WHERE
        email='$email'
        AND otp_code='$otp'
        AND otp_expired > NOW()
    ");

  if (mysqli_num_rows($cek) > 0) {

    $_SESSION['otp_verified'] = true;
    unset($_SESSION['show_otp_modal']);
    $_SESSION['show_password_modal'] = true;

  } else {

    $_SESSION['flash'] = [
      'icon' => 'error',
      'title' => 'OTP Salah',
      'text' => 'OTP tidak valid atau sudah kadaluarsa.'
    ];

    $_SESSION['show_otp_modal'] = true;

  }

  header("Location:index.php");
  exit();
}

/* =========================
   UPDATE PASSWORD
========================= */
if (isset($_POST['ubah_password'])) {

  if (!isset($_SESSION['otp_verified'])) {

    $_SESSION['flash'] = [
      'icon' => 'error',
      'title' => 'Akses Ditolak',
      'text' => 'Silakan verifikasi OTP terlebih dahulu.'
    ];

    header("Location:index.php");
    exit();
  }

  $email = $_SESSION['reset_email'];
  if ($_POST['password_baru'] != $_POST['konfirmasi_password']) {

    $_SESSION['flash'] = [
      'icon' => 'error',
      'title' => 'Password Tidak Sama',
      'text' => 'Konfirmasi password tidak sesuai.'
    ];

    $_SESSION['show_password_modal'] = true;

    header("Location:index.php");
    exit();
  }
  $password = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);

  mysqli_query($conn, "
        UPDATE users
        SET
        password='$password',
        otp_code=NULL,
        otp_expired=NULL
        WHERE email='$email'
    ");

  unset($_SESSION['otp_verified']);
  unset($_SESSION['reset_email']);
  unset($_SESSION['show_password_modal']);

  $_SESSION['flash'] = [
    'icon' => 'success',
    'title' => 'Berhasil',
    'text' => 'Password berhasil diubah.'
  ];

  header("Location:index.php");
  exit();
}
?>


<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sistem Monitoring Tanaman</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/logo-m.png" />
  <link rel="stylesheet" href="assets/css/styles.min.css" />
</head>

<body>
  <div class="flash-data" data-status="<?= $_SESSION['flash']['icon'] ?? ''; ?>"
    data-text="<?= $_SESSION['flash']['text'] ?? ''; ?>" data-title="<?= $_SESSION['flash']['title'] ?? ''; ?>">
  </div>
  <?php unset($_SESSION['flash']); ?>

  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div
      class="position-relative overflow-hidden text-bg-light min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="./index.html" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="assets/images/logos/logo.png" alt="">
                </a>
                <p class="text-center">Sistem Monitoring Tanaman</p>
                <form method="post">
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Username</label>
                    <input name="username" type="text" class="form-control" id="exampleInputEmail1"
                      aria-describedby="emailHelp">
                  </div>
                  <div class="mb-4">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" id="exampleInputPassword1">
                  </div>
                  <div class="mb-3">
                    <div class="cf-turnstile" data-sitekey="0x4AAAAAAB_JV497VQbLUQKa" data-callback="turnstileSuccess">
                    </div>

                    <a href="#" data-bs-toggle="modal" data-bs-target="#lupaPasswordModal">
                      Lupa Password?
                    </a>
                  </div>
                  <button type="submit" name="login" class="btn btn-success w-100 py-8 fs-4 mb-2">Log In</button>
                </form>
                <a href="../" class="btn btn-success w-100 py-8 fs-4 mb-4">Kembali</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

  <script>
    function turnstileSuccess(token) {
      document.getElementById("captcha-status").style.display = "block";
    }
  </script>

  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/libs/sweetalert/sweetalert2.all.min.js"></script>
  <script src="assets/libs/sweetalert/alert.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <!-- Modal Lupa Password -->
  <!-- Modal Lupa Password (Versi Simple) -->
  <div class="modal fade" id="lupaPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Lupa Password</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <!-- ================= STEP 1 ================= -->
          <form method="post" id="form-email">

            <h6 class="text-center mb-3">
              Masukkan email yang terdaftar
            </h6>

            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>

            <button type="submit" name="cek_email" class="btn btn-success w-100">
              Kirim OTP
            </button>

          </form>


          <!-- ================= STEP 2 ================= -->
          <form method="post" id="form-otp" class="d-none">

            <h6 class="text-center mb-2">
              Masukkan Kode OTP
            </h6>

            <p class="text-center text-muted small">
              OTP telah dikirim ke email Anda.
            </p>

            <div class="mb-3">
              <label>Kode OTP</label>
              <input type="text" name="otp" maxlength="6" class="form-control text-center" placeholder="123456"
                required>
            </div>

            <button type="submit" name="cek_otp" class="btn btn-success w-100">
              Verifikasi OTP
            </button>

          </form>


          <!-- ================= STEP 3 ================= -->
          <form method="post" id="form-password" class="d-none">

            <h6 class="text-center mb-3">
              Password Baru
            </h6>

            <div class="mb-3">
              <label>Password Baru</label>
              <input type="password" name="password_baru" class="form-control" required>
            </div>

            <div class="mb-3">
              <label>Konfirmasi Password</label>
              <input type="password" name="konfirmasi_password" class="form-control" required>
            </div>

            <button type="submit" name="ubah_password" class="btn btn-primary w-100">
              Simpan Password
            </button>

          </form>

        </div>

      </div>
    </div>
  </div>

  <?php if (isset($_SESSION['show_otp_modal'])): ?>

    <script>

      document.addEventListener("DOMContentLoaded", function () {

        var modal = new bootstrap.Modal(
          document.getElementById('lupaPasswordModal')
        );

        modal.show();

        document.getElementById('form-email').classList.add('d-none');
        document.getElementById('form-otp').classList.remove('d-none');

      });

    </script>

    <?php unset($_SESSION['show_otp_modal']); endif; ?>

  <?php if (isset($_SESSION['show_password_modal'])): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function () {

        var modal = new bootstrap.Modal(document.getElementById('lupaPasswordModal'));
        modal.show();

        document.getElementById('form-email').classList.add('d-none');
        document.getElementById('form-otp').classList.add('d-none');
        document.getElementById('form-password').classList.remove('d-none');

      });
    </script>
    <?php unset($_SESSION['show_password_modal']); endif; ?>
</body>

</html>