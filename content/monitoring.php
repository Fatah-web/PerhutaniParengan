<?php /** @var mysqli $conn */ // 🔥 biar VS Code tidak merah ?>
<div class="col d-flex align-items-stretch">
    <div class="card w-100">
        <div class="card-body">

            <!-- ================= HEADER ================= -->
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div>
                    <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'mandor') { ?>

                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#formMonitoringModal">
                            <iconify-icon icon="solar:add-square-bold" height="25"
                                style="vertical-align: -0.5em;"></iconify-icon>
                            Tambah
                        </button>

                    <?php } ?>

                    <h5 class="card-title fw-semibold mt-3">Data Monitoring</h5>
                </div>
                <div class="modal fade" id="modalCetakDataAwal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="../laporan/export_data_awal.php" method="GET" target="_blank">
                            <div class="modal-content">

                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title">Cetak Data Awal</h5>
                                    <button type="button" class="btn-close btn-close-white"
                                        data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    <label class="form-label">Tahun</label>

                                    <select name="tahun" class="form-select">
                                        <option value="all">Semua Tahun</option>

                                        <?php
                                        $tahun = mysqli_query($conn, "
                                            SELECT DISTINCT tahun_tanam
                                            FROM data_awal
                                            WHERE tahun_tanam IS NOT NULL
                                            AND tahun_tanam <> ''
                                            ORDER BY tahun_tanam DESC
                                        ");

                                        while ($t = mysqli_fetch_assoc($tahun)) {
                                            echo "<option value='{$t['tahun_tanam']}'>{$t['tahun_tanam']}</option>";
                                        }
                                        ?>
                                    </select>

                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">
                                        <iconify-icon icon="solar:printer-bold-duotone"></iconify-icon>
                                        Cetak PDF
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                    data-bs-target="#modalCetakDataAwal">
                    <iconify-icon icon="solar:printer-bold-duotone" height="25"
                        style="vertical-align: -0.5em;"></iconify-icon>
                    Cetak
                </button>
            </div>

            <!-- ================= MODAL TAMBAH ================= -->
            <div class="modal fade" id="formMonitoringModal" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-white">Tambah Data Monitoring</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body bg-light">

                            <form action="../function/fungsi_monitoring.php?aksi=simpan" method="POST"
                                enctype="multipart/form-data">

                                <div class="row">

                                    <!-- ================= CARD POHON HIDUP ================= -->
                                    <div class="col-md-6">
                                        <div class="card shadow-sm border-success h-100">
                                            <div class="card-header bg-success text-center text-white fw-semibold">
                                                Monitoring Pohon Hidup
                                            </div>

                                            <div class="card-body">
                                                <div class="text-center mb-3">
                                                    <div style="width:290px; 
                                                            height:200px;
                                                            margin:auto;
                                                            padding:5px; 
                                                            border:3px solid #000; 
                                                            border-radius:10px; 
                                                            background-color:#f8f9fa;
                                                            display:flex;
                                                            align-items:center;
                                                            justify-content:center;">
                                                        <img id="preview_hidup" src="" alt="Preview Foto Hidup" style="max-width:100%;
                                                            max-height:100%;
                                                            object-fit:cover;
                                                            border-radius:6px;">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Foto Pohon Hidup</label>
                                                    <input type="file" class="form-control" name="foto_hidup"
                                                        accept="image/*"
                                                        onchange="preview_hidup.src = window.URL.createObjectURL(this.files[0])">
                                                </div>

                                                <div class="mb-3">
                                                    <label>Jumlah Hidup</label>
                                                    <input type="number" class="form-control" name="jumlah_hidup"
                                                        placeholder="Contoh: 950" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Tinggi / Diameter</label>
                                                    <input type="text" class="form-control" name="tinggi_diameter"
                                                        required placeholder="Contoh: 120cm / 3cm">
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- ================= CARD POHON MATI ================= -->
                                    <div class="col-md-6">
                                        <div class="card shadow-sm border-danger h-100">
                                            <div class="card-header bg-danger text-center text-white fw-semibold">
                                                Monitoring Pohon Mati
                                            </div>

                                            <div class="card-body">
                                                <div class="text-center mb-3">
                                                    <div style="width:290px; 
                                                            height:200px;
                                                            margin:auto;
                                                            padding:5px; 
                                                            border:3px solid #000; 
                                                            border-radius:10px; 
                                                            background-color:#f8f9fa;
                                                            display:flex;
                                                            align-items:center;
                                                            justify-content:center;">

                                                        <img id="preview_mati" src="" alt="Preview Foto Mati" style="max-width:100%;
                                                            max-height:100%;
                                                            object-fit:cover;
                                                            border-radius:6px;">
                                                    </div>
                                                </div>


                                                <div class="mb-3">
                                                    <label>Foto Pohon Mati</label>
                                                    <input type="file" class="form-control" name="foto_mati"
                                                        accept="image/*"
                                                        onchange="preview_mati.src = window.URL.createObjectURL(this.files[0])">
                                                </div>

                                                <div class="mb-3">
                                                    <label>Jumlah Mati</label>
                                                    <input type="number" class="form-control" name="jumlah_mati"
                                                        required placeholder="Contoh: 50">
                                                </div>

                                                <div class="mb-3">
                                                    <label>Gangguan</label>
                                                    <input type="text" class="form-control" name="gangguan" required
                                                        placeholder="Contoh: Hama, kekeringan">
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <hr>
                                <div class="col-md-12">
                                    <div class="card shadow-sm border-success h-100">
                                        <div class="card-header bg-dark text-white text-center fw-semibold">
                                            DATA UMUM
                                        </div>

                                        <div class="card-body">
                                            <!-- ================= DATA UMUM ================= -->
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label>Tanggal Monitoring</label>
                                                    <input type="date" class="form-control" name="tanggal" required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Data Penanaman</label>
                                                    <select name="tanam_id" class="form-select" required>
                                                        <option value="">-- Pilih Data Tanam --</option>

                                                        <?php
                                                        $role = $_SESSION['role'] ?? '';
                                                        $mandor_id = $profil['mandor_id'] ?? '';

                                                        $sql = "
                                                        SELECT 
                                                            p.tanam_id,
                                                            p.jumlah_tanam,
                                                            p.tanggal,
                                                            b.nama_bkph,
                                                            b.rph,
                                                            b.petak,
                                                            b.mandor_id
                                                        FROM penanaman p
                                                        LEFT JOIN ajir a ON p.ajir_id = a.ajir_id
                                                        LEFT JOIN lubang l ON a.lubang_id = l.lubang_id
                                                        LEFT JOIN data_awal da ON l.data_awal_id = da.data_awal_id
                                                        LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
                                                    ";

                                                        /* =========================
                                                    FILTER KHUSUS MANDOR
                                                    ========================= */
                                                        if (strtolower($role) === 'mandor' && !empty($mandor_id)) {
                                                            $sql .= " WHERE b.mandor_id = '$mandor_id' ";
                                                        }

                                                        $sql .= " ORDER BY b.nama_bkph ASC, b.rph ASC";

                                                        $q = mysqli_query($conn, $sql);

                                                        while ($d = mysqli_fetch_assoc($q)) {
                                                            ?>
                                                            <option value="<?= $d['tanam_id'] ?>">
                                                                <?= $d['nama_bkph'] ?> |
                                                                RPH <?= $d['rph'] ?> |
                                                                Petak <?= $d['petak'] ?> |
                                                                <?= number_format($d['jumlah_tanam']) ?> pohon
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label>Evaluasi</label>
                                                <textarea class="form-control" name="evaluasi" rows="3"
                                                    required></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label>Catatan</label>
                                                <textarea class="form-control" name="catatan" rows="3"
                                                    required></textarea>
                                            </div>

                                        </div>
                                    </div>
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


            <!-- ================= TABLE ================= -->
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Kondisi</th>
                            <th>Foto Hidup</th>
                            <th>Foto Mati</th>
                            <th>Status</th>
                            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'asper') { ?>
                                <th class="text-center">Verifikasi</th>
                            <?php } ?>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        $no = 1;

                        $role = $_SESSION['role'] ?? '';
                        $mandor_id = $profil['mandor_id'] ?? '';
                        $isMandor = ($role === 'mandor');

                        $sql = "
                            SELECT 
                                m.*,
                                b.nama_bkph,
                                b.rph,
                                b.petak,
                                ma.nama AS nama_mandor
                            FROM monitoring m
                            LEFT JOIN penanaman p ON m.tanam_id = p.tanam_id
                            LEFT JOIN ajir a ON p.ajir_id = a.ajir_id
                            LEFT JOIN lubang l ON a.lubang_id = l.lubang_id
                            LEFT JOIN data_awal da ON l.data_awal_id = da.data_awal_id
                            LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
                            LEFT JOIN mandor ma ON b.mandor_id = ma.mandor_id
                        ";

                        /* =========================
                        FILTER KHUSUS MANDOR
                        ========================= */
                        /* Filter khusus MANDOR */
                        if ($role === 'mandor' && !empty($mandor_id)) {
                            $where[] = "b.mandor_id = '$mandor_id'";
                        }

                        /* Gabungkan WHERE kalau ada */
                        if (!empty($where)) {
                            $sql .= " WHERE " . implode(" AND ", $where);
                        }

                        $sql .= " ORDER BY b.nama_bkph ASC, b.rph ASC";

                        $query = mysqli_query($conn, $sql);

                        if (!$query) {
                            die("Query Error: " . mysqli_error($conn));
                        }

                        while ($data = mysqli_fetch_array($query)) {

                            $badgeColor = [
                                'rejected' => 'danger',
                                'pending' => 'warning',
                                'verified' => 'success'
                            ][$data['status']] ?? 'secondary';
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime($data['tanggal'])) ?></td>

                                <td>
                                    <b>Mandor:<?= $data['nama_mandor'] ?></b><br>
                                    BKPH:<?= $data['nama_bkph'] ?><br>
                                    RPH-<?= $data['rph'] ?><br>
                                    <small>Petak-<?= $data['petak'] ?></small>

                                </td>

                                <td><?= $data['jumlah_hidup'] ?> Hidup <br>
                                    <?= $data['jumlah_mati'] ?> Mati</td>

                                <!-- FOTO HIDUP -->
                                <td class="text-center">
                                    <?php if ($data['foto_hidup'] != '') { ?>
                                        <img src="../gambar_monitoring/<?= $data['foto_hidup'] ?>" width="100"
                                            style="object-fit: cover; aspect-ratio:1/1; border-radius:6px;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailMonitoring<?= $data['monitoring_id'] ?>">
                                    <?php } ?> <br>
                                    tinggi/diameter : <?= $data['tinggi_diameter'] ?>
                                </td>

                                <!-- FOTO MATI -->
                                <td class="text-center">
                                    <?php if ($data['foto_mati'] != '') { ?>
                                        <img src="../gambar_monitoring/<?= $data['foto_mati'] ?>" width="100"
                                            style="object-fit: cover; aspect-ratio:1/1; border-radius:6px;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailMonitoring<?= $data['monitoring_id'] ?>">
                                    <?php } ?> <br>
                                    gangguan : <?= $data['gangguan'] ?>
                                </td>


                                <td>
                                    <button class="btn btn-<?= $badgeColor ?>" style="width:115px;">
                                        <?= ucfirst($data['status']) ?>
                                    </button>
                                </td>

                                <!-- VERIFIKASI -->
                                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'asper') { ?>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <?php if ($data['status'] == 'pending') { ?>
                                                <a href="../function/fungsi_monitoring.php?aksi=verifikasi&monitoring_id=<?= $data['monitoring_id'] ?>"
                                                    class="btn btn-success btn-sm d-flex align-items-center justify-content-center btn-verif"
                                                    style="width:32px; height:32px;">
                                                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                                                </a>
                                                <a class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalReject<?= $data['monitoring_id'] ?>"
                                                    style="width:32px; height:32px;">
                                                    <iconify-icon icon="solar:close-circle-bold-duotone" width="20"></iconify-icon>
                                                </a>
                                            <?php } else { ?>
                                                <small class="text-muted">—</small>
                                            <?php } ?>
                                        </div>
                                    </td>
                                <?php } ?>

                                <!-- AKSI -->
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a class="btne btn-detail" data-bs-toggle="modal"
                                            data-bs-target="#detailMonitoring<?= $data['monitoring_id'] ?>">
                                            <iconify-icon icon="solar:eye-bold-duotone" height="20"></iconify-icon>
                                        </a>
                                        <?php
                                        $isMandor = $_SESSION['role'] == 'mandor';
                                        $isPimpinan = $_SESSION['role'] == 'pimpinan';
                                        $isPending = $data['status'] == 'pending';
                                        ?>

                                        <?php if (
                                            !$isPimpinan &&                 // Pimpinan tidak boleh sama sekali
                                            (
                                                !$isMandor ||               // Jika bukan mandor → boleh
                                                ($isMandor && $isPending)   // Jika mandor → hanya boleh kalau pending
                                            )
                                        ) { ?>
                                            <a class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editMonitoring<?= $data['monitoring_id'] ?>">
                                                <iconify-icon icon="solar:pen-2-bold-duotone" height="20"></iconify-icon>
                                            </a>

                                            <a href="../function/fungsi_monitoring.php?aksi=hapus&monitoring_id=<?= $data['monitoring_id'] ?>"
                                                class="btne btn-hapus fw-medium">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone"
                                                    height="20"></iconify-icon>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- ================= MODAL DETAIL MONITORING ================= -->
                            <div class="modal fade" id="detailMonitoring<?= $data['monitoring_id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow">

                                        <!-- HEADER -->
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title text-white fw-semibold">
                                                <i class="bi bi-activity me-2"></i>
                                                Detail Monitoring Tanaman
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body bg-light">

                                            <?php
                                            $total = $data['jumlah_hidup'] + $data['jumlah_mati'];
                                            $persen = $total > 0
                                                ? round(($data['jumlah_hidup'] / $total) * 100, 2)
                                                : 0;
                                            ?>

                                            <!-- ================= RINGKASAN ================= -->
                                            <div class="card border-0 shadow-sm mb-4">
                                                <div class="card-body">
                                                    <div class="row align-items-center">

                                                        <div class="col-md-8">
                                                            <h4 class="fw-bold text-success mb-1">
                                                                BKPH <?= $data['nama_bkph'] ?>
                                                            </h4>

                                                            <div class="text-muted mb-2">
                                                                <i class="bi bi-calendar-event me-1"></i>
                                                                <?= date('d F Y', strtotime($data['tanggal'])) ?>
                                                            </div>

                                                            <span
                                                                class="badge bg-<?= $data['status'] == 'baik' ? 'success' : 'warning' ?> px-3 py-2">
                                                                Status: <?= ucfirst($data['status']) ?>
                                                            </span>
                                                        </div>

                                                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                                            <small class="text-muted">Total Tanaman</small>
                                                            <div class="fs-3 fw-bold">
                                                                <?= number_format($total) ?> Pohon
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>


                                            <!-- ================= STATISTIK ================= -->
                                            <div class="row g-4 mb-4">

                                                <div class="col-md-4">
                                                    <div class="card border-0 shadow-sm text-center h-100">
                                                        <div class="card-body py-4">
                                                            <div class="text-muted mb-2">Tanaman Hidup</div>
                                                            <div class="display-6 fw-bold text-success">
                                                                <?= number_format($data['jumlah_hidup'] ?? 0) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="card border-0 shadow-sm text-center h-100">
                                                        <div class="card-body py-4">
                                                            <div class="text-muted mb-2">Tanaman Mati</div>
                                                            <div class="display-6 fw-bold text-danger">
                                                                <?= number_format($data['jumlah_mati'] ?? 0) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="card border-0 shadow-sm h-100">
                                                        <div class="card-body py-4">
                                                            <div class="text-muted mb-2">Persentase Hidup</div>
                                                            <div class="fs-3 fw-bold"><?= $persen ?>%</div>

                                                            <div class="progress mt-2" style="height:12px;">
                                                                <div class="progress-bar bg-success"
                                                                    style="width: <?= $persen ?>%">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <!-- ================= INFORMASI LOKASI ================= -->
                                            <div class="card border-0 shadow-sm mb-4">
                                                <div class="card-body">
                                                    <h5 class="fw-bold mb-4 text-muted text-center">Informasi Lokasi &
                                                        Kondisi</h5>

                                                    <div class="row text-center">

                                                        <div class="col">
                                                            <div class="text-muted">BKPH</div>
                                                            <div class="fs-5 fw-semibold">
                                                                <?= $data['nama_bkph'] ?>
                                                            </div>
                                                        </div>

                                                        <div class="col">
                                                            <div class="text-muted">RPH</div>
                                                            <div class="fs-5 fw-semibold">
                                                                <?= $data['rph'] ?>
                                                            </div>
                                                        </div>

                                                        <div class="col">
                                                            <div class="text-muted">Petak</div>
                                                            <div class="fs-5 fw-semibold">
                                                                <?= $data['petak'] ?>
                                                            </div>
                                                        </div>

                                                        <div class="col">
                                                            <div class="text-muted">Tinggi/Diameter</div>
                                                            <div class="fs-5 fw-semibold">
                                                                <?= $data['tinggi_diameter'] ?>
                                                            </div>
                                                        </div>

                                                        <div class="col">
                                                            <div class="text-muted">Gangguan</div>
                                                            <div class="fs-5 fw-semibold">
                                                                <?= $data['gangguan'] ?: '<span class="text-muted">Tidak ada</span>'; ?>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <!-- ================= FOTO DOKUMENTASI ================= -->
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="text-uppercase text-muted text-center mb-3">
                                                        Dokumentasi Lapangan
                                                    </h6>

                                                    <div class="row g-3">

                                                        <div class="col-md-6 text-center">
                                                            <div class="fw-semibold mb-2">Tanaman Hidup</div>
                                                            <img src="../gambar_monitoring/<?= $data['foto_hidup'] ?>"
                                                                class="img-fluid rounded border shadow-sm"
                                                                style="max-height:250px; object-fit:cover;">
                                                        </div>

                                                        <div class="col-md-6 text-center">
                                                            <div class="fw-semibold mb-2">Tanaman Mati</div>
                                                            <img src="../gambar_monitoring/<?= $data['foto_mati'] ?>"
                                                                class="img-fluid rounded border shadow-sm"
                                                                style="max-height:250px; object-fit:cover;">
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card border-0 shadow-sm mb-4">
                                                <div class="card-body">

                                                    <h5 class="fw-bold mb-4 text-center text-muted">Evaluasi & Catatan
                                                        Monitoring</h5>

                                                    <div class="row">

                                                        <!-- EVALUASI -->
                                                        <div class="col-md-6 mb-3">
                                                            <div class="p-3 border rounded h-100 bg-light">
                                                                <div class="text-muted mb-2">
                                                                    <i class="bi bi-clipboard-check me-1"></i>
                                                                    Evaluasi
                                                                </div>

                                                                <div class="fs-6">
                                                                    <?= $data['evaluasi']
                                                                        ?: '<span class="text-muted">Belum ada evaluasi</span>'; ?>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- CATATAN -->
                                                        <div class="col-md-6 mb-3">
                                                            <div class="p-3 border rounded h-100 bg-light">
                                                                <div class="text-muted mb-2">
                                                                    <i class="bi bi-journal-text me-1"></i>
                                                                    Catatan Lapangan
                                                                </div>

                                                                <div class="fs-6">
                                                                    <?= $data['catatan']
                                                                        ?: '<span class="text-muted">Tidak ada catatan</span>'; ?>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>


                                        </div>

                                        <!-- FOOTER -->
                                        <div class="modal-footer">
                                            <a target="_blank"
                                                href="../laporan/export_monitoring.php?monitoring_id=<?= $data['monitoring_id'] ?>"
                                                class="btn btn-success px-4">
                                                <i class="bi bi-printer me-1"></i> Cetak Data
                                            </a>
                                            <button type="button" class="btn btn-outline-secondary px-4"
                                                data-bs-dismiss="modal">
                                                Tutup
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- ================= MODAL REJECT ================= -->
                            <div class="modal fade" id="modalReject<?= $data['monitoring_id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Alasan Penolakan</h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal"></button>
                                        </div>

                                        <form action="../function/fungsi_monitoring.php?aksi=reject" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="monitoring_id"
                                                    value="<?= $data['monitoring_id'] ?>">
                                                <textarea name="alasan_reject" class="form-control" rows="3"></textarea>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Kirim Reject</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>

                            <!-- ================= MODAL EDIT ================= -->
                            <div class="modal fade" id="editMonitoring<?= $data['monitoring_id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit Data Monitoring</h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal"></button>
                                        </div>

                                        <form action="../function/fungsi_monitoring.php?aksi=edit" method="POST"
                                            enctype="multipart/form-data">

                                            <input type="hidden" name="monitoring_id" value="<?= $data['monitoring_id'] ?>">
                                            <input type="hidden" name="foto_hidup_lama" value="<?= $data['foto_hidup'] ?>">
                                            <input type="hidden" name="foto_mati_lama" value="<?= $data['foto_mati'] ?>">

                                            <div class="modal-body bg-light">

                                                <div class="row">

                                                    <!-- ================= CARD POHON HIDUP ================= -->
                                                    <div class="col-md-6">
                                                        <div class="card shadow-sm border-success h-100">
                                                            <div
                                                                class="card-header bg-success text-center text-white fw-semibold">
                                                                Monitoring Pohon Hidup
                                                            </div>

                                                            <div class="card-body">

                                                                <div class="text-center mb-3">
                                                                    <div
                                                                        style="width:290px;height:200px;margin:auto;padding:5px;
                                                                        border:3px solid #000;border-radius:10px;background:#f8f9fa;
                                                                        display:flex;align-items:center;justify-content:center;">

                                                                        <img id="preview_hidup_edit<?= $data['monitoring_id'] ?>"
                                                                            src="../gambar_monitoring/<?= $data['foto_hidup'] ?>"
                                                                            style="max-width:100%;max-height:100%;object-fit:cover;border-radius:6px;">
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label>Ganti Foto Hidup</label>
                                                                    <input type="file" class="form-control"
                                                                        name="foto_hidup" accept="image/*"
                                                                        onchange="document.getElementById('preview_hidup_edit<?= $data['monitoring_id'] ?>').src = window.URL.createObjectURL(this.files[0])">
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label>Jumlah Hidup</label>
                                                                    <input type="number" class="form-control"
                                                                        name="jumlah_hidup"
                                                                        value="<?= $data['jumlah_hidup'] ?>" required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label>Tinggi / Diameter</label>
                                                                    <input type="text" class="form-control"
                                                                        name="tinggi_diameter"
                                                                        value="<?= $data['tinggi_diameter'] ?>" required>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- ================= CARD POHON MATI ================= -->
                                                    <div class="col-md-6">
                                                        <div class="card shadow-sm border-danger h-100">
                                                            <div
                                                                class="card-header bg-danger text-center text-white fw-semibold">
                                                                Monitoring Pohon Mati
                                                            </div>

                                                            <div class="card-body">

                                                                <div class="text-center mb-3">
                                                                    <div
                                                                        style="width:290px;height:200px;margin:auto;padding:5px;
                                                                            border:3px solid #000;border-radius:10px;background:#f8f9fa;
                                                                            display:flex;align-items:center;justify-content:center;">

                                                                        <img id="preview_mati_edit<?= $data['monitoring_id'] ?>"
                                                                            src="../gambar_monitoring/<?= $data['foto_mati'] ?>"
                                                                            style="max-width:100%;max-height:100%;object-fit:cover;border-radius:6px;">
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label>Ganti Foto Mati</label>
                                                                    <input type="file" class="form-control" name="foto_mati"
                                                                        accept="image/*"
                                                                        onchange="document.getElementById('preview_mati_edit<?= $data['monitoring_id'] ?>').src = window.URL.createObjectURL(this.files[0])">
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label>Jumlah Mati</label>
                                                                    <input type="number" class="form-control"
                                                                        name="jumlah_mati"
                                                                        value="<?= $data['jumlah_mati'] ?>" required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label>Gangguan</label>
                                                                    <input type="text" class="form-control" name="gangguan"
                                                                        value="<?= $data['gangguan'] ?>" required>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <hr>

                                                <!-- ================= DATA UMUM ================= -->
                                                <div class="card shadow-sm border-dark">
                                                    <div class="card-header bg-dark text-white text-center fw-semibold">
                                                        DATA UMUM
                                                    </div>

                                                    <div class="card-body">

                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <label>Tanggal Monitoring</label>
                                                                <input type="date" class="form-control" name="tanggal"
                                                                    value="<?= $data['tanggal'] ?>" required>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <label>Data Penanaman</label>
                                                                <select name="tanam_id" class="form-select" required>
                                                                    <option value="">-- Pilih Data Tanam --</option>

                                                                    <?php
                                                                    $role = $_SESSION['role'] ?? '';
                                                                    $mandor_id = $profil['mandor_id'] ?? '';

                                                                    $sql = "
                                                                        SELECT 
                                                                            p.tanam_id,
                                                                            p.jumlah_tanam,
                                                                            p.tanggal,
                                                                            b.nama_bkph,
                                                                            b.rph,
                                                                            b.petak,
                                                                            b.mandor_id
                                                                        FROM penanaman p
                                                                        LEFT JOIN ajir a ON p.ajir_id = a.ajir_id
                                                                        LEFT JOIN lubang l ON a.lubang_id = l.lubang_id
                                                                        LEFT JOIN data_awal da ON l.data_awal_id = da.data_awal_id
                                                                        LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
                                                                    ";

                                                                    /* =========================
                                                                    FILTER KHUSUS MANDOR
                                                                    ========================= */
                                                                    if (strtolower($role) === 'mandor' && !empty($mandor_id)) {
                                                                        $sql .= " WHERE b.mandor_id = '$mandor_id' ";
                                                                    }

                                                                    $sql .= " ORDER BY b.nama_bkph ASC, b.rph ASC";

                                                                    $q = mysqli_query($conn, $sql);

                                                                    while ($d = mysqli_fetch_assoc($q)) {

                                                                        // ✅ AUTO SELECT SAAT EDIT
                                                                        $selected = ($d['tanam_id'] == ($data['tanam_id'] ?? '')) ? 'selected' : '';
                                                                        ?>
                                                                        <option value="<?= $d['tanam_id'] ?>" <?= $selected ?>>
                                                                            BKPH <?= $d['nama_bkph'] ?> |
                                                                            RPH <?= $d['rph'] ?> |
                                                                            Petak <?= $d['petak'] ?> |
                                                                            <?= number_format($d['jumlah_tanam']) ?> pohon
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <label>status</label>
                                                                <?php if ($isMandor): ?>
                                                                    <!-- Mandor hanya melihat -->
                                                                    <input type="text" class="form-control"
                                                                        value="<?= ucfirst($data['status']) ?>" disabled>

                                                                    <!-- Supaya tetap terkirim saat submit -->
                                                                    <input type="hidden" name="status"
                                                                        value="<?= $data['status'] ?>">

                                                                <?php else: ?>
                                                                    <!-- Role lain bisa edit -->
                                                                    <select name="status" class="form-select">
                                                                        <option value="pending" <?= $data['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                                        <option value="verified" <?= $data['status'] == 'verified' ? 'selected' : '' ?>>Verified</option>
                                                                        <option value="rejected" <?= $data['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                                                    </select>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label>Evaluasi</label>
                                                            <textarea class="form-control" name="evaluasi"
                                                                required><?= $data['evaluasi'] ?></textarea>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label>Catatan</label>
                                                            <textarea class="form-control" name="catatan"
                                                                required><?= $data['catatan'] ?></textarea>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning">Update</button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>


                        <?php } ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>