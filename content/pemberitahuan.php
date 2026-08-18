<?php
/** @var mysqli $conn */ // 🔥 biar VS Code tidak merah
$tabel = $_GET['tabel'] ?? 'semua';

$allowedTables = ['semua', 'lubang', 'ajir', 'penanaman', 'monitoring'];

if (!in_array($tabel, $allowedTables)) {
    $tabel = 'semua';
}

if ($tabel == "semua") {

    $queryStatus = mysqli_query($conn, "
SELECT
SUM(pending) AS total_pending,
SUM(verified) AS total_verified,
SUM(rejected) AS total_rejected
FROM (

SELECT
SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) pending,
SUM(CASE WHEN status='verified' THEN 1 ELSE 0 END) verified,
SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END) rejected
FROM lubang

UNION ALL

SELECT
SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END),
SUM(CASE WHEN status='verified' THEN 1 ELSE 0 END),
SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END)
FROM ajir

UNION ALL

SELECT
SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END),
SUM(CASE WHEN status='verified' THEN 1 ELSE 0 END),
SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END)
FROM penanaman

UNION ALL

SELECT
SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END),
SUM(CASE WHEN status='verified' THEN 1 ELSE 0 END),
SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END)
FROM monitoring

) AS data
");
} else {

    $queryStatus = mysqli_query($conn, "
SELECT
SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) AS total_pending,
SUM(CASE WHEN status='verified' THEN 1 ELSE 0 END) AS total_verified,
SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END) AS total_rejected
FROM $tabel
");
}

$dataStatus = mysqli_fetch_assoc($queryStatus);

$pending  = (int)($dataStatus['total_pending'] ?? 0);
$verified = (int)($dataStatus['total_verified'] ?? 0);
$rejected = (int)($dataStatus['total_rejected'] ?? 0);

?>
<div class="row">
    <!-- ranking -->
    <?php
    $notif = mysqli_query($conn, " 
                  SELECT 
                  'lubang' as jenis,
                  l.tanggal,
                  l.catatan,
                  b.nama_bkph,
                  b.rph,
                  b.petak,
                  m.nama as nama_mandor,
                  l.status,
                  l.jumlah_lubang,
                  NULL as jumlah_ajir,
                  NULL as jumlah_tanam,
                  NULL as jumlah_hidup,
                  NULL as jumlah_mati
                  FROM lubang l
                  JOIN data_awal da ON l.data_awal_id = da.data_awal_id
                  JOIN bkph b ON da.bkph_id = b.bkph_id
                  JOIN mandor m ON b.mandor_id = m.mandor_id
                  WHERE l.tanggal >= DATE_SUB(NOW(), INTERVAL 3 MONTH)

                  UNION ALL

                  SELECT 
                  'ajir' as jenis,
                  a.tanggal,
                  a.catatan,
                  b.nama_bkph,
                  b.rph,
                  b.petak,
                  m.nama,
                  a.status,
                  NULL,
                  a.jumlah_ajir,
                  NULL,
                  NULL,
                  NULL
                  FROM ajir a
                  JOIN lubang l ON a.lubang_id = l.lubang_id
                  JOIN data_awal da ON l.data_awal_id = da.data_awal_id
                  JOIN bkph b ON da.bkph_id = b.bkph_id
                  JOIN mandor m ON b.mandor_id = m.mandor_id
                  WHERE a.tanggal >= DATE_SUB(NOW(), INTERVAL 3 MONTH)

                  UNION ALL

                  SELECT 
                  'penanaman' as jenis,
                  p.tanggal,
                  p.catatan,
                  b.nama_bkph,
                  b.rph,
                  b.petak,
                  m.nama,
                  p.status,
                  NULL,
                  NULL,
                  p.jumlah_tanam,
                  NULL,
                  NULL
                  FROM penanaman p
                  JOIN ajir a ON p.ajir_id = a.ajir_id
                  JOIN lubang l ON a.lubang_id = l.lubang_id
                  JOIN data_awal da ON l.data_awal_id = da.data_awal_id
                  JOIN bkph b ON da.bkph_id = b.bkph_id
                  JOIN mandor m ON b.mandor_id = m.mandor_id
                  WHERE p.tanggal >= DATE_SUB(NOW(), INTERVAL 3 MONTH)

                  UNION ALL

                  SELECT 
                  'monitoring' as jenis,
                  mo.tanggal,
                  mo.catatan,
                  b.nama_bkph,
                  b.rph,
                  b.petak,
                  m.nama,
                  mo.status,
                  NULL,
                  NULL,
                  NULL,
                  mo.jumlah_hidup,
                  mo.jumlah_mati
                  FROM monitoring mo
                  JOIN penanaman p ON mo.tanam_id = p.tanam_id
                  JOIN ajir a ON p.ajir_id = a.ajir_id
                  JOIN lubang l ON a.lubang_id = l.lubang_id
                  JOIN data_awal da ON l.data_awal_id = da.data_awal_id
                  JOIN bkph b ON da.bkph_id = b.bkph_id
                  JOIN mandor m ON b.mandor_id = m.mandor_id
                  WHERE mo.tanggal >= DATE_SUB(NOW(), INTERVAL 3 MONTH)

                  ORDER BY tanggal DESC
                  LIMIT 15
                  ");
    $jumlahNotif = mysqli_num_rows($notif);
    ?>
    <div class="col-lg-6 d-flex">
        <div class="card w-100 shadow-sm">
            <div class="card-body p-4">

                <!-- HEADER -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-bell-fill text-primary"></i>
                        Notifikasi Kegiatan
                    </h5>

                    <?php if ($jumlahNotif > 0) { ?>
                        <span class="badge bg-primary fs-6"><?= $jumlahNotif ?></span>
                    <?php } ?>
                </div>

                <!-- LIST -->
                <div style="max-height:520px; overflow-y:auto;">

                    <?php while ($n = mysqli_fetch_assoc($notif)) { ?>

                        <?php
                        if ($n['jenis'] == "penanaman") {
                            $icon = "bi-tree-fill";
                            $bg = "bg-success-subtle";
                            $judul = "Penanaman";
                        } elseif ($n['jenis'] == "ajir") {
                            $icon = "bi-stickies-fill";
                            $bg = "bg-warning-subtle";
                            $judul = "Ajir";
                        } elseif ($n['jenis'] == "lubang") {
                            $icon = "bi-circle";
                            $bg = "bg-secondary-subtle";
                            $judul = "Lubang";
                        } elseif ($n['jenis'] == "monitoring") {
                            $icon = "bi-clipboard-data";
                            $bg = "bg-primary-subtle";
                            $judul = "Monitoring";
                        }
                        ?>

                        <div class="border rounded-3 p-3 mb-3">

                            <div class="d-flex justify-content-between">

                                <!-- kiri -->
                                <div class="d-flex">

                                    <div class="me-3">
                                        <div class="<?= $bg ?> rounded-circle d-flex align-items-center justify-content-center"
                                            style="width:45px;height:45px;">
                                            <i class="bi <?= $icon ?> fs-5"></i>
                                        </div>
                                    </div>

                                    <div>

                                        <div class="fw-semibold">
                                            <?= $judul ?> —
                                            BKPH <?= $n['nama_bkph'] ?>
                                            | RPH <?= $n['rph'] ?>
                                            | Petak <?= $n['petak'] ?>
                                        </div>

                                        <div class="small text-muted">

                                            <?php
                                            if ($n['jenis'] == "penanaman") {

                                        echo "Kegiatan penanaman dilakukan oleh mandor 
                                        <b>{$n['nama_mandor']}</b> dengan jumlah tanam 
                                        <b>{$n['jumlah_tanam']}</b>.";
                                            } elseif ($n['jenis'] == "ajir") {

                                                echo "Pemasangan ajir dilakukan oleh mandor 
                                        <b>{$n['nama_mandor']}</b> dengan jumlah ajir 
                                        <b>{$n['jumlah_ajir']}</b>.";
                                            } elseif ($n['jenis'] == "lubang") {

                                                echo "Pembuatan lubang dilakukan oleh mandor 
                                        <b>{$n['nama_mandor']}</b> dengan jumlah lubang 
                                        <b>{$n['jumlah_lubang']}</b>.";
                                        } elseif ($n['jenis'] == "monitoring") {

                                                echo "Monitoring tanaman oleh mandor 
                                        <b>{$n['nama_mandor']}</b> dengan hasil 
                                        <span class='text-success'>hidup {$n['jumlah_hidup']}</span> 
                                        dan 
                                        <span class='text-danger'>mati {$n['jumlah_mati']}</span>.";
                                            }

                                            ?>
                                        </div>

                                        <div class="small text-muted">
                                            Catatan : <?= $n['catatan'] ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end d-flex flex-column justify-content-center"
                                    style="min-width:120px;">

                                    <div class="mb-1">
                                        <?php
                                        if ($n['status'] == 'verified') {
                                            echo "<span class='badge bg-success'>Verified</span>";
                                        } elseif ($n['status'] == 'pending') {
                                            echo "<span class='badge bg-warning text-dark'>Pending</span>";
                                        } else {
                                            echo "<span class='badge bg-danger'>Rejected</span>";
                                        }
                                        ?>
                                    </div>

                                    <div class="small text-muted">
                                        <i class="bi bi-calendar3"></i>
                                        <?= date('d M Y', strtotime($n['tanggal'])) ?>
                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php } ?>

                </div>

            </div>
        </div>
    </div>



    <div class="col-lg-6">
        <div class="card border-0 shadow-sm w-100" style="height:200px;"> <!-- ATUR TINGGI DISINI -->

            <div class="card-body h-100 d-flex align-items-center px-4">

                <!-- Logo -->
                <img src="../assets/images/logos/logo-m.png"
                    alt="Logo Perum Perhutani"
                    style="width:150px;"
                    class="me-4">

                <!-- Divider -->
                <div class="vr me-4" style="height:80%;"></div>

                <!-- Text -->
                <div>
                    <h2 class="fw-bold mb-1"
                        style="font-family:'Manrope',sans-serif; letter-spacing:1px; line-height:1.1;">
                        Perum Perhutani <br> KPH Parengan
                    </h2>

                    <div class="text-muted small">
                        • Mengelola Hutan dengan Integritas <br> • Harmoni Bersama Masyarakat
                    </div>
                </div>

            </div>

        </div>

       <div class="card shadow-sm border-0">
  <div class="card-body">

    <!-- HEADER -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">

      <h5 class="fw-bold mb-0">
        Status Kegiatan
      </h5>

      <form method="GET" action="index.php">
        <input type="hidden" name="p" value="pemberitahuan">

        <select name="tabel"
          class="form-select form-select-sm"
          onchange="this.form.submit()">

          <option value="semua" <?= ($tabel == "semua") ? "selected" : "" ?>>Semua Kegiatan</option>
          <option value="lubang" <?= ($tabel == "lubang") ? "selected" : "" ?>>Lubang</option>
          <option value="ajir" <?= ($tabel == "ajir") ? "selected" : "" ?>>Ajir</option>
          <option value="penanaman" <?= ($tabel == "penanaman") ? "selected" : "" ?>>Penanaman</option>
          <option value="monitoring" <?= ($tabel == "monitoring") ? "selected" : "" ?>>Monitoring</option>

        </select>
      </form>
    </div>

    <div class="row align-items-center">

      <!-- CHART -->
      <div class="col-12 col-lg-6 mb-4 mb-lg-0">
        <div id="chartStatus" style="height:260px;"></div>
      </div>

      <!-- DATA -->
      <div class="col-12 col-lg-6">
        <div class="d-flex flex-column gap-3">

          <!-- ITEM -->
          <?php
          function itemStatus($icon, $color, $title, $desc, $value, $textClass = '') {
          ?>
          <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border bg-light">

            <div class="d-flex align-items-center gap-3">

              <div class="rounded-circle d-flex align-items-center justify-content-center">
                <iconify-icon icon="<?= $icon ?>" width="40" style="color:<?= $color ?>;"></iconify-icon>
              </div>

              <div>
                <div class="fw-semibold text-dark fs-6 fs-md-5">
                  <?= $title ?>
                </div>
                <small class="text-muted">
                  <?= $desc ?>
                </small>
              </div>

            </div>

            <div class="text-end">
              <div class="fw-bold fs-6 <?= $textClass ?>">
                <?= number_format($value, 0, ',', '.') ?>
              </div>
            </div>

          </div>
          <?php } ?>

          <?php
          itemStatus("solar:clock-circle-bold", "#FFC107", "Pending", "Menunggu verifikasi", $pending);
          itemStatus("solar:check-circle-bold", "#4CAF50", "Verified", "Data disetujui", $verified, "text-success");
          itemStatus("solar:close-circle-bold", "#F44336", "Rejected", "Data ditolak", $rejected, "text-danger");
          ?>

        </div>
      </div>

    </div>
  </div>
</div>


    </div>
</div>
</div>


</div>