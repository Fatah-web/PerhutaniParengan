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
                    Ranking BKPH
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

                <h5 class="card-title mb-3 fw-semibold">Statistik Tanaman</h5>

                <div class="row align-items-center">

                    <!-- DONUT -->
                    <div class="col-5">
                        <div class="d-flex justify-content-center">
                            <div id="chartTanaman"></div>
                        </div>
                    </div>

                    <div class="col-12 col-md-7">

                        <!-- TOTAL TANAMAN -->
                        <h2 class="fw-bold mb-1" style="font-size:38px;">
                            <?= number_format($jumlah_tanaman ?? 0, 0, ',', '.'); ?> Pohon
                        </h2>

                        <p class="text-muted fs-4 mb-3">Jumlah Tanaman</p>

                        <!-- PERSENTASE HIDUP -->
                        <div class="d-flex align-items-center mb-4 flex-wrap">

                            <span
                                class="me-2 rounded-circle bg-light-success round-25 d-flex align-items-center justify-content-center"
                                id="tanamanIconi" style="font-size:30px;width:42px;height:42px;">
                            </span>

                            <p class="text-success fw-bold me-2 mb-0" id="tanamanValue" style="font-size:36px;">
                                0%
                            </p>

                            <p class="fs-5 text-muted mb-0">Survival Rate</p>

                        </div>

                        <!-- LEGEND -->
                        <div class="d-flex align-items-center flex-wrap gap-4">

                            <!-- Pohon Hidup -->
                            <div class="text-center">
                                <div class="fw-bold fs-3">
                                    <?= number_format($pohon_hidup ?? 0, 0, ',', '.'); ?>
                                </div>

                                <div class="d-flex align-items-center justify-content-center mt-1">
                                    <span class="rounded-circle me-2"
                                        style="width:16px;height:16px;background:#00E396;"></span>
                                    <span class="fs-3">Pohon Hidup</span>
                                </div>
                            </div>

                            <!-- Pohon Mati -->
                            <div class="text-center">
                                <div class="fw-bold fs-3">
                                    <?= number_format($pohon_mati ?? 0, 0, ',', '.'); ?>
                                </div>

                                <div class="d-flex align-items-center justify-content-center mt-1">
                                    <span class="rounded-circle me-2"
                                        style="width:16px;height:16px;background:#FF4560;"></span>
                                    <span class="fs-3">Pohon Mati</span>
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

<script>
    document.addEventListener("DOMContentLoaded", function () {

        const el = document.getElementById("chartTanam12");
        if (!el) return;


        // ==========================================
        // HITUNG GAP
        // ==========================================
        const gapData = targetData.map((target, index) => {

            const targetValue = Number(target) || 0;
            const realisasiValue = Number(realisasiData[index]) || 0;

            return Math.max(0, targetValue - realisasiValue);

        });


        // ==========================================
        // HITUNG PERSENTASE PROGRESS
        // ==========================================
        const persentaseData = targetData.map((target, index) => {

            const targetValue = Number(target) || 0;
            const realisasiValue = Number(realisasiData[index]) || 0;

            return targetValue > 0
                ? Number(
                    ((realisasiValue / targetValue) * 100).toFixed(1)
                )
                : 0;

        });


        // ==========================================
        // CHART OPTIONS
        // ==========================================
        const options = {

            // ======================================
            // SERIES
            // ======================================
            series: [

                // ----------------------------------
                // TARGET
                // ----------------------------------
                {
                    name: "Target Pohon",
                    type: "column",
                    data: targetData,
                    group: "target"
                },


                // ----------------------------------
                // REALISASI
                // ----------------------------------
                {
                    name: "Realisasi Tanam",
                    type: "column",
                    data: realisasiData,
                    group: "progress"
                },


                // ----------------------------------
                // GAP
                // ----------------------------------
                {
                    name: "Gap",
                    type: "column",
                    data: gapData,
                    group: "progress"
                },


                // ----------------------------------
                // LINE PROGRESS
                // ----------------------------------
                {
                    name: "Progress",
                    type: "line",
                    data: persentaseData
                }

            ],


            // ==========================================
            // CHART
            // ==========================================
            chart: {
                type: "line",
                height: 480,
                stacked: true,
                stackOnlyBar: true,

                toolbar: {
                    show: true,
                    offsetX: -5,
                    offsetY: 0,

                    tools: {
                        download: true,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false
                    }
                },


                animations: {
                    enabled: true,
                    easing: "easeinout",
                    speed: 1000
                }
            },


            // ==========================================
            // LEGEND
            // ==========================================
            legend: {
                position: "top",
                horizontalAlign: "center",

                fontSize: "12px",
                fontWeight: 500,

                labels: {
                    colors: "#475569"
                },

                markers: {
                    width: 9,
                    height: 9,
                    radius: 4
                },

                itemMargin: {
                    horizontal: 12,
                    vertical: 4
                }
            },


            // ==========================================
            // BAR
            // ==========================================
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: "50%",
                    borderRadius: 8,
                    borderRadiusApplication: "around",
                    borderRadiusWhenStacked: "last"
                }
            },

            // ==========================================
            // DATA LABEL
            // ==========================================
            dataLabels: {

                enabled: false

            },


            // ==========================================
            // X AXIS
            // ==========================================
            xaxis: {
                categories: bkphData,

                axisBorder: {
                    show: false
                },

                axisTicks: {
                    show: false
                },

                labels: {
                    style: {
                        colors: "#64748B",
                        fontSize: "12px",
                        fontWeight: 500
                    }
                }
            },

            // ==========================================
            // Y AXIS
            // ==========================================
            yaxis: [

                // --------------------------------------
                // Y AXIS KIRI
                // JUMLAH POHON
                // --------------------------------------
                {
                    seriesName: [
                        "Target Pohon",
                        "Realisasi Tanam",
                        "Gap"
                    ],

                    min: 0,

                    labels: {
                        style: {
                            colors: "#64748B",
                            fontSize: "11px"
                        },

                        formatter: function (value) {
                            return Math.round(value)
                                .toLocaleString("id-ID");
                        }
                    },

                    title: {
                        text: "Jumlah Pohon",
                        style: {
                            color: "#64748B",
                            fontSize: "12px",
                            fontWeight: 600
                        }
                    }
                },

                // --------------------------------------
                // Y AXIS KANAN
                // PERSENTASE
                // --------------------------------------
                {
                    seriesName: "Progress",

                    opposite: true,

                    min: 0,
                    max: 100,
                    tickAmount: 5,

                    labels: {
                        style: {
                            colors: "#6366F1",
                            fontSize: "11px",
                            fontWeight: 600
                        },

                        formatter: function (value) {
                            return value.toFixed(0) + "%";
                        }
                    },

                    title: {
                        text: "Progress",
                        style: {
                            color: "#6366F1",
                            fontSize: "12px",
                            fontWeight: 600
                        }
                    }
                }

            ],


            // ==========================================
            // WARNA
            // ==========================================
            colors: [
                "#60A5FA", // Target
                "#10B981", // Realisasi
                "#FBBF24", // Gap
                "#6366F1"  // Progress
            ],


            // ==========================================
            // STROKE
            // ==========================================
            stroke: {
                width: [
                    0,
                    0,
                    0,
                    3
                ],
                curve: "smooth",
                lineCap: "round"
            },
            states: {
                hover: {
                    filter: {
                        type: "lighten",
                        value: 0.05
                    }
                }
            },


            // ==========================================
            // MARKER LINE
            // ==========================================
            markers: {
                size: 5,

                strokeWidth: 3,

                strokeColors: "#ffffff",

                hover: {
                    size: 7
                }
            },



            // ==========================================
            // FILL
            // ==========================================
            fill: {

                opacity: [
                    1,
                    1,
                    1,
                    1
                ]

            },


            // ==========================================
            // GRID
            // ==========================================
            grid: {
                borderColor: "#E2E8F0",
                strokeDashArray: 3,

                padding: {
                    top: 5,
                    right: 15,
                    left: 10,
                    bottom: 5
                },

                xaxis: {
                    lines: {
                        show: false
                    }
                }
            },


            // ==========================================
            // TOOLTIP
            // ==========================================

            // ======================================
            // TOOLTIP
            // ======================================
            tooltip: {

                shared: false,

                intersect: true,

                custom: function ({
                    dataPointIndex
                }) {

                    const target =
                        Number(targetData[dataPointIndex]) || 0;

                    const realisasi =
                        Number(realisasiData[dataPointIndex]) || 0;

                    const gap =
                        Math.max(0, target - realisasi);

                    const persentase =
                        target > 0
                            ? ((realisasi / target) * 100).toFixed(1)
                            : "0.0";

                    const bkph =
                        bkphData[dataPointIndex] ?? "-";


                    return `
                    <div style="
                        width: 250px;
                        padding: 14px 16px;
                        background: #ffffff;
                        border: 1px solid #e5e7eb;
                        border-radius: 10px;
                        box-shadow:
                            0 8px 24px
                            rgba(15, 23, 42, 0.12);
                        font-family: inherit;
                    ">

                        <div style="
                            display:flex;
                            justify-content:space-between;
                            align-items:center;
                            margin-bottom:12px;
                        ">

                            <div>
                                <div style="
                                    font-size:10px;
                                    color:#94a3b8;
                                    margin-bottom:2px;
                                ">
                                    LOKASI
                                </div>

                                <div style="
                                    font-size:14px;
                                    font-weight:700;
                                    color:#1e293b;
                                ">
                                    ${bkph}
                                </div>
                            </div>

                            <div style="
                                padding:5px 9px;
                                border-radius:6px;
                                background:#f5f3ff;
                                color:#7c3aed;
                                font-size:11px;
                                font-weight:700;
                            ">
                                ${persentase}%
                            </div>

                        </div>


                        <div style="
                            border-top:1px solid #f1f5f9;
                            padding-top:10px;
                        ">

                            <!-- TARGET -->
                            <div style="
                                display:flex;
                                justify-content:space-between;
                                margin-bottom:8px;
                            ">

                                <span style="
                                    font-size:12px;
                                    color:#64748b;
                                ">
                                    🔵 Target Pohon
                                </span>

                                <strong style="
                                    font-size:13px;
                                    color:#1e293b;
                                ">
                                    ${target.toLocaleString("id-ID")}
                                </strong>

                            </div>


                            <!-- REALISASI -->
                            <div style="
                                display:flex;
                                justify-content:space-between;
                                margin-bottom:8px;
                            ">

                                <span style="
                                    font-size:12px;
                                    color:#64748b;
                                ">
                                    🟢 Realisasi Tanam
                                </span>

                                <strong style="
                                    font-size:13px;
                                    color:#1e293b;
                                ">
                                    ${realisasi.toLocaleString("id-ID")}
                                </strong>

                            </div>


                            <!-- GAP -->
                            <div style="
                                display:flex;
                                justify-content:space-between;
                                padding-top:8px;
                                border-top:1px dashed #e2e8f0;
                            ">

                                <span style="
                                    font-size:12px;
                                    color:#64748b;
                                ">
                                    🟠 Gap
                                </span>

                                <strong style="
                                    font-size:13px;
                                    color:#d97706;
                                ">
                                    ${gap.toLocaleString("id-ID")}
                                </strong>

                            </div>

                        </div>


                        <!-- PROGRESS -->
                        <div style="
                            margin-top:13px;
                        ">

                            <div style="
                                display:flex;
                                justify-content:space-between;
                                margin-bottom:5px;
                            ">

                                <span style="
                                    font-size:10px;
                                    color:#94a3b8;
                                ">
                                    Progress
                                </span>

                                <strong style="
                                    font-size:10px;
                                    color:#7c3aed;
                                ">
                                    ${persentase}%
                                </strong>

                            </div>

                            <div style="
                                height:5px;
                                width:100%;
                                background:#e2e8f0;
                                border-radius:10px;
                                overflow:hidden;
                            ">

                                <div style="
                                    height:100%;
                                    width:${Math.min(
                        100,
                        Number(persentase)
                    )}%;
                                    background:#8B5CF6;
                                    border-radius:10px;
                                "></div>

                            </div>

                        </div>

                    </div>
                `;
                }

            }

        };


        // ==========================================
        // RENDER
        // ==========================================
        const chart = new ApexCharts(el, options);

        chart.render();

    });
</script>