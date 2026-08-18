<?php /** @var mysqli $conn */ // 🔥 biar VS Code tidak merah ?>
<div class="col d-flex align-items-stretch">
    <div class="card w-100">
        <div class="card-body">

            <div class="d-flex align-items-center justify-content-between mb-1">
                <div>
                    <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'mandor') { ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formAjirModal">
                            <iconify-icon icon="solar:add-square-bold" height="25" style="vertical-align: -0.5em;"></iconify-icon> Tambah
                        </button>
                    <?php } ?>
                    <h5 class="card-title fw-semibold mt-3">Data Ajir</h5>
                </div>
                <div class="modal fade" id="modalCetakDataAwal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <form action="../laporan/export_data_awal.php" method="GET" target="_blank">
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
                  <button type="submit" class="btn btn-success">
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

            <!-- ================= Modal Tambah ================= -->
            <div class="modal fade" id="formAjirModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-white">Tambah Data Ajir</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">

                            <form action="../function/fungsi_ajir.php?aksi=simpan" method="POST" enctype="multipart/form-data">

                                <div class="row mb-3">
                                    <div>
                                        <h4>Informasi Ajir</h4>
                                        <hr>
                                    </div>
                                    <div class="col-4 d-flex justify-content-center">
                                        <img id="preview" src="" alt="Preview Foto" width="150">
                                    </div>
                                    <div class="col align-self-center">
                                        <input type="file" id="foto" class="form-control" name="foto" accept="image/*" oninput="preview.src = window.URL.createObjectURL(this.files[0])">
                                        <div class="form-text">Maksimal ukuran file 2MB. Format: JPG, JPEG, PNG</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label>Jumlah Ajir</label>
                                    <input type="number" class="form-control" name="jumlah_ajir" placeholder="Contoh: 1200" required>
                                </div>

                                <div class="mb-3">
                                    <label>Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" required>
                                </div>

                                <div class="mb-3">
                                    <label>Catatan</label>
                                    <textarea class="form-control" name="catatan" placeholder="Contoh: Pembuatan Ajir lancar" rows="4"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Data Lubang</label>

                                    <select name="lubang_id" class="form-select selectinput" required>
                                        <option value="">-- Pilih Lokasi --</option>

                                        <?php
                                        $role = $_SESSION['role'];
                                        $mandor_id = $profil['mandor_id'] ?? null;

                                        $sql_lubang = "
                                        SELECT
                                            l.lubang_id,
                                            l.jumlah_lubang,
                                            l.tanggal,
                                            da.tahun_tanam,
                                            b.nama_bkph,
                                            b.rph,
                                            b.petak,
                                            b.mandor_id
                                        FROM lubang l
                                        LEFT JOIN data_awal da 
                                            ON l.data_awal_id = da.data_awal_id
                                        LEFT JOIN bkph b 
                                            ON da.bkph_id = b.bkph_id
                                    ";

                                        // 🔥 Filter jika mandor
                                        if (strtolower(trim($role)) === 'mandor' && !empty($mandor_id)) {
                                            $sql_lubang .= " WHERE b.mandor_id = $mandor_id";
                                        }

                                        $sql_lubang .= " ORDER BY b.nama_bkph ASC, b.rph ASC";

                                        $result_lubang = mysqli_query($conn, $sql_lubang);

                                        if (!$result_lubang) {
                                            die("Query Error: " . mysqli_error($conn));
                                        }

                                        while ($d = mysqli_fetch_assoc($result_lubang)) {
                                        ?>
                                            <option value="<?= $d['lubang_id']; ?>">
                                                <?= $d['nama_bkph']; ?> |
                                                RPH <?= $d['rph']; ?> |
                                                Petak <?= $d['petak']; ?> |
                                                Tahun <?= $d['tahun_tanam']; ?> |
                                                <?= number_format($d['jumlah_lubang']); ?> Lubang tersedia
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
            <!-- ================= End Modal Tambah ================= -->

            <!-- ================= Table ================= -->
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
                            <th>Jumlah Acir</th>
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

                        $role      = trim(strtolower($_SESSION['role'] ?? ''));
                        $mandor_id = $profil['mandor_id'] ?? '';
                        $isMandor = ($role === 'mandor');

                        $sql = "
                                SELECT 
                                    l.lubang_id,
                                    l.jumlah_lubang,

                                    a.ajir_id,
                                    a.jumlah_ajir,
                                    a.foto_ajir,
                                    a.tanggal,
                                    a.catatan,
                                    a.status,

                                    da.data_awal_id,
                                    da.tahun_tanam,
                                    da.target_pohon,

                                    b.bkph_id,
                                    b.nama_bkph,
                                    b.rph,
                                    b.petak,
                                    b.jenis_tanaman,
                                    b.jarak_tanam,

                                    m.mandor_id,
                                    m.nama AS nama_mandor,
                                    m.nip,
                                    m.no_hp

                                FROM ajir a
                                LEFT JOIN lubang l ON a.lubang_id = l.lubang_id
                                LEFT JOIN data_awal da ON l.data_awal_id = da.data_awal_id
                                LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
                                LEFT JOIN mandor m ON b.mandor_id = m.mandor_id
                            ";

                        /* =========================
                            FILTER DINAMIS
                            ========================= */
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
                                <td><?= $no++; ?></td>

                                <td>
                                    <img src="../gambar_ajir/<?= $data['foto_ajir']; ?>"
                                        width="100"
                                        style="object-fit: cover; aspect-ratio:1/1; border-radius:6px;">
                                </td>

                                <td><?= date('d-m-Y', strtotime($data['tanggal'])); ?></td>

                                <td>
                                    <b>BKPH:<?= $data['nama_bkph']; ?></b> <br>RPH-<?= $data['rph']; ?><br>
                                    <small>Petak-<?= $data['petak']; ?></small>
                                </td>
                                <td><?= $data['nama_mandor']; ?></td>
                                <td>
                                    <b>Tanam Tahun:<?= $data['tahun_tanam']; ?></b><br>
                                    <small>Jenis-<?= $data['jenis_tanaman']; ?> <br>
                                        Target <?= number_format((int)($data['target_pohon'] ?? 0)); ?> Pohon </small>

                                </td>
                                <td>
                                    <?= number_format($data['jumlah_ajir'] ?? 0); ?> Acir
                                </td>

                                <td>
                                    <button type="button" class="btn btn-<?= $badgeColor ?>" style="width:115px;">
                                        <?= ucfirst($data['status']) ?>
                                    </button>

                                </td>
                                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'asper') { ?>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <?php if ($data['status'] == 'pending') { ?>

                                                <!-- VERIFIKASI -->
                                                <a href="../function/fungsi_ajir.php?aksi=verifikasi&ajir_id=<?= $data['ajir_id']; ?>"
                                                    class="btn btn-success btn-sm d-flex align-items-center justify-content-center btn-verif"
                                                    style="width:32px; height:32px;">
                                                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                                                </a>

                                                <!-- REJECT -->
                                                <a class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalReject<?= $data['ajir_id'] ?>"
                                                    style="width:32px; height:32px;">
                                                    <iconify-icon icon="solar:close-circle-bold-duotone" width="20"></iconify-icon>
                                                </a>

                                            <?php } else { ?>
                                                <small class="text-muted">—</small>
                                            <?php } ?>
                                        </div>
                                    </td>
                                <?php } ?>
                                <!-- Kolom Verifikasi -->


                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a href="#" class="btne btn-detail" data-bs-toggle="modal" data-bs-target="#detailAjir<?= $data['ajir_id'] ?>">
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

                                            <a href="#" class="btne btn-edit fw-medium" data-bs-toggle="modal" data-bs-target="#editAjir<?= $data['ajir_id'] ?>">
                                                <iconify-icon icon="solar:pen-2-bold-duotone" height="20"></iconify-icon>
                                            </a>
                                            <a href="../function/fungsi_ajir.php?aksi=hapus&ajir_id=<?= $data['ajir_id']; ?>"
                                                class="btne btn-hapus fw-medium">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone" height="20"></iconify-icon>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Reject -->
                            <div class="modal fade" id="modalReject<?= $data['ajir_id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">

                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Alasan Penolakan</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <form action="../function/fungsi_ajir.php?aksi=reject" method="POST">
                                            <div class="modal-body">

                                                <!-- LANGSUNG VALUE -->
                                                <input type="hidden" name="ajir_id" value="<?= $data['ajir_id'] ?>">

                                                <div class="mb-3">
                                                    <label class="form-label">Alasan</label>
                                                    <textarea name="alasan_reject" class="form-control" rows="3" required></textarea>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Kirim Reject</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>

                            <!-- ================= Modal Detail Ajir ================= -->
                            <div class="modal fade" id="detailAjir<?= $data['ajir_id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow-lg">

                                        <!-- HEADER -->
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title fw-semibold text-white">
                                                <i class="bi bi-geo-alt-fill me-2"></i>Detail Data Ajir Tanam
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body bg-light">
                                            <div class="container-fluid">

                                                <!-- ================= RINGKASAN ================= -->
                                                <div class="card border-0 shadow-sm mb-4">
                                                    <div class="card-body">
                                                        <div class="row align-items-center">

                                                            <div class="col-md-3 text-center">
                                                                <img src="../gambar_ajir/<?= $data['foto_ajir'] ?>"
                                                                    class="rounded shadow-sm img-fluid"
                                                                    style="max-height:180px; object-fit:cover;"
                                                                    alt="Foto Lokasi">
                                                            </div>

                                                            <div class="col-md-9 mt-3 mt-md-0">
                                                                <h3 class="fw-bold text-primary mb-1">
                                                                    BKPH <?= $data['nama_bkph'] ?> (RPH <?= $data['rph'] ?>)

                                                                </h3>

                                                                <div class="text-muted mb-2">
                                                                    <i class="bi bi-calendar-event me-1"></i>
                                                                    <?= date('d F Y', strtotime($data['tanggal'])) ?>
                                                                </div>
                                                                <div class="d-flex gap-2 flex-wrap">
                                                                    <span class="badge bg-danger px-4 py-2 fs-6 mb-2 shadow-sm">
                                                                        <?= number_format($data['jumlah_lubang'] ?? 0) ?> Lubang tersedia
                                                                    </span>
                                                                    <span class="badge bg-warning px-4 py-2 fs-6 mb-2 shadow-sm">
                                                                        <?= number_format($data['jumlah_ajir'] ?? 0) ?> Ajir telah dibuat
                                                                    </span>
                                                                    <?php
                                                                    $total = $data['jumlah_lubang'];
                                                                    $persen = $total > 0
                                                                        ? round(($data['jumlah_ajir'] / $total) * 100, 2)
                                                                        : 0;
                                                                    ?>
                                                                    <span class="badge bg-success px-4 py-2 fs-6 mb-2 shadow-sm">
                                                                        <?= $persen ?> % Selesai
                                                                    </span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- ================= INFORMASI DETAIL ================= -->
                                                <div class="row g-4">

                                                    <!-- Informasi Lokasi -->
                                                    <div class="col-md-6">
                                                        <div class="card border-0 shadow-sm h-100">
                                                            <div class="card-body">
                                                                <h6 class="text-uppercase text-muted mb-3">Informasi Lokasi</h6>

                                                                <div class="mb-2">
                                                                    <small class="text-muted d-block">Petak</small>
                                                                    <div class="fw-semibold"><?= $data['petak'] ?></div>
                                                                </div>

                                                                <div class="mb-2">
                                                                    <small class="text-muted d-block">Tahun Tanam</small>
                                                                    <div class="fw-semibold"><?= $data['tahun_tanam'] ?></div>
                                                                </div>

                                                                <div>
                                                                    <small class="text-muted d-block">Jenis Tanaman</small>
                                                                    <div class="fw-semibold"><?= $data['jenis_tanaman'] ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Informasi Mandor + Catatan -->
                                                    <div class="col-md-6">
                                                        <div class="card border-0 shadow-sm h-100">
                                                            <div class="card-body">
                                                                <h6 class="text-uppercase text-muted mb-3">Petugas Lapangan</h6>

                                                                <div class="mb-3">
                                                                    <small class="text-muted d-block">Mandor</small>
                                                                    <div class="fw-semibold"><?= $data['nama_mandor'] ?></div>
                                                                </div>

                                                                <h6 class="text-uppercase text-muted mt-4 mb-2">Catatan Lapangan</h6>
                                                                <div class="p-3 bg-white rounded border small">
                                                                    <?= $data['catatan']
                                                                        ? nl2br(htmlspecialchars($data['catatan']))
                                                                        : '<span class="text-muted">Tidak ada catatan tambahan</span>'; ?>
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
                                                href="../laporan/export_ajir.php?ajir_id=<?= $data['ajir_id'] ?>"
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



                            <!-- ================= Modal Edit ================= -->
                            <div class="modal fade" id="editAjir<?= $data['ajir_id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit Ajir</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="../function/fungsi_ajir.php?aksi=edit" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="ajir_id" value="<?= $data['ajir_id'] ?>">
                                                <input type="hidden" name="fotolama" value="<?= $data['foto_ajir'] ?>">
                                                <div class="row mb-3">
                                                    <div class="col-4 d-flex justify-content-center">
                                                        <img id="preview" src="../gambar_ajir/<?= $data['foto_ajir']; ?>" alt="Preview Foto" width="150">
                                                    </div>
                                                    <div class="col align-self-center">
                                                        <input type="file" class="form-control" name="foto" accept="image/*" oninput="preview.src = window.URL.createObjectURL(this.files[0])">
                                                        <div class="form-text">Maksimal ukuran file 2MB. Format: JPG, JPEG, PNG</div>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Jumlah Ajir</label>
                                                    <input type="number" class="form-control"
                                                        name="jumlah_ajir"
                                                        value="<?= $data['jumlah_ajir'] ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Tanggal</label>
                                                    <input type="date" class="form-control"
                                                        name="tanggal"
                                                        value="<?= $data['tanggal'] ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Catatan</label>
                                                    <textarea class="form-control"
                                                        name="catatan"><?= $data['catatan'] ?></textarea>
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

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Data Lubang</label>

                                                    <select name="lubang_id" class="form-select selectinput" required>
                                                        <option value="">-- Pilih Lokasi --</option>

                                                        <?php
                                                        $role = $_SESSION['role'];
                                                        $mandor_id = $profil['mandor_id'] ?? null;

                                                        $sql_lubang = "
                                                                        SELECT
                                                                            l.lubang_id,
                                                                            l.jumlah_lubang,
                                                                            l.tanggal,
                                                                            da.tahun_tanam,
                                                                            b.nama_bkph,
                                                                            b.rph,
                                                                            b.petak,
                                                                            b.mandor_id
                                                                        FROM lubang l
                                                                        LEFT JOIN data_awal da 
                                                                            ON l.data_awal_id = da.data_awal_id
                                                                        LEFT JOIN bkph b 
                                                                            ON da.bkph_id = b.bkph_id
                                                                    ";

                                                        // 🔥 Filter jika mandor
                                                        if (strtolower(trim($role)) === 'mandor' && !empty($mandor_id)) {
                                                            $sql_lubang .= " WHERE b.mandor_id = $mandor_id";
                                                        }

                                                        $sql_lubang .= " ORDER BY b.nama_bkph ASC, b.rph ASC";

                                                        $result_lubang = mysqli_query($conn, $sql_lubang);

                                                        if (!$result_lubang) {
                                                            die("Query Error: " . mysqli_error($conn));
                                                        }

                                                        while ($d = mysqli_fetch_assoc($result_lubang)) {

                                                            // tetap selected saat edit
                                                            $selected = ($d['lubang_id'] == $data['lubang_id']) ? 'selected' : '';
                                                        ?>
                                                            <option value="<?= $d['lubang_id']; ?>" <?= $selected; ?>>
                                                                <?= $d['nama_bkph']; ?> |
                                                                RPH <?= $d['rph']; ?> |
                                                                Petak <?= $d['petak']; ?> |
                                                                Tahun <?= $d['tahun_tanam']; ?> |
                                                                <?= number_format($d['jumlah_lubang']); ?> Lubang tersedia
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">Simpan</button>
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