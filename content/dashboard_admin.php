<?php

function getTotal($conn, $kategori)
{
    $stmt = $conn->prepare("SELECT SUM(jumlah) AS total FROM transaksi WHERE kategori = ?");
    $stmt->bind_param("s", $kategori);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'] ?? 0;
}

// Total kategori transaksi
$total_tabungan        = getTotal($conn, 'Tabungan nasabah');
$total_pinjaman        = getTotal($conn, 'Pinjaman');
$total_angsuran        = getTotal($conn, 'Angsuran');
$total_pengluaran      = getTotal($conn, 'Pengluaran');
$total_pemasukan       = getTotal($conn, 'Pemasukan');
$total_tarik_tabungan  = getTotal($conn, 'tarik tabungan');

// Total Kas
$kas_masuk  = $total_tabungan + $total_angsuran + $total_pemasukan;
$kas_keluar = $total_pengluaran + $total_pinjaman + $total_tarik_tabungan;

$total_kas = $kas_masuk - $kas_keluar;

// Hitung jumlah status pinjaman
$pending  = $conn->query("SELECT COUNT(*) AS jml FROM pinjaman WHERE status='pending'")->fetch_assoc()['jml'];
$approved = $conn->query("SELECT COUNT(*) AS jml FROM pinjaman WHERE status='approved'")->fetch_assoc()['jml'];
$active   = $conn->query("SELECT COUNT(*) AS jml FROM pinjaman WHERE status='active'")->fetch_assoc()['jml'];
$closed   = $conn->query("SELECT COUNT(*) AS jml FROM pinjaman WHERE status='closed'")->fetch_assoc()['jml'];
?>
<div class="col-lg-8 d-flex align-items-strech">
    <div class="card w-100">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div class="">
                    <h5 class="card-title fw-semibold">Grafik Keuangan</h5>
                </div>
            </div>
            <div id="chartKeuangan"></div>
        </div>
    </div>
</div>

<div class="col-lg-4">
    <div class="row">


        <div class="col-lg-12 col-sm-6">
            <!-- Yearly Breakup -->
            <div class="card overflow-hidden">
                <div class="card-body p-4">
                    <h5 class="card-title mb-10 fw-semibold">Kas</h5>
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="d-flex justify-content-center">
                                <div id="chartKas"></div>
                            </div>
                        </div>
                        <div class="col-7">
                            <h4 class="fw-semibold mb-3">Rp <?= number_format($total_kas, 0, ',', '.'); ?></h4>
                            <p class="fs-3 mb-0">Sisa Kas</p>
                            <div class="d-flex align-items-center mb-2" id="sisaKasPersentase">
                                <span
                                    class="me-1 rounded-circle bg-light-success round-25 d-flex align-items-center justify-content-center"
                                    id="sisaKasIcon" style="font-size:34px;">
                                    <i class="ti ti-arrow-up-left text-success"></i>
                                </span>
                                <p class="text-dark me-2 fs-7 mb-0" id="sisaKasValue">+0%</p>
                                <p class="fs-3 mb-0">Dari Kas Masuk</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span
                                        class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                                    <span class="fs-2">Kas Masuk</span>
                                </div>
                                <div>
                                    <span
                                        class="round-8 bg-danger rounded-circle me-2 d-inline-block"></span>
                                    <span class="fs-2">Kas Keluar</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-sm-6">
            <div class="card overflow-hidden">
                <div class="card-body pb-2 px-4 pt-3">
                    <h5 class="card-title mb-2 fw-semibold">Pinjaman Nasabah</h5>
                </div>
                <div id="chartPinjaman" class="px-6"></div>
            </div>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="card w-100" style="height: 70vh;">
        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">

            <!-- Logo besar -->
            <img src="../assets/images/logos/logo-m.png"
                alt="Logo BMT NU"
                style="max-width: 320px;">

            <!-- Judul -->
            <h1 class="fw-bold mt-3">Perum Perhutani KPH Parengan</h1>

            <!-- 2 Baris teks -->
            <p class="text-muted fs-5">
                Mengelola Hutan dengan Integritas,Bersama Masyarakat, Mewujudkan Harmoni Alam,
            </p>

        </div>
    </div>
</div>
<!-- STATISTIK -->
<div class="row text-center small mb-3 g-2">

    <!-- Lubang -->
    <div class="col-3">
        <div class="stat-box">
            <div class="stat-icon-box icon-lubang">
                <i class="bi bi-hammer"></i>
            </div>
            <div class="info-label">Lubang</div>
            <div class="info-value"><?= number_format($total_lubang) ?></div>
        </div>
    </div>

    <!-- Ajir -->
    <div class="col-3">
        <div class="stat-box">
            <div class="stat-icon-box icon-ajir">
                <i class="bi bi-geo"></i>
            </div>
            <div class="info-label">Ajir</div>
            <div class="info-value"><?= number_format($total_ajir) ?></div>
        </div>
    </div>

    <!-- Tanam -->
    <div class="col-3">
        <div class="stat-box">
            <div class="stat-icon-box icon-tanam">
                <i class="bi bi-tree"></i>
            </div>
            <div class="info-label">Tanam</div>
            <div class="info-value"><?= number_format($total_tanam) ?></div>
        </div>
    </div>

    <!-- Hidup -->
    <div class="col-3">
        <div class="stat-box">
            <div class="stat-icon-box icon-hidup">
                <i class="bi bi-patch-check"></i>
            </div>
            <div class="info-label">Hidup</div>
            <div class="info-value text-success">
                <?= number_format($total_hidup) ?>
            </div>
        </div>
    </div>

</div>