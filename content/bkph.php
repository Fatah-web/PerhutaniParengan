<?php /** @var mysqli $conn */ // 🔥 biar VS Code tidak merah ?>
<div class="col d-flex align-items-stretch">
    <div class="card w-100">
        <div class="card-body">

            <!-- HEADER -->
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div>

                    <?php if ($_SESSION['role'] == 'admin') { ?>

                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formBKPHModal">
                            <iconify-icon icon="solar:add-square-bold" height="25" style="vertical-align: -0.5em;"></iconify-icon> Tambah
                        </button>

                    <?php } ?>

                    <h5 class="card-title fw-semibold mt-3">Data BKPH</h5>
                </div>
                <a href="../laporan/export_data_bkph.php" target="_blank" class="btn btn-success">
                    <iconify-icon icon="solar:printer-bold-duotone" height="25" style="vertical-align: -0.5em;"></iconify-icon>
                    Cetak
                </a>
            </div>

            <!-- ================= MODAL TAMBAH ================= -->
            <div class="modal fade" id="formBKPHModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-white">Tambah BKPH</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="../function/fungsi_bkph.php?aksi=simpan" method="POST">

                                <div class="mb-3">
                                    <label>Nama BKPH</label>
                                    <input type="text"
                                        name="nama_bkph"
                                        class="form-control"
                                        placeholder="Contoh: BKPH Malo"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label>RPH</label>
                                    <input type="text"
                                        name="rph"
                                        class="form-control"
                                        placeholder="Contoh: RPH Malo Barat"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label>Petak</label>
                                    <input type="text"
                                        name="petak"
                                        class="form-control"
                                        placeholder="Contoh: 12A"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label>Luas Baku (Ha)</label>
                                    <input type="text"
                                        name="luas_baku"
                                        class="form-control"
                                        placeholder="Contoh: 2.5 Ha"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label>Rencana Tanam</label>
                                    <input type="text"
                                        name="rencana_tanam"
                                        class="form-control"
                                        placeholder="Contoh: 2026"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label>Jenis Tanaman</label>
                                    <input type="text"
                                        name="jenis_tanaman"
                                        class="form-control"
                                        placeholder="Contoh: Jati"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label>Jarak Tanam</label>
                                    <input type="text"
                                        name="jarak_tanam"
                                        class="form-control"
                                        placeholder="Contoh: 3 x 3 meter"
                                        required>
                                </div>

                            

                                <div class="mb-3">
                                    <label>Mandor</label>
                                    <select name="mandor_id" class="form-select selectinput" required>
                                        <option value="">-- Pilih Mandor --</option>
                                        <?php
                                        $mandor = mysqli_query($conn, "SELECT * FROM mandor");
                                        while ($m = mysqli_fetch_array($mandor)) {
                                        ?>
                                            <option value="<?= $m['mandor_id'] ?>">
                                                <?= $m['nama'] ?> -   <?= $m['bkph'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ================= END TAMBAH ================= -->

            <!-- ================= TABEL ================= -->
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama BKPH</th>
                            <th>RPH</th>
                            <th>Petak</th>
                            <th>Jenis Tanaman</th>
                            <th>Mandor</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $role      = $_SESSION['role'] ?? '';
                        $mandor_id = $profil['mandor_id'] ?? '';
                        $no = 1;

                        /* =========================
                        QUERY DASAR
                        ========================= */
                        $sql = "
                            SELECT 
                                b.*,
                                m.nama
                            FROM bkph b
                            LEFT JOIN mandor m 
                                ON b.mandor_id = m.mandor_id
                        ";

                        /* =========================
                        FILTER DINAMIS
                        ========================= */
                        $where = [];

                        /* Jika role mandor → hanya lihat datanya sendiri */
                        if ($role === 'mandor' && !empty($mandor_id)) {
                            $where[] = "b.mandor_id = '$mandor_id'";
                        }

                        /* Gabungkan WHERE jika ada */
                        if (!empty($where)) {
                            $sql .= " WHERE " . implode(" AND ", $where);
                        }

                        /* ORDER BY selalu terakhir */
                        $sql .= " ORDER BY b.nama_bkph ASC";

                        /* Eksekusi */
                        $query = mysqli_query($conn, $sql);

                        if (!$query) {
                            die("Query Error: " . mysqli_error($conn));
                        }

                        while ($data = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $data['nama_bkph'] ?></td>
                                <td><?= $data['rph'] ?></td>
                                <td><?= $data['petak'] ?></td>
                                <td><?= $data['jenis_tanaman'] ?></td>
                                <td><?= $data['nama'] ?></td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a href="#" class="btne btn-detail" data-bs-toggle="modal" data-bs-target="#detailBKPH<?= $data['bkph_id'] ?>">
                                            <iconify-icon icon="solar:eye-bold-duotone" height="20"></iconify-icon>
                                        </a>
                                        <?php
                                        $role = $_SESSION['role'] ?? '';
                                        ?>

                                        <?php if (!in_array($role, ['mandor', 'asper','pimpinan'])) : ?>
                                            <a href="#" class="btne btn-edit fw-medium"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editBKPH<?= $data['bkph_id'] ?>">
                                                <iconify-icon icon="solar:pen-2-bold-duotone" height="20"></iconify-icon>
                                            </a>

                                            <a href="../function/fungsi_bkph.php?aksi=hapus&bkph_id=<?= $data['bkph_id'] ?>"
                                                class="btne btn-hapus fw-medium">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone" height="20"></iconify-icon>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- ================= MODAL DETAIL ================= -->
                            <div class="modal fade" id="detailBKPH<?= $data['bkph_id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content shadow">

                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title text-white">
                                                <i class="bi bi-info-circle me-2"></i>Detail Data BKPH
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="container-fluid">

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="card border-0 bg-light h-100">
                                                            <div class="card-body">
                                                                <h6 class="text-muted mb-3">Informasi Lokasi</h6>
                                                                <p class="mb-2"><strong>Nama BKPH:</strong><br><?= $data['nama_bkph'] ?></p>
                                                                <p class="mb-2"><strong>RPH:</strong><br><?= $data['rph'] ?></p>
                                                                <p class="mb-2"><strong>Petak:</strong><br><?= $data['petak'] ?></p>
                                                                <p class="mb-0"><strong>Luas Baku:</strong><br><?= $data['luas_baku'] ?> Ha</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="card border-0 bg-light h-100">
                                                            <div class="card-body">
                                                                <h6 class="text-muted mb-3">Informasi Tanam</h6>
                                                                <p class="mb-2"><strong>Rencana Tanam:</strong><br><?= $data['rencana_tanam'] ?></p>
                                                                <p class="mb-2"><strong>Jenis Tanaman:</strong><br><?= $data['jenis_tanaman'] ?></p>
                                                                <p class="mb-2"><strong>Jarak Tanam:</strong><br><?= $data['jarak_tanam'] ?></p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="card border-0 bg-light">
                                                            <div class="card-body">
                                                                <h6 class="text-muted mb-2">Penanggung Jawab</h6>
                                                                <span class="badge bg-primary fs-6 px-3 py-2">
                                                                    <?= $data['nama'] ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>

                                        <div class="modal-footer bg-light">
                                            <a target="_blank"
                                                href="../laporan/export_bkph.php?bkph_id=<?= $data['bkph_id'] ?>"
                                                class="btn btn-success px-4">
                                                <i class="bi bi-printer me-1"></i> Cetak Data
                                            </a>
                                            <button class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                                <i class="bi bi-x-circle me-1"></i> Tutup
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <!-- ================= MODAL EDIT ================= -->
                            <div class="modal fade" id="editBKPH<?= $data['bkph_id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit BKPH</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <form action="../function/fungsi_bkph.php?aksi=edit" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="bkph_id" value="<?= $data['bkph_id'] ?>">

                                                <div class="mb-3">
                                                    <label>Nama BKPH</label>
                                                    <input type="text" name="nama_bkph" class="form-control" value="<?= $data['nama_bkph'] ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label>RPH</label>
                                                    <input type="text" name="rph" class="form-control" value="<?= $data['rph'] ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label>Petak</label>
                                                    <input type="text" name="petak" class="form-control" value="<?= $data['petak'] ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label>Luas Baku</label>
                                                    <input type="text" name="luas_baku" class="form-control" value="<?= $data['luas_baku'] ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label>Rencana Tanam</label>
                                                    <input type="text" name="rencana_tanam" class="form-control" value="<?= $data['rencana_tanam'] ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label>Jenis Tanaman</label>
                                                    <input type="text" name="jenis_tanaman" class="form-control" value="<?= $data['jenis_tanaman'] ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label>Jarak Tanam</label>
                                                    <input type="text" name="jarak_tanam" class="form-control" value="<?= $data['jarak_tanam'] ?>">
                                                </div>


                                                <div class="mb-3">
                                                    <label>Mandor</label>
                                                    <select name="mandor_id" class="form-select selectinput">
                                                        <?php
                                                        $mandor2 = mysqli_query($conn, "SELECT * FROM mandor");
                                                        while ($m2 = mysqli_fetch_array($mandor2)) {
                                                        ?>
                                                            <option value="<?= $m2['mandor_id'] ?>" <?= $m2['mandor_id'] == $data['mandor_id'] ? 'selected' : '' ?>>
                                                                <?= $m2['nama'] ?> - <?= $m2['bkph'] ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">Simpan</button>
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