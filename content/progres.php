<?php
include '../function/koneksi.php';
/** @var mysqli $conn */ // 🔥 biar VS Code tidak merah
$keyword = $_GET['keyword'] ?? '';
$conditions = [];

/* FILTER PENCARIAN */
if (!empty($keyword)) {
  $keyword = mysqli_real_escape_string($conn, $keyword);

  $conditions[] = "( 
        da.tahun_tanam LIKE '%$keyword%' OR
        b.nama_bkph LIKE '%$keyword%' OR
        b.rph LIKE '%$keyword%' OR
        m.nama LIKE '%$keyword%'
  )";
}

/* FILTER MANDOR LOGIN */
if (isset($_SESSION['role']) && $_SESSION['role'] == 'mandor') {
  $mandor_id = mysqli_real_escape_string($conn, $_SESSION['mandor_id']);
  $conditions[] = "b.mandor_id = '$mandor_id'";
}

/* GABUNGKAN WHERE */
$where = "";
if (!empty($conditions)) {
  $where = "WHERE " . implode(" AND ", $conditions);
}

$query = mysqli_query($conn, "
SELECT 
    da.data_awal_id,
    da.tahun_tanam,
    da.target_pohon,
    b.nama_bkph,
    b.rph,
    b.petak,
    m.nama AS nama_mandor,
    m.foto AS foto_mandor,

    /* TOTAL LUBANG */
    (
        SELECT IFNULL(SUM(l.jumlah_lubang),0)
        FROM lubang l
        WHERE l.data_awal_id = da.data_awal_id
    ) AS total_lubang,

    /* TOTAL AJIR */
    (
        SELECT IFNULL(SUM(a.jumlah_ajir),0)
        FROM ajir a
        JOIN lubang l ON a.lubang_id = l.lubang_id
        WHERE l.data_awal_id = da.data_awal_id
    ) AS total_ajir,

    /* TOTAL TANAM */
    (
        SELECT IFNULL(SUM(p.jumlah_tanam),0)
        FROM penanaman p
        JOIN ajir a ON p.ajir_id = a.ajir_id
        JOIN lubang l ON a.lubang_id = l.lubang_id
        WHERE l.data_awal_id = da.data_awal_id
    ) AS total_tanam,

    /* TOTAL HIDUP */
    (
        SELECT IFNULL(SUM(mo.jumlah_hidup),0)
        FROM monitoring mo
        JOIN penanaman p ON mo.tanam_id = p.tanam_id
        JOIN ajir a ON p.ajir_id = a.ajir_id
        JOIN lubang l ON a.lubang_id = l.lubang_id
        WHERE l.data_awal_id = da.data_awal_id
    ) AS total_hidup,

    /* TANGGAL TERAKHIR MONITORING */
   /* TANGGAL MONITORING TERAKHIR */
    (
        SELECT MAX(mo.tanggal)
        FROM monitoring mo
        JOIN penanaman p ON mo.tanam_id = p.tanam_id
        JOIN ajir a ON p.ajir_id = a.ajir_id
        JOIN lubang l ON a.lubang_id = l.lubang_id
        WHERE l.data_awal_id = da.data_awal_id
    ) AS tanggal_monitoring,

    /* TANGGAL PENANAMAN TERAKHIR */
    (
        SELECT MAX(p.tanggal)
        FROM penanaman p
        JOIN ajir a ON p.ajir_id = a.ajir_id
        JOIN lubang l ON a.lubang_id = l.lubang_id
        WHERE l.data_awal_id = da.data_awal_id
    ) AS tanggal_penanaman,

    /* TANGGAL AJIR TERAKHIR */
    (
        SELECT MAX(a.tanggal)
        FROM ajir a
        JOIN lubang l ON a.lubang_id = l.lubang_id
        WHERE l.data_awal_id = da.data_awal_id
    ) AS tanggal_ajir,

    /* TANGGAL LUBANG TERAKHIR */
    (
        SELECT MAX(l.tanggal)
        FROM lubang l
        WHERE l.data_awal_id = da.data_awal_id
    ) AS tanggal_lubang,

    /* STATUS TERAKHIR (PRIORITAS MONITORING) */
    (
        SELECT mo.status
        FROM monitoring mo
        JOIN penanaman p ON mo.tanam_id = p.tanam_id
        JOIN ajir a ON p.ajir_id = a.ajir_id
        JOIN lubang l ON a.lubang_id = l.lubang_id
        WHERE l.data_awal_id = da.data_awal_id
        ORDER BY mo.tanggal DESC
        LIMIT 1
    ) AS status_monitoring,
     /* STATUS AJIR TERAKHIR */
(
    SELECT a.status
    FROM ajir a
    JOIN lubang l ON a.lubang_id = l.lubang_id
    WHERE l.data_awal_id = da.data_awal_id
    ORDER BY a.tanggal DESC
    LIMIT 1
) AS status_ajir,

/* STATUS LUBANG TERAKHIR */
(
    SELECT l.status
    FROM lubang l
    WHERE l.data_awal_id = da.data_awal_id
    ORDER BY l.tanggal DESC
    LIMIT 1
) AS status_lubang,

    (
        SELECT p.status
        FROM penanaman p
        JOIN ajir a ON p.ajir_id = a.ajir_id
        JOIN lubang l ON a.lubang_id = l.lubang_id
        WHERE l.data_awal_id = da.data_awal_id
        ORDER BY p.tanggal DESC
        LIMIT 1
    ) AS status_penanaman

FROM data_awal da
LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
LEFT JOIN mandor m ON b.mandor_id = m.mandor_id
$where
ORDER BY da.tahun_tanam DESC
");
?>
<!-- ================= HEADER DASHBOARD ================= -->
<!-- ================= HEADER DASHBOARD ================= -->

<!-- BARIS 1 -->
<div class="row align-items-center mb-2">

  <!-- JUDUL + TOMBOL -->
  <div class="col-12 col-md-auto">
    <div class="d-flex flex-wrap align-items-center gap-3">

      <h2 class="fw-bold mb-0">
        Progres Tanam
        <span class="text-muted">(<?= mysqli_num_rows($query) ?>)</span>
      </h2>
      <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'asper') { ?>
        <button type="button"
          class="btn btn-primary d-flex align-items-center gap-2"
          data-bs-toggle="modal"
          data-bs-target="#formDataAwalModal">

          <iconify-icon icon="solar:add-square-bold" height="20"></iconify-icon>
          Tambah  
        </button>
      <?php } ?>

    </div>
  </div>

</div>


<!-- BARIS 2 -->
<div class="row align-items-center mb-4">

  <!-- DESKRIPSI -->
  <div class="col-12 col-md-6 mb-2 mb-md-0">
    <small class="text-muted">
      Kelola dan cari data penanaman
    </small>
  </div>

  <!-- SEARCH -->
  <div class="col-12 col-md-6">

    <form method="GET" action="index.php" class="d-flex flex-wrap gap-2 justify-content-md-end">

      <input type="hidden" name="p" value="progres">

      <div class="input-group search-modern" style="max-width:320px;">
        <span class="input-group-text bg-white">
          <i class="bi bi-search"></i>
        </span>

        <input type="text"
          name="keyword"
          class="form-control border-start-0 bg-white"
          placeholder="Cari Tahun / BKPH / Mandor..."
          value="<?= $_GET['keyword'] ?? '' ?>">
      </div>

      <button type="submit" class="btn btn-primary">
        Cari
      </button>

      <a href="index.php?p=progres" class="btn btn-outline-secondary">
        Reset
      </a>

    </form>

  </div>

</div>

<!-- ================= MODAL TAMBAH ================= -->
<div class="modal fade" id="formDataAwalModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <form action="../function/fungsi_data_awal.php?aksi=simpan" method="POST">

        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white">Tambah Data Awal</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="mb-3">
            <label>BKPH (BKPH-RPH-Petak)</label>
            <select name="bkph_id" class="form-select selectinput" required>
              <option value="">-- Pilih BKPH / RPH / Petak --</option>
              <?php
              $bkph = mysqli_query($conn, "SELECT * FROM bkph ORDER BY nama_bkph, rph, petak");
              while ($b = mysqli_fetch_assoc($bkph)) {
              ?>
                <option value="<?= $b['bkph_id'] ?>">
                  <?= $b['nama_bkph'] ?> - <?= $b['rph'] ?> - <?= $b['petak'] ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <div class="mb-3">
            <label>Tahun Tanam</label>
            <input type="text" name="tahun_tanam" class="form-control" placeholder="Contoh: 2025" required>
          </div>

          <div class="mb-3">
            <label>Target Pohon</label>
            <input type="number" name="target_pohon" class="form-control" placeholder="Contoh: 1200" required>
          </div>


        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>

      </form>
    </div>
  </div>
</div>
<!-- ================= END MODAL TAMBAH ================= -->
<div class="row">
  <?php while ($row = mysqli_fetch_assoc($query)):

    $target_pohon = (int)($row['target_pohon'] ?? 0);
    $total_lubang = (int)($row['total_lubang'] ?? 0);
    $total_ajir   = (int)($row['total_ajir'] ?? 0);
    $total_tanam  = (int)($row['total_tanam'] ?? 0);
    $total_hidup  = (int)($row['total_hidup'] ?? 0);

    $status = $row['status_monitoring']
      ?? $row['status_penanaman']
      ?? $row['status_ajir']
      ?? $row['status_lubang']
      ?? 'data awal';

    $progress = 20;
    if ($total_lubang > 0) $progress = 40;
    if ($total_ajir > 0)   $progress = 60;
    if ($total_tanam > 0)  $progress = 80;
    if ($total_hidup > 0)  $progress = 100;

    $tahap = 1; // default data awal

    if ($total_lubang > 0) $tahap = 2;
    if ($total_ajir > 0)   $tahap = 3;
    if ($total_tanam > 0)  $tahap = 4;
    if ($total_hidup > 0)  $tahap = 5;

    $badgeColor = "secondary";
    if ($status == "verified") $badgeColor = "success";
    if ($status == "pending")  $badgeColor = "warning";
    if ($status == "rejected") $badgeColor = "danger";

    $progressColor = "secondary";
    if ($progress > 20 && $progress <= 40) $progressColor = "danger";
    elseif ($progress > 40 && $progress <= 60) $progressColor = "warning";
    elseif ($progress > 60 && $progress <= 80) $progressColor = "primary";
    elseif ($progress > 80) $progressColor = "success";

    $tanggal = $row['tanggal_monitoring']
      ?? $row['tanggal_penanaman']
      ?? $row['tanggal_ajir']
      ?? $row['tanggal_lubang']
      ?? null;

    if ($tanggal) {
      $tanggal = date('d M Y', strtotime($tanggal));
    } else {
      $tanggal = '-';
    }

    $persentase = 0;

    $target  = (int)($row['target_pohon'] ?? 0);
    $lubang  = (int)($row['total_lubang'] ?? 0);
    $ajir    = (int)($row['total_ajir'] ?? 0);
    $tanam   = (int)($row['total_tanam'] ?? 0);
    $hidup   = (int)($row['total_hidup'] ?? 0);

    if ($target > 0) {

      // PRIORITAS TAHAP TERAKHIR
      if ($hidup > 0) {
        $persentase = ($hidup / $target) * 100;
      } elseif ($tanam > 0) {
        $persentase = ($tanam / $target) * 100;
      } elseif ($ajir > 0) {
        $persentase = ($ajir / $target) * 100;
      } elseif ($lubang > 0) {
        $persentase = ($lubang / $target) * 100;
      }
    }

    $persentase = number_format($persentase, 2);

    // BATASI MAX 100%
    if ($persentase > 100) {
      $persentase = 100;
    }

  ?>

    <div class="col-lg-4 col-md-6 mb-4">
      <div class="card project-card p-4 h-100">

        <!-- HEADER -->
        <div class="project-header d-flex justify-content-between align-items-start">
          <div>
            <h5 class="fw-bold mb-1">
              <i class="bi bi-calendar-event text-primary me-2"></i>
              Tahun <?= $row['tahun_tanam'] ?>
            </h5>
            <div class="small text-muted">
              <i class="bi bi-geo-alt me-1"></i>
              <?= $row['nama_bkph'] ?> • RPH <?= $row['rph'] ?> • Petak <?= $row['petak'] ?>
            </div>
          </div>
          <span class="badge bg-<?= $badgeColor ?> px-3 py-2 text-uppercase shadow-sm">
            <?= $status ?>
          </span>
        </div>

        <!-- CARD INFORMASI -->
        <div class="card border-0 shadow-sm mb-4 bg- rounded-3" style="background: linear-gradient(135deg, #f9f9f9, #dde2e1);">
          <div class="card-body p-4">

            <!-- MANDOR -->
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom border-1 border-black">

              <div>
                <div class="text-muted small text-uppercase fw-semibold mb-1">
                  Mandor
                </div>
                <div class="fw-semibold fs-5 text-dark">
                  <?= htmlspecialchars($row['nama_mandor']) ?>
                </div>
              </div>

              <!-- FOTO MANDOR (ukuran sama seperti icon target) -->
              <div class="icon-wrapper">
                <img
                  src="<?= !empty($row['foto_mandor']) ? '../image/' . $row['foto_mandor'] : '../assets/images/profile/user1.jpg'; ?>"
                  alt="Foto Mandor"
                  class="rounded-circle img-fluid"
                  onclick="openImage(this.src)">
              </div>

            </div>

            <!-- TARGET POHON -->
            <div class="d-flex align-items-center justify-content-between">

              <div>
                <div class="text-muted small text-uppercase fw-semibold mb-1">
                  Target Pohon
                </div>
                <div class="fw-bold fs-4 text-success">
                  <?= number_format($row['target_pohon']) ?>
                  <span class="fs-6 text-muted fw-normal">pohon</span>
                </div>
              </div>

            </div>

          </div>
        </div>

        <!-- PROGRESS -->
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span><i class="bi bi-bar-chart-line me-1"></i> Progress</span>
            <h5 class="fw-bold text-dark"><?= $progress ?>%</h5>
          </div>

          <div class="progress" style="height:10px; border-radius:20px;">
            <div class="progress-bar bg-<?= $progressColor ?>"
              role="progressbar"
              style="width: <?= $progress ?>%; border-radius:20px; transition:0.6s;">
            </div>
          </div>
        </div>

        <div id="chart<?= $row['data_awal_id'] ?>" style="height:190px;"></div>
        <script>
          document.addEventListener("DOMContentLoaded", function() {

            var options = {
              series: [{
                name: "Jumlah",
                data: [<?= $target_pohon ?>, <?= $total_lubang ?>, <?= $total_ajir ?>, <?= $total_tanam ?>, <?= $total_hidup ?>]
              }],
              chart: {
                type: "line",
                height: 200,
                toolbar: {
                  show: false
                }
              },
              title: {
                text: "Tahapan Penanaman",
                align: "center"
              },

              stroke: {
                curve: "smooth",
                width: 3
              },
              markers: {
                size: 4
              },
              xaxis: {
                categories: ["target", "Lubang", "Ajir", "Tanam", "Hidup"]
              },
              grid: {
                padding: {
                  bottom: -5
                }
              },
              yaxis: {
                title: {
                  text: "hasil kegiatan"
                }
              },
              colors: ["#0d6efd"]
            };

            var chart = new ApexCharts(
              document.querySelector("#chart<?= $row['data_awal_id'] ?>"),
              options
            );

            chart.render();

          });
        </script>
        <h5 class="text-center fw-bold" style="padding-top: -10px; "> <i class="bi bi-graph-up-arrow"></i>
          Realisasi data terakhir dari Target </h5>
        <h5 class="text-center fw-bold text-success">
          <?= number_format($persentase, 2) ?>%</h5>
        <!-- REALISASI -->
        <!-- FOOTER -->
        <div class="project-footer d-flex justify-content-between align-items-center border-top border-3 border-light">
          <div class="small text-muted">
            <i class="bi bi-clock-history me-1"></i>
            Update: <?= $tanggal ?>
          </div>
          <button
            class="btn btn-sm btn-outline-primary rounded-pill px-3"
            data-bs-toggle="modal"
            data-bs-target="#detailModal<?= $row['data_awal_id'] ?>">
            Detail →
          </button>
        </div>

      </div>
    </div>

    <?php
    $id = $row['data_awal_id'];

    $detail = mysqli_query($conn, "SELECT 
ma.nama AS nama_mandor,
ma.foto AS foto_mandor,
ma.alamat,
ma.no_hp,

da.data_awal_id,
da.tahun_tanam,
da.target_pohon,

b.nama_bkph,
b.rph,
b.petak,
b.luas_baku,
b.jenis_tanaman,
b.jarak_tanam,

l.jumlah_lubang,
l.foto_lokasi,
l.tanggal AS tanggal_lubang,
l.catatan AS catatan_lubang,
l.status AS status_lubang,

a.jumlah_ajir,
a.foto_ajir,
a.tanggal AS tanggal_ajir,
a.catatan AS catatan_ajir,
a.status AS status_ajir,

p.jumlah_tanam,
p.foto_tanam,
p.sumber_bibit,
p.tanggal AS tanggal_tanam,
p.catatan AS catatan_tanam,
p.status AS status_tanam,

m.jumlah_hidup,
m.jumlah_mati,
m.gangguan,
m.foto_hidup,
m.foto_mati,
m.tanggal AS tanggal_monitor,
m.catatan AS catatan_monitor,
m.evaluasi,
m.catatan,
m.status AS status_monitor

FROM data_awal da
LEFT JOIN bkph b ON da.bkph_id=b.bkph_id
LEFT JOIN mandor ma ON b.mandor_id=ma.mandor_id
LEFT JOIN lubang l ON l.data_awal_id=da.data_awal_id
LEFT JOIN ajir a ON a.lubang_id=l.lubang_id
LEFT JOIN penanaman p ON p.ajir_id=a.ajir_id
LEFT JOIN monitoring m ON m.tanam_id=p.tanam_id

WHERE da.data_awal_id='$id'
");

    $d = mysqli_fetch_assoc($detail);
    ?>
    <div class="modal fade" id="detailModal<?= $row['data_awal_id'] ?>" tabindex="-1">
      <div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 800px;">

        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

          <div class="modal-body p-0" id="printArea<?= $d['data_awal_id'] ?>">

            <!-- GAMBAR FULL -->
            <div class="position-relative">
              <img src="../assets/images/logos/lokasi.png"
                class="w-100"
                style="height:340px; object-fit:cover;"
                onclick="openImage(this.src)">

              <button class="btn-close position-absolute top-0 end-0 m-3 bg-white"
                data-bs-dismiss="modal"></button>

              <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white"
                style="background:linear-gradient(to top,rgba(0,0,0,0.5),transparent);">
              </div>
            </div>


            <h2 class="fw-bold mb-2 px-4 pt-4">
              <?= $d['nama_bkph'] ?? '-' ?> - RPH <?= $d['rph'] ?? '-' ?> • Petak <?= $d['petak'] ?? '-' ?>
            </h2>
            <!-- ROW UTAMA -->
            <div class="row g-4 px-4 pb-4">
              <!-- KIRI -->
              <div class="col-lg-8">

                <!-- MANDOR -->
                <div class="card border-1 shadow-sm mb-8">
                  <div class="card-body d-flex align-items-center">

                    <img src="<?= !empty($d['foto_mandor']) ? '../image/' . $d['foto_mandor'] : '../assets/images/profile/user1.jpg' ?>"
                      class="rounded-circle me-3 img-fluid"
                      onclick="openImage(this.src)"
                      style="width:70px;height:70px;object-fit:cover;">

                    <div>
                      <h4 class="mb-1 fw-bold"><?= $d['nama_mandor'] ?? '-' ?></h4>

                      <small class="text-muted">
                        <i class="bi bi-telephone"></i> <?= $d['no_hp'] ?? '-' ?> <br>
                        <?= $d['alamat'] ?? '-' ?>

                      </small>
                    </div>

                  </div>
                </div>


                <!-- PROGRESS -->
                <div class="card border-1 shadow-sm mb-4">
                  <div class="card-body">

                    <div class="d-flex justify-content-between mb-2 small">
                      <h5 class="fw-semibold">Progress Tanam</h5>
                      <h5 class="fw-bold"><?= $progress ?>%</h5>
                    </div>

                    <div class="progress" style="height:10px">
                      <div class="progress-bar bg-<?= $progressColor ?>"
                        style="width:<?= $progress ?>%">
                      </div>
                    </div>

                  </div>
                </div>

                <!-- INFORMASI TANAM -->
                <div class="card border-0 shadow-sm bg-light">
                  <div class="card-body">

                    <h6 class="fw-bold mb-4 d-flex align-items-center">
                      <i class="bi bi-info-circle me-2 text-primary"></i>
                      Informasi Tanam
                    </h6>

                    <div class="row g-3">

                      <!-- Tahun Tanam -->
                      <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 stat-box bg-white shadow-sm">
                          <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width:42px;height:42px;">
                            <i class="bi bi-calendar-event"></i>
                          </div>
                          <div>
                            <div class="small text-muted">Tahun Tanam</div>
                            <div class="fw-bold"><?= $d['tahun_tanam'] ?? '-' ?></div>
                          </div>
                        </div>
                      </div>

                      <!-- Target Pohon -->
                      <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-white stat-box shadow-sm">
                          <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3" style="width:42px;height:42px;">
                            <i class="bi bi-bullseye"></i>
                          </div>
                          <div>
                            <div class="small text-muted">Target Pohon</div>
                            <div class="fw-bold"><?= number_format($d['target_pohon'] ?? 0) ?> pohon</div>
                          </div>
                        </div>
                      </div>

                      <!-- Jenis Tanaman -->
                      <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-white stat-box shadow-sm">
                          <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3" style="width:42px;height:42px;">
                            <i class="bi bi-tree"></i>
                          </div>
                          <div>
                            <div class="small text-muted">Jenis Tanaman</div>
                            <div class="fw-bold"><?= $d['jenis_tanaman'] ?? '-' ?></div>
                          </div>
                        </div>
                      </div>

                      <!-- Luas Baku -->
                      <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-white stat-box shadow-sm">
                          <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center me-3" style="width:42px;height:42px;">
                            <i class="bi bi-map"></i>
                          </div>
                          <div>
                            <div class="small text-muted">Luas Baku</div>
                            <div class="fw-bold"><?= $d['luas_baku'] ?? '-' ?> Ha</div>
                          </div>
                        </div>
                      </div>

                      <!-- Jarak Tanam -->
                      <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-white stat-box shadow-sm">
                          <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center me-3" style="width:42px;height:42px;">
                            <i class="bi bi-rulers"></i>
                          </div>
                          <div>
                            <div class="small text-muted">Jarak Tanam</div>
                            <div class="fw-bold"><?= $d['jarak_tanam'] ?? '-' ?></div>
                          </div>
                        </div>
                      </div>

                      <!-- Gangguan -->
                      <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-white stat-box shadow-sm">
                          <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center me-3" style="width:42px;height:42px;">
                            <i class="bi bi-exclamation-triangle"></i>
                          </div>
                          <div>
                            <div class="small text-muted">Gangguan</div>
                            <div class="fw-bold"><?= $d['gangguan'] ?? '-' ?></div>
                          </div>
                        </div>
                      </div>

                    </div>

                  </div>
                </div>

              </div>


              <!-- KANAN -->
              <div class="col-lg-4">


                <!-- TIMELINE -->
                <div class="card bg-light border-0 shadow-sm">
                  <div class="card-body">

                    <h6 class="fw-bold mb-3 d-flex align-items-center">
                      <i class="bi bi-clock-history me-2 text-primary"></i>
                      Timeline Kegiatan
                    </h6>

                    <ul class="list-group list-group-flush bg-light">

                      <!-- LUBANG -->
                      <li class="list-group-item bg-light">
                        <div class="row align-items-center">

                          <div class="col d-flex align-items-center gap-1">
                            <span class="badge rounded-circle bg-success p-3">
                              <i class="bi bi-circle"></i>
                            </span>

                            <span class="fw-semibold">Lubang <br>
                              <?= !empty($d['tanggal_lubang']) ? date('d M Y', strtotime($d['tanggal_lubang'])) : '-' ?>
                            </span>
                          </div>
                        </div>
                      </li>

                      <!-- AJIR -->
                      <li class="list-group-item bg-light">
                        <div class="row align-items-center">

                          <div class="col d-flex align-items-center gap-1">
                            <span class="badge rounded-circle bg-info p-3">
                              <i class="bi bi-geo-alt"></i>
                            </span>

                            <span class="fw-semibold">Ajir<br>
                              <?= !empty($d['tanggal_ajir']) ? date('d M Y', strtotime($d['tanggal_ajir'])) : '-' ?>
                            </span>
                          </div>


                        </div>
                      </li>

                      <!-- TANAM -->
                      <li class="list-group-item bg-light">
                        <div class="row align-items-center">

                          <div class="col d-flex align-items-center gap-1">
                            <span class="badge rounded-circle bg-success p-3">
                              <i class="bi bi-tree"></i>
                            </span>

                            <span class="fw-semibold">Penanaman <br>
                              <?= !empty($d['tanggal_tanam']) ? date('d M Y', strtotime($d['tanggal_tanam'])) : '-' ?>
                            </span>
                          </div>

                        </div>
                      </li>

                      <!-- MONITORING -->
                      <li class="list-group-item bg-light">
                        <div class="row align-items-center">

                          <div class="col d-flex align-items-center gap-1">
                            <span class="badge rounded-circle bg-warning p-3">
                              <i class="bi bi-clipboard-data"></i>
                            </span>

                            <span class="fw-semibold">Monitoring <br>
                              <?= !empty($d['tanggal_monitor']) ? date('d M Y', strtotime($d['tanggal_monitor'])) : '-' ?>
                            </span>
                          </div>

                        </div>
                      </li>

                    </ul>

                  </div>
                </div>
                <!-- MONITORING -->
                <div class="card border-1 shadow-sm rounded-4 mb-1">
                  <div class="card-body">

                    <h6 class="fw-bold mb-3 text-center">Hasil Monitoring
                    </h6>

                    <div class="row g-3 border-bottom border-2 border-light">

                      <div class="col-6">
                        <div class="stat-box bg-light text-center">
                          <div class="stat-number"><?= number_format($d['jumlah_lubang'] ?? 0) ?></div>
                          <div class="stat-label">Lubang</div>
                        </div>
                      </div>

                      <div class="col-6">
                        <div class="stat-box bg-light text-center">
                          <div class="stat-number"><?= number_format($d['jumlah_ajir'] ?? 0) ?></div>
                          <div class="stat-label">Ajir</div>
                        </div>
                      </div>

                      <div class="col-6 mb-2">
                        <div class="stat-box bg-light text-center">
                          <div class="stat-number"><?= number_format($d['jumlah_tanam'] ?? 0) ?></div>
                          <div class="stat-label">Tanam</div>
                        </div>
                      </div>

                      <div class="col-6 mb-2">
                        <div class="stat-box bg-light text-center">
                          <div class="stat-number text-success"><?= number_format($d['jumlah_hidup'] ?? 0) ?></div>
                          <div class="stat-label">Hidup</div>
                        </div>
                      </div>

                    </div>

                    <div class="mt-2 text-center">
                      <div class="text-dark fw-bold">
                        <i class="bi bi-graph-up-arrow me-1"></i>
                        Realisasi Target
                      </div>
                      <div class="fw-bold fs-5 text-success">
                        <?= number_format($persentase, 2) ?>%
                      </div>
                    </div>
                  </div>
                </div>

              </div>

            </div>
            <h6 class="fw-bold mb-2 text-center" style="padding-top: -55px;">
              <i class="bi bi-clipboard-check fs-8"></i>
            </h6>

            <div class="row g-3 px-4">

              <div class="col-md-6">
                <div class="border rounded-3 p-3 text-center h-100">
                  <b class="d-block mb-2">Catatan</b>
                  <p class="text-muted mb-0"><?= $d['catatan'] ?? '-' ?></p>
                </div>
              </div>

              <div class="col-md-6">
                <div class="border rounded-3 p-3 text-center h-100">
                  <b class="d-block mb-2">Evaluasi</b>
                  <p class="text-muted mb-0"><?= $d['evaluasi'] ?? '-' ?></p>
                </div>
              </div>

            </div>


            <!-- ================= DOKUMENTASI ================= -->
            <div class="card border-0 shadow-sm mt-4">
              <div class="card-body">

                <!-- Judul -->

                <h6 class="fw-bold text-center mb-2">
                  <i class="bi bi-camera fs-8 me-2"></i> <br>
                  Dokumentasi Lapangan
                </h6>

                <div class="row g-4">

                  <!-- Lubang -->
                  <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100 text-center dokumentasi-card">
                      <div class="card-body p-2">
                        <img src="<?= !empty($d['foto_lokasi']) ? '../gambar_lubang/' . $d['foto_lokasi'] : '../assets/images/default.jpg' ?>"
                          class="img-fluid rounded dokumentasi-img"
                          onclick="openImage(this.src)">
                        <p class="small fw-semibold mt-2 mb-0">Lubang</p>
                      </div>
                    </div>
                  </div>

                  <!-- Ajir -->
                  <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100 text-center dokumentasi-card">
                      <div class="card-body p-2">
                        <img src="<?= !empty($d['foto_ajir']) ? '../gambar_ajir/' . $d['foto_ajir'] : '../assets/images/default.jpg' ?>"
                          class="img-fluid rounded dokumentasi-img"
                          onclick="openImage(this.src)">
                        <p class="small fw-semibold mt-2 mb-0">Ajir</p>
                      </div>
                    </div>
                  </div>

                  <!-- Tanam -->
                  <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100 text-center dokumentasi-card">
                      <div class="card-body p-2">
                        <img src="<?= !empty($d['foto_tanam']) ? '../gambar_tanam/' . $d['foto_tanam'] : '../assets/images/default.jpg' ?>"
                          class="img-fluid rounded dokumentasi-img"
                          onclick="openImage(this.src)">
                        <p class="small fw-semibold mt-2 mb-0">Penanaman</p>
                      </div>
                    </div>
                  </div>

                  <!-- Monitoring -->
                  <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100 text-center dokumentasi-card">
                      <div class="card-body p-2">
                        <img src="<?= !empty($d['foto_hidup']) ? '../gambar_monitoring/' . $d['foto_hidup'] : '../assets/images/default.jpg' ?>"
                          class="img-fluid rounded dokumentasi-img"
                          onclick="openImage(this.src)">
                        <p class="small fw-semibold mt-2 mb-0">Monitoring</p>
                      </div>
                    </div>
                  </div>

                </div>

              </div>
            </div>

          </div>
          <!-- FOOTER -->
          <div class="modal-footer bg-white">
            <a href="../laporan/export_project.php?data_awal_id=<?= $d['data_awal_id'] ?>"
              target="_blank"
              class="btn btn-success px-4">
              <i class="bi bi-printer"></i> Cetak
            </a>

            <button type="button"
              class="btn btn-outline-secondary px-4"
              data-bs-dismiss="modal">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="lightbox" id="lightbox" onclick="closeImage()">
      <img id="lightbox-img">
    </div>
  <?php endwhile; ?>
</div>