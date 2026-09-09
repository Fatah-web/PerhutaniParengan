<?php
/** @var mysqli $conn */ // 🔥 biar VS Code tidak merah
$tahun = $_GET['tahun'] ?? 'all';
$whereTahun = ($tahun == 'all') ? "" : "WHERE da.tahun_tanam = '$tahun'";

$query = mysqli_query($conn, "
SELECT 
    b.nama_bkph,

    SUM(DISTINCT da.target_pohon) AS total_target,

    SUM(p.jumlah_tanam) AS total_realisasi,

    SUM(m.jumlah_hidup) AS total_hidup,

    SUM(m.jumlah_mati) AS total_mati,

    SUM(p.jumlah_tanam) AS total_tanam

FROM data_awal da
LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
LEFT JOIN lubang l ON da.data_awal_id = l.data_awal_id
LEFT JOIN ajir a ON l.lubang_id = a.lubang_id
LEFT JOIN penanaman p ON a.ajir_id = p.ajir_id
LEFT JOIN monitoring m ON p.tanam_id = m.tanam_id
$whereTahun



GROUP BY b.nama_bkph
ORDER BY b.nama_bkph ASC
");

$bkph = [];
$target = [];
$realisasi = [];
$hidup = [];
$mati = [];
$totalTanam = [];

while ($row = mysqli_fetch_assoc($query)) {
    $bkph[] = $row['nama_bkph'];
    $target[] = (int) ($row['total_target'] ?? 0);
    $realisasi[] = (int) ($row['total_realisasi'] ?? 0);
    $hidup[] = (int) ($row['total_hidup'] ?? 0);
    $mati[] = (int) ($row['total_mati'] ?? 0);
    $totalTanam[] = (int) ($row['total_tanam'] ?? 0);
}
$queryRank = mysqli_query($conn, "
SELECT 
    b.nama_bkph,
    SUM(da.target_pohon) AS total_target,
    SUM(p.jumlah_tanam) AS total_realisasi
FROM data_awal da
LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
LEFT JOIN lubang l ON da.data_awal_id = l.data_awal_id
LEFT JOIN ajir a ON l.lubang_id = a.lubang_id
LEFT JOIN penanaman p ON a.ajir_id = p.ajir_id
$whereTahun
GROUP BY b.nama_bkph
ORDER BY total_realisasi DESC
");
?>

<script>
    const bkphData = <?= json_encode($bkph) ?>;
    const targetData = <?= json_encode($target) ?>;
    const realisasiData = <?= json_encode($realisasi) ?>;
    const hidupData = <?= json_encode($hidup) ?>;
    const matiData = <?= json_encode($mati) ?>;
    const totalTanamData = <?= json_encode($totalTanam) ?>;
</script>

<?php

// ==========================================
// TOTAL STATISTIK TANAMAN
// ==========================================

// TOTAL TARGET
$queryTarget = mysqli_query($conn, "
    SELECT
        COALESCE(SUM(da.target_pohon), 0) AS total_target
    FROM data_awal da
    $whereTahun
");

$dataTarget = mysqli_fetch_assoc($queryTarget);

$totalTarget = (int) ($dataTarget['total_target'] ?? 0);


// TOTAL REALISASI TANAM
$queryRealisasi = mysqli_query($conn, "
    SELECT
        COALESCE(SUM(p.jumlah_tanam), 0) AS total_realisasi
    FROM data_awal da

    LEFT JOIN lubang l
        ON da.data_awal_id = l.data_awal_id

    LEFT JOIN ajir a
        ON l.lubang_id = a.lubang_id

    LEFT JOIN penanaman p
        ON a.ajir_id = p.ajir_id

    $whereTahun
");

$dataRealisasi = mysqli_fetch_assoc($queryRealisasi);

$totalRealisasi = (int) ($dataRealisasi['total_realisasi'] ?? 0);


// ==========================================
// GAP
// ==========================================

$totalGap = max(
    0,
    $totalTarget - $totalRealisasi
);

?>
<script>

    const dashboardData = {
        target: <?= $totalTarget ?>,
        realisasi_tanam: <?= $totalRealisasi ?>,
        gap: <?= $totalGap ?>
    };

</script>
<?php
/* ===== Chart Tanaman ===== */
$queryTotal = mysqli_query($conn, "
                        SELECT 
                            SUM(p.jumlah_tanam) AS total_tanam,
                            SUM(m.jumlah_hidup) AS total_hidup,
                            SUM(m.jumlah_mati) AS total_mati
                        FROM data_awal da
                        LEFT JOIN lubang l ON da.data_awal_id = l.data_awal_id
                        LEFT JOIN ajir a ON l.lubang_id = a.lubang_id
                        LEFT JOIN penanaman p ON a.ajir_id = p.ajir_id
                        LEFT JOIN monitoring m ON p.tanam_id = m.tanam_id
                       $whereTahun
                        ");

$total = mysqli_fetch_assoc($queryTotal);

$jumlah_tanaman = (int) ($total['total_tanam'] ?? 0);
$pohon_hidup = (int) ($total['total_hidup'] ?? 0);
$pohon_mati = (int) ($total['total_mati'] ?? 0);

/* ===== Chart Pie ===== */
$tabel = $_GET['tabel'] ?? 'monitoring';

/* ===== WHITELIST (WAJIB) ===== */
$allowedTables = ['lubang', 'ajir', 'penanaman', 'monitoring'];

if (!in_array($tabel, $allowedTables)) {
    $tabel = 'monitoring';
}

/* ===== QUERY STATUS ===== */
$queryStatus = mysqli_query($conn, "
                        SELECT 
                            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS total_pending,
                            SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) AS total_verified,
                            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS total_rejected
                        FROM $tabel
                    ");

$dataStatus = mysqli_fetch_assoc($queryStatus);

$total_pending = (int) ($dataStatus['total_pending'] ?? 0);
$total_verified = (int) ($dataStatus['total_verified'] ?? 0);
$total_rejected = (int) ($dataStatus['total_rejected'] ?? 0);


?>

<div class="col-lg-12">
    <div class="card">
        <div class="card-body py-3 px-4">
            <form method="GET" action="index.php" class="d-flex align-items-center justify-content-between flex-wrap">
                <!-- WAJIB supaya tetap di dashboard_adminn -->
                <input type="hidden" name="p" value="analisis">
                <!-- KIRI: JUDUL -->
                <div class="d-flex align-items-center"> <i class="ti ti-calendar me-2 text-primary fs-5"></i>
                    <h5 class="mb-0 fw-semibold">Filter Tahun</h5>
                </div> <!-- TENGAH: SELECT -->
                <div style="width:200px;"> <select name="tahun" class="form-select" onchange="this.form.submit()">
                        <!-- OPTION ALL -->
                        <option value="all" <?= ($tahun == 'all') ? 'selected' : '' ?>> Semua Tahun

                        </option> <?php $queryTahun = mysqli_query($conn, " SELECT DISTINCT tahun_tanam FROM data_awal ORDER BY tahun_tanam DESC ");
                        while ($rowTahun = mysqli_fetch_assoc($queryTahun)) {
                            $th = $rowTahun['tahun_tanam'];
                            $selected = ($tahun == $th) ? 'selected' : '';
                            echo "<option value='$th' $selected>$th</option>";
                        } ?>
                    </select> </div> <!-- KANAN: INFO -->
                <div> <span class="text-muted small"> Data berdasarkan tahun tanam </span> </div>
            </form>
        </div>
    </div>
</div>
<div class="row">
    <!-- ranking -->
    <div class="col-lg-5 d-flex">
        <div class="card w-100 shadow-sm border-0">
            <div class="card-body p-4">

                <h5 class="fw-bold mb-3">
                    Ranking Jumlah Tanam BKPH
                </h5>

                <!-- container scroll -->
                <div style="max-height:220px; overflow-y:auto;">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th style="width:70px">Rank</th>
                                <th>BKPH</th>
                                <th class="text-end">Target</th>
                                <th class="text-end">Realisasi</th>
                                <th style="width:120px">Progress</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            $rank = 1;
                            while ($row = mysqli_fetch_assoc($queryRank)):

                                $target = $row['total_target'] ?? 0;
                                $realisasi = $row['total_realisasi'] ?? 0;

                                $persen = ($target > 0) ? min(100, round(($realisasi / $target) * 100)) : 0;

                                /* warna progress */
                                if ($persen <= 20)
                                    $warna = "bg-danger";
                                elseif ($persen <= 40)
                                    $warna = "bg-warning";
                                elseif ($persen <= 60)
                                    $warna = "bg-info";
                                elseif ($persen <= 80)
                                    $warna = "bg-primary";
                                else
                                    $warna = "bg-success";
                                ?>

                                <tr>

                                    <!-- ranking -->
                                    <td class="fw-bold">
                                        <?php
                                        if ($rank == 1) {
                                            echo '<i class="bi bi-trophy-fill text-warning"></i>';
                                        } elseif ($rank == 2) {
                                            echo '<i class="bi bi-award-fill text-secondary"></i>';
                                        } elseif ($rank == 3) {
                                            echo '<i class="bi bi-award text-dark"></i>';
                                        }
                                        ?>
                                        <?= $rank++ ?>
                                    </td>

                                    <!-- bkph -->
                                    <td class="fw-semibold">
                                        <?= $row['nama_bkph'] ?>
                                    </td>

                                    <!-- target -->
                                    <td class="text-end">
                                        <?= number_format($target, 0, ',', '.') ?>
                                    </td>

                                    <!-- realisasi -->
                                    <td class="text-end fw-semibold">
                                        <?= number_format($realisasi, 0, ',', '.') ?>
                                    </td>

                                    <!-- progress -->
                                    <td>

                                        <div class="progress" style="height:8px;">
                                            <div class="progress-bar <?= $warna ?>" style="width: <?= $persen ?>%">
                                            </div>
                                        </div>

                                        <small class="text-muted">
                                            <?= $persen ?>%
                                        </small>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            </div>
        </div>
    </div>

    <!-- STATISTIK TANAMAN -->
    <div class="col-lg-7 d-flex">
        <div class="card overflow-hidden w-100">
            <div class="card-body p-6">

                <h5 class="card-title mb-3 fw-semibold">
                    Statistik GAP Tanam
                </h5>

                <div class="row align-items-center">

                    <!-- DONUT -->
                    <div class="col-5">
                        <div class="d-flex justify-content-center">
                            <div id="chartTanamanGAP"></div>
                        </div>
                    </div>


                    <!-- INFORMASI -->
                    <div class="col-12 col-md-7">

                        <!-- TOTAL TARGET -->
                        <h2 class="fw-bold mb-1" style="font-size:38px;">
                            <?= number_format($totalTarget ?? 0, 0, ',', '.'); ?> Pohon
                        </h2>

                        <p class="text-muted fs-4 mb-3">
                            Target Tanaman
                        </p>


                        <!-- PERSENTASE REALISASI -->
                        <div class="d-flex align-items-center mb-4 flex-wrap">

                            <span
                                class="me-2 rounded-circle bg-light-success round-25 d-flex align-items-center justify-content-center"
                                id="tanamanIconiGAP" style="font-size:30px;width:42px;height:42px;">
                            </span>

                            <p class="text-success fw-bold me-2 mb-0" id="tanamanValueGAP" style="font-size:36px;">
                                0%
                            </p>

                            <p class="fs-5 text-muted mb-0">
                                Realisasi
                            </p>

                        </div>


                        <!-- LEGEND -->
                        <div class="d-flex align-items-center flex-wrap gap-4">

                            <!-- REALISASI TANAM -->
                            <div class="text-center">

                                <div class="fw-bold fs-3">
                                    <?= number_format($totalRealisasi ?? 0, 0, ',', '.'); ?>
                                </div>

                                <div class="d-flex align-items-center justify-content-center mt-1">

                                    <span class="rounded-circle me-2"
                                        style="width:16px;height:16px;background:#00E396;">
                                    </span>

                                    <span class="fs-3">
                                        Realisasi Tanam
                                    </span>

                                </div>

                            </div>


                            <!-- GAP -->
                            <div class="text-center">

                                <div class="fw-bold fs-3">
                                    <?= number_format($totalGap ?? 0, 0, ',', '.'); ?>
                                </div>

                                <div class="d-flex align-items-center justify-content-center mt-1">

                                    <span class="rounded-circle me-2"
                                        style="width:16px;height:16px;background:#FF4560;">
                                    </span>

                                    <span class="fs-3">
                                        Gap
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

</div>
<div class="row">

    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-body">

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="card-title fw-semibold">
                        Grafik Realisasi Penanaman
                    </h5>
                </div>

                <div id="chartTanam12"></div>

            </div>
        </div>
    </div>

</div>