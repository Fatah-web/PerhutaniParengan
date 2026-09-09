<?php /** @var mysqli $conn */ // 🔥 biar VS Code tidak merah ?>
<div class="col d-flex align-items-stretch">
    <div class="card w-100">
        <div class="card-body">

            <div class="d-flex align-items-center justify-content-between mb-1">
                <div>
                    <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'mandor') { ?>
                        <button type="button" class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#formTanamModal">
                            <iconify-icon icon="solar:add-square-bold" height="25" style="vertical-align: -0.5em;"></iconify-icon>
                            Tambah
                        </button>
                    <?php } ?>
                    <h5 class="card-title fw-semibold mt-3">Data Penanaman</h5>
                </div>
                <div class="modal fade" id="modalCetakDataAwal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <form action="../laporan/export_data_tanam.php" method="GET" target="_blank">
              <div class="modal-content">

                <div class="modal-header bg-success text-white">
                  <h5 class="modal-title">Cetak Data Awal</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                  <button type="submit" class="btn btn-success" onclick="setTimeout(function(){ location.reload(); }, 800);">
                    <iconify-icon icon="solar:printer-bold-duotone"></iconify-icon>
                    Cetak PDF
                  </button>
                </div>

              </div>
            </form>
          </div>
        </div>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCetakDataAwal">
          <iconify-icon icon="solar:printer-bold-duotone" height="25" style="vertical-align: -0.5em;"></iconify-icon>
          Cetak
        </button>
            </div>

            <!-- ================= MODAL TAMBAH ================= -->
            <div class="modal fade" id="formTanamModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header text-white bg-primary">
                            <h5 class="modal-title text-white">Tambah Data Penanaman</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <form action="../function/fungsi_penanaman.php?aksi=simpan"
                                method="POST" enctype="multipart/form-data">

                                <div class="row mb-3">
                                    <div>
                                        <h4>Informasi Penanaman</h4>
                                        <hr>
                                    </div>

                                    <div class="col-4 d-flex justify-content-center">
                                        <img id="preview" alt="Preview Foto" src="" width="150">
                                    </div>

                                    <div class="col align-self-center">
                                        <input type="file" class="form-control"
                                            name="foto"
                                            accept="image/*"
                                            oninput="preview.src = window.URL.createObjectURL(this.files[0])">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label>Jumlah Tanam</label>
                                    <input type="number" class="form-control"
                                        name="jumlah_tanam" placeholder="Contoh: 1200" required>
                                </div>
                                <div class="mb-3">
                                    <label>Sumber Bibit</label>
                                    <input type="text" class="form-control"
                                        name="sumber_bibit" placeholder="Contoh: sumber bibit bkph parengan selatan" required>
                                </div>

                                <div class="mb-3">
                                    <label>Tanggal</label>
                                    <input type="date" class="form-control"
                                        name="tanggal" required>
                                </div>

                                <div class="mb-3">
                                    <label>Catatan</label>
                                    <textarea class="form-control"
                                        name="catatan" placeholder="Contoh: penanaman lancar" rows="4" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Data Ajir</label>
                                    <select name="ajir_id" class="form-select selectinput" required>
                                        <option value="">-- Pilih Lokasi --</option>

                                        <?php
                                        $role = $_SESSION['role'];
                                        $mandor_id = $profil['mandor_id'] ?? null;

                                        $sql_ajir = "
                                                    SELECT 
                                                        a.ajir_id,
                                                        a.jumlah_ajir,
                                                        da.tahun_tanam,
                                                        b.nama_bkph,
                                                        b.rph,
                                                        b.petak,
                                                        b.mandor_id
                                                    FROM ajir a
                                                    LEFT JOIN lubang l ON a.lubang_id = l.lubang_id
                                                    LEFT JOIN data_awal da ON l.data_awal_id = da.data_awal_id
                                                    LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
                                                ";

                                        // 🔥 Filter jika mandor
                                        if (strtolower(trim($role)) === 'mandor' && !empty($mandor_id)) {
                                            $sql_ajir .= " WHERE b.mandor_id = $mandor_id";
                                        }

                                        $sql_ajir .= " ORDER BY b.nama_bkph ASC, b.rph ASC";

                                        $result_ajir = mysqli_query($conn, $sql_ajir);

                                        if (!$result_ajir) {
                                            die("Query Error: " . mysqli_error($conn));
                                        }

                                        while ($d = mysqli_fetch_assoc($result_ajir)) {
                                        ?>
                                            <option value="<?= $d['ajir_id'] ?>">
                                                <?= $d['nama_bkph'] ?> |
                                                RPH <?= $d['rph'] ?> |
                                                Petak <?= $d['petak'] ?> |
                                                Tahun <?= $d['tahun_tanam'] ?> |
                                                Ajir <?= number_format($d['jumlah_ajir']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
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


            <!-- ================= TABLE ================= -->
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Tanggal</th>
                            <th>Data Lokasi</th>
                            <th>Mandor</th>
                            <th>Rencana Tanam</th>
                            <th>Jumlah Tanam</th>
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

                        $role      = $_SESSION['role'] ?? '';
                        $mandor_id = $profil['mandor_id'] ?? '';
                        $isMandor = ($role === 'mandor');

                        $sql = "
                            SELECT 
                                p.*,
                                a.jumlah_ajir,
                                da.tahun_tanam,
                                da.target_pohon,
                                b.nama_bkph,
                                b.rph,
                                b.petak,
                                b.jenis_tanaman,
                                m.nama AS nama_mandor
                            FROM penanaman p
                            LEFT JOIN ajir a ON p.ajir_id = a.ajir_id
                            LEFT JOIN lubang l ON a.lubang_id = l.lubang_id
                            LEFT JOIN data_awal da ON l.data_awal_id = da.data_awal_id
                            LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
                            LEFT JOIN mandor m ON b.mandor_id = m.mandor_id
                        ";

                        /* =========================
                        FILTER KHUSUS MANDOR
                        ========================= */
                        /* Filter khusus MANDOR */
                        $where = [];

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
                                'pending'  => 'warning',
                                'verified' => 'success'
                            ][$data['status']] ?? 'secondary';
                        ?>

                            <tr>
                                <td><?= $no++ ?></td>

                                <td>
                                    <img src="../gambar_tanam/<?= $data['foto_tanam'] ?>"
                                        width="100"
                                        style="object-fit:cover;aspect-ratio:1/1;border-radius:6px;">
                                </td>

                                <td><?= date('d-m-Y', strtotime($data['tanggal'])) ?></td>

                                <td>
                                    <b>BKPH:<?= $data['nama_bkph'] ?></b><br>
                                    RPH-<?= $data['rph'] ?><br>
                                    <small>Petak-<?= $data['petak'] ?></small>
                                </td>

                                <td><?= $data['nama_mandor'] ?></td>

                                <td>
                                    <b>Tanam Tahun:<?= $data['tahun_tanam'] ?></b><br>
                                    <small>Jenis-<?= $data['jenis_tanaman'] ?><br>
                                        Target <?= number_format((int)($data['target_pohon'] ?? 0)) ?> Pohon</small>
                                </td>

                                <td><?= $data['jumlah_tanam'] ?> Pohon</td>

                                <td>
                                    <button class="btn btn-<?= $badgeColor ?>" style="width:115px;">
                                        <?= ucfirst($data['status']) ?>
                                    </button>
                                </td>
                                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'asper') { ?>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <?php if ($data['status'] == 'pending') { ?>

                                                <a href="../function/fungsi_penanaman.php?aksi=verifikasi&tanam_id=<?= $data['tanam_id'] ?>"
                                                    class="btn btn-success btn-sm d-flex align-items-center justify-content-center btn-verif"
                                                    style="width:32px; height:32px;">
                                                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                                                </a>

                                                <a class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                                                    data-bs-toggle="modal"
                                                    style="width:32px; height:32px;"
                                                    data-bs-target="#modalReject<?= $data['tanam_id'] ?>">
                                                    <iconify-icon icon="solar:close-circle-bold-duotone" width="20"></iconify-icon>
                                                </a>

                                            <?php } else { ?>
                                                <small class="text-muted">—</small>
                                            <?php } ?>
                                        </div>
                                    </td>
                                <?php } ?>

                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">

                                        <a class="btne btn-detail" data-bs-toggle="modal"
                                            data-bs-target="#detailTanam<?= $data['tanam_id'] ?>">
                                            <iconify-icon icon="solar:eye-bold-duotone" height="20"></iconify-icon>
                                        </a>
                                        <?php
                                        $isMandor     = $_SESSION['role'] == 'mandor';
                                        $isPimpinan   = $_SESSION['role'] == 'pimpinan';
                                        $isPending    = $data['status'] == 'pending';
                                        ?>

                                        <?php if (
                                            !$isPimpinan &&                 // Pimpinan tidak boleh sama sekali
                                            (
                                                !$isMandor ||               // Jika bukan mandor → boleh
                                                ($isMandor && $isPending)   // Jika mandor → hanya boleh kalau pending
                                            )
                                        ) { ?>

                                            <a class="btne btn-edit fw-medium"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editTanam<?= $data['tanam_id'] ?>">
                                                <iconify-icon icon="solar:pen-2-bold-duotone" height="20"></iconify-icon>
                                            </a>

                                            <a href="../function/fungsi_penanaman.php?aksi=hapus&tanam_id=<?= $data['tanam_id'] ?>"
                                                class="btne btn-hapus fw-medium">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone" height="20"></iconify-icon>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- ================= MODAL REJECT ================= -->
                            <div class="modal fade" id="modalReject<?= $data['tanam_id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Alasan Penolakan</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <form action="../function/fungsi_penanaman.php?aksi=reject" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="tanam_id" value="<?= $data['tanam_id'] ?>">
                                                <label>Alasan</label>
                                                <textarea name="alasan_reject" class="form-control" rows="3"></textarea>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Kirim Reject</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>

                            <!-- ================= MODAL DETAIL ================= -->
                            <div class="modal fade" id="detailTanam<?= $data['tanam_id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content shadow-lg border-0">

                                        <!-- HEADER -->
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title text-white fw-semibold">
                                                <i class="bi bi-geo-alt-fill me-2"></i>Detail Data Penanaman
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <!-- BODY -->
                                        <div class="modal-body bg-light">

                                            <?php
                                            $total = $data['jumlah_ajir'];
                                            $persen = $total > 0
                                                ? round(($data['jumlah_tanam'] / $total) * 100, 2)
                                                : 0;
                                            ?>

                                            <div class="row g-4">

                                                <!-- FOTO -->
                                                <div class="col-md-4">
                                                    <div class="card border-0 shadow-sm">
                                                        <div class="card-body text-center">

                                                            <div style="
                                                                width:100%;
                                                                height:280px;
                                                                background:#f8f9fa;
                                                                display:flex;
                                                                align-items:center;
                                                                justify-content:center;
                                                                border-radius:10px;
                                                                overflow:hidden;
                                                            ">
                                                                <img src="../gambar_tanam/<?= $data['foto_tanam'] ?>"
                                                                    style="
                                                                        max-width:100%;
                                                                        max-height:100%;
                                                                        object-fit:contain;
                                                                    ">
                                                            </div>

                                                            <div class="mt-2 text-muted small">
                                                                Foto Dokumentasi Penanaman
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- INFORMASI UTAMA -->
                                                <div class="col-md-8">

                                                    <div class="card border-0 shadow-sm mb-3">
                                                        <div class="card-body">

                                                            <h4 class="fw-bold text-primary mb-2">
                                                                BKPH <?= $data['nama_bkph'] ?> ( RPH <?= $data['rph'] ?>)
                                                            </h4>

                                                            <div class="text-muted mb-3">
                                                                •
                                                                <?= date('d F Y', strtotime($data['tanggal'])) ?>
                                                            </div>

                                                            <div class="d-flex gap-2 flex-wrap">
                                                                <span class="badge bg-danger px-4 py-2 fs-6 mb-2 shadow-sm">
                                                                    <?= number_format($data['jumlah_ajir'] ?? 0) ?> Ajir tersedia
                                                                </span>
                                                                <br>
                                                                <span class="badge bg-warning px-4 py-2 fs-6 mb-2 shadow-sm">
                                                                    <?= number_format($data['jumlah_tanam'] ?? 0) ?> Pohon ditanam
                                                                </span>
                                                                <span class="badge bg-success px-4 py-2 fs-6 mb-2 shadow-sm">
                                                                    <?= $persen ?> % Selesai
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <!-- DETAIL DATA -->
                                                    <div class="card border-0 shadow-sm">
                                                        <div class="card-body">

                                                            <div class="row mb-2">
                                                                <div class="col-md-4 text-muted">Mandor</div>
                                                                <div class="col-md-8 fw-semibold">
                                                                    <?= $data['nama_mandor'] ?>
                                                                </div>
                                                            </div>

                                                            <div class="row mb-2">
                                                                <div class="col-md-4 text-muted">Jenis Tanaman</div>
                                                                <div class="col-md-8 fw-semibold">
                                                                    <?= $data['jenis_tanaman'] ?>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-4 text-muted">Catatan</div>
                                                                <div class="col-md-8">
                                                                    <div class="p-3 bg-light border rounded">
                                                                        <?= $data['catatan'] ?: '-' ?>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                        <!-- FOOTER -->
                                        <div class="modal-footer bg-white">
                                            <a target="_blank"
                                                href="../laporan/export_tanam.php?tanam_id=<?= $data['tanam_id'] ?>"
                                                class="btn btn-success px-4">
                                                <i class="bi bi-printer me-1"></i> Cetak Data
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


                            <!-- ================= MODAL EDIT ================= -->
                            <div class="modal fade" id="editTanam<?= $data['tanam_id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit Penanaman</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <form action="../function/fungsi_penanaman.php?aksi=edit"
                                                method="POST" enctype="multipart/form-data">

                                                <input type="hidden" name="tanam_id" value="<?= $data['tanam_id'] ?>">
                                                <input type="hidden" name="fotolama" value="<?= $data['foto_tanam'] ?>">

                                                <div class="row mb-3">
                                                    <div class="col-4 d-flex justify-content-center">
                                                        <img id="preview" src="../gambar_tanam/<?= $data['foto_tanam']; ?>" alt="Preview Foto" width="150">
                                                    </div>
                                                    <div class="col align-self-center">
                                                        <input type="file" class="form-control" name="foto" accept="image/*" oninput="preview.src = window.URL.createObjectURL(this.files[0])">
                                                        <div class="form-text">Maksimal ukuran file 2MB. Format: JPG, JPEG, PNG</div>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Jumlah Tanam</label>
                                                    <input type="number" name="jumlah_tanam"
                                                        class="form-control"
                                                        required
                                                        value="<?= $data['jumlah_tanam'] ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label>Sumber Bibit</label>
                                                    <input type="text" name="sumber_bibit"
                                                        class="form-control"
                                                        required
                                                        value="<?= $data['sumber_bibit'] ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label>Tanggal</label>
                                                    <input type="date" name="tanggal"
                                                        class="form-control"
                                                        required
                                                        value="<?= $data['tanggal'] ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label>Catatan</label>
                                                    <textarea name="catatan" rows="3"
                                                        class="form-control" required><?= $data['catatan'] ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Data Ajir</label>
                                                    <select name="ajir_id" class="form-select selectinput" required>
                                                        <option value="">-- Pilih Lokasi --</option>

                                                        <?php
                                                        $role      = $_SESSION['role'] ?? '';
                                                        $mandor_id = $profil['mandor_id'] ?? '';

                                                        $sql = "
                                                        SELECT 
                                                            a.ajir_id,
                                                            a.jumlah_ajir,
                                                            da.tahun_tanam,
                                                            b.nama_bkph,
                                                            b.rph,
                                                            b.petak,
                                                            b.mandor_id
                                                        FROM ajir a
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

                                                            // ✅ SELECTED SAAT EDIT
                                                            $selected = ($d['ajir_id'] == ($data['ajir_id'] ?? '')) ? 'selected' : '';
                                                        ?>
                                                            <option value="<?= $d['ajir_id']; ?>" <?= $selected; ?>>
                                                                <?= $d['nama_bkph']; ?> |
                                                                RPH <?= $d['rph']; ?> |
                                                                Petak <?= $d['petak']; ?> |
                                                                Tahun <?= $d['tahun_tanam']; ?> |
                                                                Ajir <?= number_format($d['jumlah_ajir']); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>


                                                <div class="mb-3">
                                                    <label>Status</label>
                                                    <?php if ($isMandor): ?>
                                                        <!-- Mandor hanya melihat -->
                                                        <input type="text" class="form-control"
                                                            value="<?= ucfirst($data['status']) ?>"
                                                            disabled>

                                                        <!-- Supaya tetap terkirim saat submit -->
                                                        <input type="hidden" name="status" value="<?= $data['status'] ?>">

                                                    <?php else: ?>
                                                        <!-- Role lain bisa edit -->
                                                        <select name="status" class="form-select">
                                                            <option value="pending" <?= $data['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                            <option value="verified" <?= $data['status'] == 'verified' ? 'selected' : '' ?>>Verified</option>
                                                            <option value="rejected" <?= $data['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                                        </select>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="modal-footer">
                                                    <!-- INI YANG PENTING -->
                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                                        Batal
                                                    </button>

                                                    <button type="submit" class="btn btn-success">
                                                        Simpan
                                                    </button>
                                                </div>

                                            </form>
                                        </div>

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