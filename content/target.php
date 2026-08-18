<?php

/** @var mysqli $conn */ // 🔥 biar VS Code tidak merah
$tahun = $_GET['tahun'] ?? '';

$where = "";

if (!empty($tahun)) {
    $tahun = mysqli_real_escape_string($conn, $tahun);
    $where = "WHERE tahun_tanam = '$tahun'";
}
/* =========================
   TOTAL TARGET POHON
========================= */
$q_target = mysqli_query($conn, "SELECT IFNULL(SUM(target_pohon),0) as total FROM data_awal $where");
$d_target = mysqli_fetch_assoc($q_target);
$total_target = $d_target['total'];


/* =========================
   TOTAL LUBANG
========================= */
$q_lubang = mysqli_query($conn, "
SELECT IFNULL(SUM(l.jumlah_lubang),0) as total
FROM lubang l
JOIN data_awal da ON l.data_awal_id = da.data_awal_id
$where
");
$d_lubang = mysqli_fetch_assoc($q_lubang);
$total_lubang = $d_lubang['total'];


/* =========================
   TOTAL AJIR
========================= */
$q_ajir = mysqli_query($conn, "
SELECT IFNULL(SUM(a.jumlah_ajir),0) as total
FROM ajir a
JOIN lubang l ON a.lubang_id = l.lubang_id
JOIN data_awal da ON l.data_awal_id = da.data_awal_id
$where
");
$d_ajir = mysqli_fetch_assoc($q_ajir);
$total_ajir = $d_ajir['total'];


/* =========================
   TOTAL TANAM
========================= */
$q_tanam = mysqli_query($conn, "
SELECT IFNULL(SUM(p.jumlah_tanam),0) as total
FROM penanaman p
JOIN ajir a ON p.ajir_id = a.ajir_id
JOIN lubang l ON a.lubang_id = l.lubang_id
JOIN data_awal da ON l.data_awal_id = da.data_awal_id
$where
");
$d_tanam = mysqli_fetch_assoc($q_tanam);
$total_tanam = $d_tanam['total'];


/* =========================
   TOTAL POHON HIDUP
========================= */
$q_hidup = mysqli_query($conn, "
SELECT IFNULL(SUM(m.jumlah_hidup),0) as total
FROM monitoring m
JOIN penanaman p ON m.tanam_id = p.tanam_id
JOIN ajir a ON p.ajir_id = a.ajir_id
JOIN lubang l ON a.lubang_id = l.lubang_id
JOIN data_awal da ON l.data_awal_id = da.data_awal_id
$where
");
$d_hidup = mysqli_fetch_assoc($q_hidup);
$total_hidup = $d_hidup['total'];


/* =========================
   FUNCTION PERSENTASE
========================= */
function persen($nilai, $target)
{
    if ($target == 0) {
        return 0;
    }

    return min(100, round(($nilai / $target) * 100, 2));
}
function warna_persen($persen)
{
    if ($persen < 50) {
        return "text-danger";   // merah
    } elseif ($persen <= 80) {
        return "text-warning";  // kuning
    } else {
        return "text-success";  // hijau
    }
}

/* =========================
   HITUNG PERSEN KEGIATAN
========================= */
$p_lubang = persen($total_lubang, $total_target);
$p_ajir   = persen($total_ajir, $total_target);
$p_tanam  = persen($total_tanam, $total_target);
$p_hidup  = persen($total_hidup, $total_tanam);


/* =========================
   SURVIVAL RATE
   (hidup dibanding tanam)
========================= */
$survival_rate = persen($total_hidup, $total_tanam);

?>


<div class="border-0">
    <div class="p-0">

        <!-- HEADER -->
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">

            <H2 class="mb-0 fw-bold">
                Target Penanaman
            </H2>

            <form method="GET" action="index.php">

                <input type="hidden" name="p" value="target">

    
                <!-- SELECT -->
                <select name="tahun" class="form-select bg-white mb-3" onchange="this.form.submit()">

                    <option value="">Semua Tahun</option>

                    <?php
                    $tahunQ = mysqli_query($conn, "SELECT DISTINCT tahun_tanam FROM data_awal ORDER BY tahun_tanam DESC");
                    while ($t = mysqli_fetch_assoc($tahunQ)) {
                        $selected = ($_GET['tahun'] ?? '') == $t['tahun_tanam'] ? 'selected' : '';
                        echo "<option value='{$t['tahun_tanam']}' $selected>{$t['tahun_tanam']}</option>";
                    }
                    ?>

                </select>
            </form>

        </div>

        <div class="row text-center g-0">

            <!-- TARGET -->
            <div class="col-md-4 border-end border-3 border-bottom p-4">

                <i class="ti ti-target text-primary mb-2" style="font-size:38px;"></i>

                <h2 class="fw-bold mb-0" style="font-size:38px;">
                    <?= number_format($total_target, 0, ',', '.') ?>
                </h2>

                <p class="text-dark mb-0 small">Target Pohon</p>

            </div>


            <!-- LUBANG -->
            <div class="col-md-4 border-end border-3 border-bottom p-4">

                <i class="ti ti-shovel text-info mb-2" style="font-size:38px;"></i>

                <h2 class="fw-bold mb-0" style="font-size:38px;">
                    <?= number_format($total_lubang, 0, ',', '.') ?>
                </h2>

                <p class="text-dark mb-0 small">Total Lubang</p>

            </div>


            <!-- AJIR -->
            <div class="col-md-4 border-3 border-bottom p-4">

                <i class="ti ti-map-pin text-warning mb-2" style="font-size:38px;"></i>

                <h2 class="fw-bold mb-0" style="font-size:38px;">
                    <?= number_format($total_ajir, 0, ',', '.') ?>
                </h2>

                <p class="text-dark mb-0 small">Total Ajir</p>

            </div>


            <!-- TANAM -->
            <div class="col-md-4 border-3 border-end p-4">

                <i class="ti ti-plant text-success mb-2" style="font-size:38px;"></i>

                <h2 class="fw-bold mb-0" style="font-size:38px;">
                    <?= number_format($total_tanam, 0, ',', '.') ?>
                </h2>

                <p class="text-dark mb-0 small">Total Tanam</p>

            </div>


            <!-- HIDUP -->
            <div class="col-md-4 border-3 border-end p-4">

                <i class="ti ti-leaf text-success mb-2" style="font-size:38px;"></i>

                <h2 class="fw-bold mb-0" style="font-size:38px;">
                    <?= number_format($total_hidup, 0, ',', '.') ?>
                </h2>

                <p class="text-dark mb-0 small">Pohon Hidup</p>

            </div>


            <!-- SURVIVAL -->
            <div class="col-md-4 p-4">

                <i class="ti ti-chart-pie text-primary mb-2" style="font-size:38px;"></i>

                <h2 class="fw-bold mb-0 <?= warna_persen($survival_rate) ?>" style="font-size:38px;">
                    <?= $survival_rate ?>%
                </h2>

                <p class="text-dark mb-0 small">Survival Rate</p>

            </div>

        </div>
    </div>
</div>

<div class="row">

    <div class="col-lg-12">
        <div class="card w-100 mt-4">
            <div class="card-body">

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="card-title fw-semibold">
                        Progress Kegiatan Penanaman
                    </h5>
                </div>

                <div id="progressChart"></div>

            </div>
        </div>
    </div>

</div>