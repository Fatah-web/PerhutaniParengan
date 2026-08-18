<?php
session_start();

// ✅ Cek apakah session ada
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
  header("location:../index.php");
  exit();
}

// koneksi
require_once '../function/koneksi.php';

/** @var mysqli $conn */
/** @var array $data */

$user_id = $_SESSION['id'] ?? 0;

// ✅ Pakai prepared statement (lebih aman)
$stmt = $conn->prepare("
    SELECT u.*, p.* 
    FROM users u 
    INNER JOIN pegawai p ON u.user_id = p.user_id 
    WHERE u.user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistem Monitoring Tanaman</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/logo-m.png" />

  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <!-- Select2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
  <link rel="stylesheet" href="../assets/css/select2theme.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="../assets/css/btnedit.css">
  <link rel="stylesheet" href="../assets/css/dashboardp.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@600;700&display=swap" rel="stylesheet">
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
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
              <iconify-icon icon="solar:widget-5-bold-duotone" class="nav-small-cap-icon fs-4"></iconify-icon>
              <span class="hide-menu">Home</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link primary-hover-bg" href="./index.php?p=dashboard" aria-expanded="false">
                <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
           <!----><li class="sidebar-item">
              <a class="sidebar-link primary-hover-bg" href="./index.php?p=pemberitahuan" aria-expanded="false">
                <iconify-icon icon="solar:bell-bing-bold-duotone"></iconify-icon>
                <span class="hide-menu">pemberitahuan</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link primary-hover-bg" href="./index.php?p=target" aria-expanded="false">
                <iconify-icon icon="solar:rocket-bold-duotone"></iconify-icon>
                <span class="hide-menu">Target</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link primary-hover-bg" href="./index.php?p=progres" aria-expanded="false">
                <iconify-icon icon="solar:graph-up-bold-duotone"></iconify-icon>
                <span class="hide-menu">Progres</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link primary-hover-bg" href="./index.php?p=analisis" aria-expanded="false">
                <iconify-icon icon="solar:chart-2-bold-duotone"></iconify-icon>
                <span class="hide-menu">Analisis</span>
              </a>
            </li>
            <li class="nav-small-cap text-muted px-3 mt-3">
              <span class="fw-bold">DATA</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link primary-hover-bg" href="./index.php?p=users" aria-expanded="false">
                <iconify-icon icon="solar:user-circle-bold-duotone"></iconify-icon>
                <span class="hide-menu">Users</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link primary-hover-bg" href="./index.php?p=mandor" aria-expanded="false">
                <iconify-icon icon="solar:people-nearby-bold-duotone"></iconify-icon>
                <span class="hide-menu">Mandor</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link primary-hover-bg" href="./index.php?p=pegawai" aria-expanded="false">
                <iconify-icon icon="solar:user-id-bold-duotone"></iconify-icon>
                <span class="hide-menu">Pegawai</span>
              </a>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link primary-hover-bg" href="./index.php?p=bkph" aria-expanded="false">
                <iconify-icon icon="mdi:pine-tree"></iconify-icon>
                <span class="hide-menu">BKPH</span>
              </a>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link primary-hover-bg" href="./index.php?p=data_awal" aria-expanded="false">
                <iconify-icon icon="solar:database-bold-duotone"></iconify-icon>
                <span class="hide-menu">Data Awal</span>
              </a>
            </li>

            <!-- PROSES -->
            <li class="nav-small-cap text-muted px-3 mt-3">
              <span class="fw-bold">PROSES</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link primary-hover-bg" href="./index.php?p=lubang" aria-expanded="false">
                <iconify-icon icon="mdi:shovel"></iconify-icon>
                <span class="hide-menu">Lubang</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link primary-hover-bg" href="./index.php?p=ajir" aria-expanded="false">
                <iconify-icon icon="mdi:flag-triangle"></iconify-icon>
                <span class="hide-menu">Acir</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link primary-hover-bg" href="./index.php?p=penanaman" aria-expanded="false">
                <iconify-icon icon="mdi:sprout"></iconify-icon>
                <span class="hide-menu">Penanaman</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link primary-hover-bg" href="./index.php?p=monitoring" aria-expanded="false">
                <iconify-icon icon="solar:radar-bold-duotone"></iconify-icon>
                <span class="hide-menu">Monitoring</span>
              </a>
            </li>
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
                  <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                  </a>
                </li>
              </ul>
              <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                  <a><?= $data['nama']; ?></a>
                  <li class="nav-item dropdown">
                    <a class="nav-link" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                      aria-expanded="false">
                      <img src="../image/<?= $data['foto']; ?>" alt="" width="35" height="35" class="rounded-circle"
                        style="object-fit: cover;" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                      <div class="message-body">
                        <a href="index.php?p=profile_pegawai" class="d-flex align-items-center gap-2 dropdown-item">
                          <i class="ti ti-user fs-6"></i>
                          <p class="mb-0 fs-3">My Profile</p>
                        </a>
                        <a href="../function/logout.php"
                          class="btn btn-outline-primary btn-keluar mx-3 mt-2 d-block">Logout</a>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </nav>
          </header>
          <!--  Header End -->

          <!-- Alert Start -->
          <div class="flash-data" data-status="<?= $_SESSION['flash']['icon'] ?? ''; ?>"
            data-text="<?= $_SESSION['flash']['text'] ?? ''; ?>" data-title="<?= $_SESSION['flash']['title'] ?? ''; ?>">
          </div>
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
  <script src="../assets/js/dashboard.js"></script>
  <script src="../assets/libs/sweetalert/sweetalert2.all.min.js"></script>
  <script src="../assets/libs/sweetalert/alert.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.6.2/countUp.umd.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <!-- DaTables JS -->
  <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.min.js"></script>
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const dashboardData = {
      jumlah_tanaman: <?= $jumlah_tanaman ?>,
      pohon_hidup: <?= $pohon_hidup ?>,
      pohon_mati: <?= $pohon_mati ?>
    };
  </script>
  <script>
    const statusData = <?= json_encode([
      "pending" => $pending,
      "verified" => $verified,
      "rejected" => $rejected
    ]); ?>;
  </script>
  <script type="module" src="../assets/js/admin.js"></script>
  <script src="../assets/js/javav/chart-bar.js"></script>
  <script src="../assets/js/javav/chart-donuts2.js"></script>
  <script src="../assets/js/javav/chart-pie.js"></script>
  <script src="../assets/js/javav/image-light.js"></script>
  <script src="../assets/js/javav/swetalert-action.js"></script>
  <script>
    var options = {
      series: [

        {
          name: "Jumlah",
          type: "column",
          data: [
            <?= $total_target ?>,
            <?= $total_lubang ?>,
            <?= $total_ajir ?>,
            <?= $total_tanam ?>,
            <?= $total_hidup ?>
          ]
        },

        {
          name: "Persen",
          type: "area",
          data: [
            100,
            <?= $p_lubang ?>,
            <?= $p_ajir ?>,
            <?= $p_tanam ?>,
            <?= $p_hidup ?>
          ]
        }

      ],

      chart: {
        height: 360,
        type: 'line',

        toolbar: {
          show: true,
          tools: {
            download: true, // tombol download
            selection: true,
            zoom: true,
            zoomin: true,
            zoomout: true,
            pan: true,
            reset: true
          },

          export: {
            csv: {
              filename: "progress-penanaman"
            },
            svg: {
              filename: "progress-penanaman"
            },
            png: {
              filename: "progress-penanaman"
            }
          }
        }
      },

      stroke: {
        width: [0, 3],
        curve: 'smooth'
      },

      plotOptions: {
        bar: {
          columnWidth: '35%',
          borderRadius: 6
        }
      },

      fill: {
        type: ['solid', 'gradient'],
        gradient: {
          shadeIntensity: 0.4,
          opacityFrom: 0.4,
          opacityTo: 0.1
        }
      },

      markers: {
        size: 5
      },

      xaxis: {
        categories: [
          'Target',
          'Lubang',
          'Ajir',
          'Tanam',
          'Hidup'
        ]
      },

      yaxis: [

        {
          title: {
            text: "Jumlah"
          }
        },

        {
          opposite: true,
          max: 100,
          title: {
            text: "Persen (%)"
          }
        }

      ],

      colors: ['#8EA6F6', '#49eee0']

    };

    var chart = new ApexCharts(document.querySelector("#progressChart"), options);
    chart.render();
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {

      const el = document.querySelector("#chartStatus");

      if (!el) return;

      var options = {

        chart: {
          type: "pie",
          height: 260
        },

        series: [
          statusData.pending,
          statusData.verified,
          statusData.rejected
        ],

        labels: [
          "Pending",
          "Verified",
          "Rejected"
        ],

        colors: [
          "#FFC107",
          "#4CAF50",
          "#F44336"
        ],

        legend: {
          position: "bottom"
        },

        tooltip: {
          y: {
            formatter: function (val) {
              return val + " Data";
            }
          }
        }

      };

      var chart = new ApexCharts(el, options);

      chart.render();

    });
  </script>
</body>

</html>