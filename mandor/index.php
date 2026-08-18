<?php
session_start();

if ($_SESSION['role'] == "" || $_SESSION['role'] !== "mandor") {
  header("location:../index.php");
  exit();
}

include '../function/koneksi.php';
/** @var mysqli $conn */ // 🔥 biar VS Code tidak merah
$user_id = $_SESSION['id'];

// Ambil data mandors + data user
$query = "SELECT 
    m.mandor_id      AS mandor_id,
    m.user_id        AS mandor_user_id,
    m.nama,
    m.nip,
    m.alamat,
    m.no_hp,
    m.status,
    m.bkph,
    m.foto,

    u.user_id        AS user_id,
    u.username,
    u.role,
    u.email,

    b.bkph_id        AS bkph_id,
    b.nama_bkph,
    b.rph,
    b.petak,
    b.luas_baku,
    b.jenis_tanaman,
    b.jarak_tanam
FROM mandor m
JOIN users u 
    ON m.user_id = u.user_id
LEFT JOIN bkph b
    ON b.mandor_id = m.mandor_id
WHERE u.user_id = '$user_id'
LIMIT 1;";
$result = mysqli_query($conn, $query);
$profil = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistem Monitoring Tanaman</title>
  <link
    rel="shortcut icon"
    type="image/png"
    href="../assets/images/logos/logo-m.png" />
  <link
    href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.min.css"
    rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <!-- Select2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
  <link rel="stylesheet" href="../assets/css/select2theme.css">
  <link rel="stylesheet" href="../assets/css/btnedit.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/dashboardp.css">
</head>

<body>
  <!--  Body Wrapper -->
  <div
    class="page-wrapper"
    id="main-wrapper"
    data-layout="vertical"
    data-navbarbg="skin6"
    data-sidebartype="full"
    data-sidebar-position="fixed"
    data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
         <div
          class="brand-logo d-flex align-items-center justify-content-between">
          <a href="" class="text-nowrap logo-img">
            <img src="../assets/images/logos/logo.png" alt="" />
          </a>
          <div
            class="close-btn d-xl-none d-flex align-items-center justify-content-center sidebartoggler cursor-pointer"
            id="sidebarCollapse">
            <i class="ti ti-x"></i>
          </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="nav-small-cap">
              <iconify-icon
                icon="solar:menu-dots-linear"
                class="nav-small-cap-icon fs-4"></iconify-icon>
              <span class="hide-menu">Home</span>
            </li>
             <li class="sidebar-item">
              <a
                class="sidebar-link primary-hover-bg"
                href="./index.php?p=dashboard"
                aria-expanded="false">
                <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link primary-hover-bg"
                href="./index.php?p=progres"
                aria-expanded="false">
                <iconify-icon icon="solar:graph-up-bold-duotone"></iconify-icon>
                <span class="hide-menu">Progres</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link primary-hover-bg"
                href="./index.php?p=bkph"
                aria-expanded="false">
                <iconify-icon icon="mdi:pine-tree"></iconify-icon>
                <span class="hide-menu">BKPH</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link primary-hover-bg"
                href="./index.php?p=data_awal"
                aria-expanded="false">
                <iconify-icon icon="solar:database-bold-duotone"></iconify-icon>
                <span class="hide-menu">Data Awal</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link primary-hover-bg"
                href="./index.php?p=lubang"
                aria-expanded="false">
                <iconify-icon icon="mdi:shovel"></iconify-icon>
                <span class="hide-menu">Lubang</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link primary-hover-bg"
                href="./index.php?p=ajir"
                aria-expanded="false">
                <iconify-icon icon="mdi:flag-triangle"></iconify-icon>
                <span class="hide-menu">Acir</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link primary-hover-bg"
                href="./index.php?p=penanaman"
                aria-expanded="false">
                <iconify-icon icon="mdi:sprout"></iconify-icon>
                <span class="hide-menu">Penanaman</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link primary-hover-bg"
                href="./index.php?p=monitoring"
                aria-expanded="false">
                <iconify-icon icon="solar:radar-bold-duotone"></iconify-icon>
                <span class="hide-menu">Monitoring</span>
              </a>
            </li>
          </ul>
        </nav>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <div class="body-wrapper-inner">
        <div class="container-fluid">
          <!--  Header Start -->
          <header class="app-header">
            <nav class="navbar navbar-expand-lg navbar-light">

              <ul class="navbar-nav">
                <li class="nav-item d-block d-xl-none">
                  <a
                    class="nav-link sidebartoggler"
                    id="headerCollapse"
                    href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                  </a>
                </li>
              </ul>

              <div
                class="navbar-collapse justify-content-end px-0"
                id="navbarNav">
                <ul
                  class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                  <a><?= $profil['nama']; ?></a>
                  <li class="nav-item dropdown">
                    <a
                      class="nav-link"
                      href="javascript:void(0)"
                      id="drop2"
                      data-bs-toggle="dropdown"
                      aria-expanded="false">
                      <img
                        src="../image/<?= $profil['foto']; ?>"
                        alt=""
                        width="35"
                        height="35"
                        class="rounded-circle"
                        style="object-fit: cover;" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                      <div class="message-body">
                        <a href="index.php?p=profile_mandor&id=<?= $_SESSION['id']; ?>" class="d-flex align-items-center gap-2 dropdown-item">
                          <i class="ti ti-user fs-6"></i>
                          <p class="mb-0 fs-3">My Profile</p>
                        </a>
                        <a href="../function/logout.php"
                          class="btn btn-outline-primary mx-3 mt-2 btn-keluar d-block">Logout</a>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </nav>
          </header>
          <!--  Header End -->

          <!-- Alert Start -->
          <div class="flash-data" data-status="<?= $_SESSION['flash']['icon'] ?? ''; ?>" data-text="<?= $_SESSION['flash']['text'] ?? ''; ?>" data-title="<?= $_SESSION['flash']['title'] ?? ''; ?>"></div>
          <!-- Alert End -->

          <!--  Row 1 -->
          <div class="row">
            <?php
            if (empty($_GET['p'])) {
              echo "<script>document.location.href='index.php?p=dashboard'</script>";
            } else {
              $p = $_GET['p'];
              include "../content/$p.php";
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
  <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="../assets/libs/sweetalert/sweetalert2.all.min.js"></script>
  <script src="../assets/libs/sweetalert/alert.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <!-- DaTables JS -->
  <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.min.js"></script>
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script type="module" src="../assets/js/admin.js"></script>
  <script src="../assets/js/javav/image-light.js"></script>
  <script src="../assets/js/javav/swetalert-action.js"></script>

  
</body>

</html>