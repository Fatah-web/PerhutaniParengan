<?php /** @var mysqli $conn */ // 🔥 biar VS Code tidak merah ?>
<div class="col d-flex align-items-stretch">
  <div class="card w-100">
    <div class="card-body">

      <div class="d-flex align-items-center justify-content-between mb-1">
        <div>
          <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'mandor') { ?>

            <button type="button" class="btn btn-primary"
              data-bs-toggle="modal"
              data-bs-target="#formLubangModal">
              <iconify-icon icon="solar:add-square-bold" height="25" style="vertical-align: -0.5em;"></iconify-icon>
              Tambah
            </button>

          <?php } ?>
          <h5 class="card-title fw-semibold mt-3">Data Lubang</h5>
        </div>
        <div class="modal fade" id="modalCetakDataAwal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <form action="../laporan/export_data_lubang.php" method="GET" target="_blank">
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
          <iconify-icon icon="solar:printer-bold-duotone" height="25" style="vertical-align: -0.5em;" ></iconify-icon>
          Cetak
        </button>
      </div>

      <!-- ================= Modal Tambah ================= -->
      <div class="modal fade" id="formLubangModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <h5 class="modal-title text-white">Tambah Data Lubang</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

              <form action="../function/fungsi_lubang.php?aksi=simpan" method="POST" enctype="multipart/form-data">

                <div class="row mb-3">
                  <div>
                    <h4>Informasi Lubang</h4>
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
                  <label>Jumlah Lubang</label>
                  <input type="number" class="form-control" name="jumlah_lubang" placeholder="Contoh: 1200" required>
                </div>

                <div class="mb-3">
                  <label>Tanggal</label>
                  <input type="date" class="form-control" name="tanggal" required>
                </div>

                <div class="mb-3">
                  <label>Catatan</label>
                  <textarea class="form-control" name="catatan" placeholder="Contoh: pembuatan lubang lancar" rows="4"></textarea>
                </div>

                <div class="mb-3">
                  <label>Data Awal</label>
                  <select name="data_awal_id" class="form-select selectinput" required>
                    <option value="">-- Pilih Lokasi --</option>
                    <?php
                    $role = $_SESSION['role'];
                    $mandor_id = $profil['mandor_id'] ?? null;

                    $query = "
                        SELECT 
                            da.*,
                            b.nama_bkph,
                            b.rph,
                            b.petak
                        FROM data_awal da
                        LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
                    ";

                    if ($role == 'mandor') {
                      $query .= " WHERE b.mandor_id = '$mandor_id'";
                    }
                    $query .= " ORDER BY b.nama_bkph ASC, b.rph ASC";
                    $da = mysqli_query($conn, $query);
                    while ($d = mysqli_fetch_array($da)) {
                    ?>
                      <option value="<?= $d['data_awal_id']; ?>">
                        <?= $d['nama_bkph']; ?> | RPH <?= $d['rph']; ?> | Petak <?= $d['petak']; ?>| Tahun <?= $d['tahun_tanam']; ?> | Target <?= number_format($d['target_pohon']); ?> Pohon
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
              <th>Jumlah lubang</th>
              <th>Status</th>
              <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'asper') { ?>
                <th class="text-center">Verifikasi</th>
              <?php } ?>
              <th class="text-center">Aksi</th>
            </tr>

          </thead>
          <?php
          $no = 1;
          $role      = $_SESSION['role'] ?? '';
          $mandor_id = $profil['mandor_id'] ?? '';
          $isMandor = ($role === 'mandor');

          $sql = "
              SELECT 
                  l.lubang_id,
                  l.jumlah_lubang,
                  l.foto_lokasi,
                  l.tanggal,
                  l.catatan,
                  l.status,

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

              FROM lubang l
              LEFT JOIN data_awal da ON l.data_awal_id = da.data_awal_id
              LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
              LEFT JOIN mandor m ON b.mandor_id = m.mandor_id
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

          while ($data = mysqli_fetch_assoc($query)) {

            $badgeColor = [
              'rejected' => 'danger',
              'pending'  => 'warning',
              'verified' => 'success'
            ][$data['status']] ?? 'secondary';
          ?>
            <tr>
              <td><?= $no++; ?></td>

              <td>
                <img src="../gambar_lubang/<?= $data['foto_lokasi']; ?>"
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
                  Target <?= number_format((int)($data['target_pohon'] ?? 0)) ?> Pohon</small>

              </td>
              <td><?= number_format($data['jumlah_lubang']?? 0); ?> Lubang</td>

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
                      <a href="../function/fungsi_lubang.php?aksi=verifikasi&lubang_id=<?= $data['lubang_id']; ?>"
                        class="btn btn-success btn-sm d-flex align-items-center justify-content-center btn-verif"
                        style="width:32px; height:32px;">
                        <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                      </a>

                      <!-- REJECT -->
                      <a class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                        data-bs-toggle="modal"
                        data-bs-target="#modalReject<?= $data['lubang_id'] ?>"
                        style="width:32px; height:32px;">
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

                  <!-- DETAIL (selalu tampil) -->
                  <a href="#" class="btne btn-detail"
                    data-bs-toggle="modal"
                    data-bs-target="#detailLubang<?= $data['lubang_id'] ?>">
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

                    <!-- EDIT -->
                    <a href="#" class="btne btn-edit fw-medium"
                      data-bs-toggle="modal"
                      data-bs-target="#editLubang<?= $data['lubang_id'] ?>">
                      <iconify-icon icon="solar:pen-2-bold-duotone" height="20"></iconify-icon>
                    </a>

                    <!-- HAPUS -->
                    <a href="../function/fungsi_lubang.php?aksi=hapus&lubang_id=<?= $data['lubang_id']; ?>"
                      class="btne btn-hapus fw-medium">
                      <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone" height="20"></iconify-icon>
                    </a>

                  <?php } ?>

                </div>
              </td>
            </tr>

            <!-- Modal Reject -->
            <div class="modal fade" id="modalReject<?= $data['lubang_id'] ?>" tabindex="-1">
              <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">

                  <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Alasan Penolakan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>

                  <form action="../function/fungsi_lubang.php?aksi=reject" method="POST">
                    <div class="modal-body">

                      <!-- LANGSUNG VALUE -->
                      <input type="hidden" name="lubang_id" value="<?= $data['lubang_id'] ?>">

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

            <!-- ================= Modal Detail Lubang ================= -->
            <div class="modal fade" id="detailLubang<?= $data['lubang_id'] ?>" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">

                  <!-- HEADER -->
                  <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white fw-semibold">
                      <i class="bi bi-geo-alt-fill me-2"></i>Detail Data Lubang Tanam
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
                              <img src="../gambar_lubang/<?= $data['foto_lokasi'] ?>"
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
                                  Target <?= number_format($data['target_pohon']?? 0) ?> Pohon
                                </span>
                                <span class="badge bg-warning px-4 py-2 fs-6 mb-2 shadow-sm">
                                  <?= number_format($data['jumlah_lubang']?? 0) ?> Lubang dibuat
                                </span>
                                <?php
                                $total = $data['target_pohon'];
                                $persen = $total > 0
                                  ? round(($data['jumlah_lubang'] / $total) * 100, 2)
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
                      href="../laporan/export_lubang.php?lubang_id=<?= $data['lubang_id'] ?>"
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
            <div class="modal fade" id="editLubang<?= $data['lubang_id'] ?>" tabindex="-1">
              <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header bg-warning">
                    <h5 class="modal-title">Edit Lubang</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <form action="../function/fungsi_lubang.php?aksi=edit" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="lubang_id" value="<?= $data['lubang_id'] ?>">
                      <input type="hidden" name="fotolama" value="<?= $data['foto_lokasi'] ?>">
                      <div class="row mb-3">
                        <div class="col-4 d-flex justify-content-center">
                          <img id="preview" src="../gambar_lubang/<?= $data['foto_lokasi']; ?>" alt="Preview Foto" width="150">
                        </div>
                        <div class="col align-self-center">
                          <input type="file" class="form-control" name="foto" accept="image/*" oninput="preview.src = window.URL.createObjectURL(this.files[0])">
                          <div class="form-text">Maksimal ukuran file 2MB. Format: JPG, JPEG, PNG</div>
                        </div>
                      </div>

                      <div class="mb-3">
                        <label>Jumlah Lubang</label>
                        <input type="number" class="form-control"
                          name="jumlah_lubang"
                          value="<?= $data['jumlah_lubang'] ?>" required>
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
                        <label class="form-label fw-semibold">Data Awal</label>

                        <select name="data_awal_id" class="form-select selectinput" required>
                          <option value="">-- Pilih Lokasi --</option>

                          <?php
                          
                          $role = $profil['role'];
                          $mandor_id = $profil['mandor_id'] ?? null;

                          $sql_da = "
                                      SELECT 
                                          da.data_awal_id,
                                          da.tahun_tanam,
                                          da.target_pohon,
                                          b.nama_bkph,
                                          b.rph,
                                          b.petak,
                                          b.mandor_id
                                      FROM data_awal da
                                      LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
                                  ";

                          if (strtolower($role) == 'mandor' && !empty($mandor_id)) {
                            $sql_da .= " WHERE b.mandor_id = '$mandor_id'";
                          }

                          $sql_da .= " ORDER BY b.nama_bkph ASC, b.rph ASC";

                          $result_da = mysqli_query($conn, $sql_da);

                          if (!$result_da) {
                            die("Query Error: " . mysqli_error($conn));
                          }

                          while ($d = mysqli_fetch_assoc($result_da)) {

                            $selected = ($d['data_awal_id'] == $data['data_awal_id']) ? 'selected' : '';
                          ?>
                            <option value="<?= $d['data_awal_id']; ?>" <?= $selected; ?>>
                              <?= $d['nama_bkph']; ?>
                              | RPH <?= $d['rph']; ?>
                              | Petak <?= $d['petak']; ?>
                              | Th <?= $d['tahun_tanam']; ?>
                              | Target <?= number_format($d['target_pohon']); ?> Pohon
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