<?php /** @var mysqli $conn */ // 🔥 biar VS Code tidak merah ?>
<div class="col d-flex align-items-stretch">
  <div class="card w-100">
    <div class="card-body">

      <!-- HEADER -->
      <div class="d-flex align-items-center justify-content-between mb-1">
        <div>

          <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'asper') { ?>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formDataAwalModal">
              <iconify-icon icon="solar:add-square-bold" height="25" style="vertical-align: -0.5em;"></iconify-icon>
              Tambah
            </button>

          <?php } ?>

          <h5 class="card-title fw-semibold mt-3">Data Awal Penanaman</h5>
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

      <!-- ================= MODAL TAMBAH ================= -->
      <div class="modal fade" id="formDataAwalModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
          <div class="modal-content">
            <form action="../function/fungsi_data_awal.php?aksi=simpan" method="POST">

              <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Tambah Data Awal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">

                <div class="mb-3">
                  <label>BKPH (BKPH-RPH-Petak)</label>
                  <select name="bkph_id" class="form-select selectinput" required>
                    <option value="">-- Pilih BKPH / RPH / Petak --</option>
                    <?php
                    $bkph = mysqli_query($conn, "SELECT * FROM bkph ORDER BY nama_bkph, rph, petak");
                    while ($b = mysqli_fetch_assoc($bkph)) {
                      ?>
                      <option value="<?= $b['bkph_id'] ?>">
                        <?= $b['nama_bkph'] ?> - <?= $b['rph'] ?> - <?= $b['petak'] ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>

                <div class="mb-3">
                  <label>Tahun Tanam</label>
                  <input type="text" name="tahun_tanam" class="form-control" placeholder="Contoh: 2025" required>
                </div>

                <div class="mb-3">
                  <label>Tenaga Kerja</label>
                  <input type="number" name="tenaga_kerja" class="form-control" placeholder="Contoh: 4" required>
                </div>

                <div class="mb-3">
                  <label>Target Pohon</label>
                  <input type="number" name="target_pohon" class="form-control" placeholder="Contoh: 1200" required>
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


      <!-- ================= TABEL ================= -->
      <div class="table-responsive">
        <table id="example" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>No</th>
              <th>Tahun Tanam</th>
              <th>Target Pohon</th>
              <th>BKPH</th>
              <th>RPH</th>
              <th>Petak</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>

          <?php
          $role = $_SESSION['role'] ?? '';
          $mandor_id = $_SESSION['mandor_id'] ?? '';
          $no = 1;

          /* =========================
            QUERY DASAR (TANPA ORDER & WHERE DULU)
          ========================= */
          $sql = "
              SELECT 
                  da.data_awal_id,
                  da.tahun_tanam,
                  da.target_pohon,
                  da.tenaga_kerja,

                  b.bkph_id,
                  b.nama_bkph,
                  b.rph,
                  b.petak,
                  b.luas_baku,
                  b.rencana_tanam,
                  b.jenis_tanaman,
                  b.jarak_tanam,

                  m.mandor_id,
                  m.nama AS nama_mandor,
                  m.nip,
                  m.no_hp,
                  m.alamat,
                  m.status

              FROM data_awal da
              LEFT JOIN bkph b 
                  ON da.bkph_id = b.bkph_id
              LEFT JOIN mandor m 
                  ON b.mandor_id = m.mandor_id
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

          /* ORDER BY diletakkan terakhir */
          $sql .= " ORDER BY b.nama_bkph ASC, da.data_awal_id DESC";

          /* Eksekusi Query */
          $query = mysqli_query($conn, $sql);

          if (!$query) {
            die("Query Error: " . mysqli_error($conn));
          }

          while ($data = mysqli_fetch_assoc($query)) {
            ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= $data['tahun_tanam'] ?></td>
              <td><?= number_format((int) ($data['target_pohon'] ?? 0)) ?>Pohon</td>
              <td><?= $data['nama_bkph'] ?></td>
              <td><?= $data['rph'] ?></td>
              <td><?= $data['petak'] ?></td>

              <td class="text-center">
                <div class="d-inline-flex gap-1">
                  <a href="#" class="btne btn-detail" data-bs-toggle="modal"
                    data-bs-target="#detailDataAwal<?= $data['data_awal_id'] ?>">
                    <iconify-icon icon="solar:eye-bold-duotone" height="20"></iconify-icon>
                  </a>
                  <?php if ($_SESSION['role'] != 'mandor' && $_SESSION['role'] != 'pimpinan') { ?>
                    <a href="#" data-bs-toggle="modal" class="btne btn-edit fw-medium"
                      data-bs-target="#editDataAwal<?= $data['data_awal_id'] ?>">
                      <iconify-icon icon="solar:pen-2-bold-duotone" height="20"></iconify-icon>
                    </a>

                    <a href="../function/fungsi_data_awal.php?aksi=hapus&data_awal_id=<?= $data['data_awal_id'] ?>"
                      class="btne btn-hapus fw-medium">
                      <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone" height="20"></iconify-icon>
                    </a>
                  <?php } ?>
                </div>
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>


<!-- ================= MODAL DETAIL & EDIT (DI LUAR TABEL) ================= -->
<?php
mysqli_data_seek($query, 0);
while ($data = mysqli_fetch_assoc($query)) {
  ?>
  <!-- ================= MODAL EDIT ================= -->
  <div class="modal fade" id="editDataAwal<?= $data['data_awal_id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="../function/fungsi_data_awal.php?aksi=edit" method="POST">
          <input type="hidden" name="data_awal_id" value="<?= $data['data_awal_id'] ?>">

          <div class="modal-header bg-warning">
            <h5 class="modal-title">Edit Data Awal</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>



          <div class="modal-body">

            <div class="mb-3">
              <label>BKPH (BKPH-RPH-Petak)</label>
              <select name="bkph_id" class="form-select selectinput">
                <?php
                $bkph2 = mysqli_query($conn, "SELECT * FROM bkph");
                while ($b2 = mysqli_fetch_assoc($bkph2)) {
                  ?>
                  <option value="<?= $b2['bkph_id'] ?>" <?= $b2['bkph_id'] == $data['bkph_id'] ? 'selected' : '' ?>>
                    <?= $b2['nama_bkph'] ?> - <?= $b2['rph'] ?> - <?= $b2['petak'] ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="mb-3">
              <label>Tahun Tanam</label>
              <input type="text" name="tahun_tanam" class="form-control" value="<?= $data['tahun_tanam'] ?>" required>
            </div>

            <div class="mb-3">
              <label>Tenaga Kerja</label>
              <input type="number" name="tenaga_kerja" class="form-control" value="<?= $data['tenaga_kerja'] ?>" required>
            </div>

            <div class="mb-3">
              <label>Target Pohon</label>
              <input type="number" name="target_pohon" class="form-control" value="<?= $data['target_pohon'] ?>" required>
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

  <!-- MODAL DETAIL DATA AWAL -->
  <div class="modal fade" id="detailDataAwal<?= $data['data_awal_id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
      <div class="modal-content border-0 shadow-lg">

        <!-- Header -->
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-semibold text-white">
            <i class="bi bi-clipboard-data me-2"></i>Detail Data Awal Penanaman
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <!-- Body -->
        <div class="modal-body bg-light">
          <div class="container-fluid">

            <div class="row g-4">

              <!-- Informasi Umum -->
              <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                  <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-3">Informasi Umum</h6>

                    <div class="mb-3">
                      <small class="text-muted d-block">Tahun Tanam</small>
                      <div class="fw-semibold"><?= $data['tahun_tanam'] ?></div>
                    </div>

                    <div class="mb-3">
                      <small class="text-muted d-block">Target Pohon</small>
                      <div class="fw-semibold"><?= number_format($data['target_pohon']) ?> Pohon</div>
                    </div>

                    <div>
                      <small class="text-muted d-block">Mandor Penanggung Jawab</small>
                      <div class="fw-semibold"><?= $data['nama_mandor'] ?></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Informasi Lokasi -->
              <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                  <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-3">Informasi Lokasi</h6>

                    <div class="mb-3">
                      <small class="text-muted d-block">BKPH</small>
                      <div class="fw-semibold"><?= $data['nama_bkph'] ?></div>
                    </div>

                    <div class="mb-3">
                      <small class="text-muted d-block">RPH</small>
                      <div class="fw-semibold"><?= $data['rph'] ?></div>
                    </div>

                    <div>
                      <small class="text-muted d-block">Petak</small>
                      <div class="fw-semibold"><?= $data['petak'] ?></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Detail Teknis -->
              <div class="col-12">
                <div class="card border-0 shadow-sm">
                  <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-3">Detail Teknis Penanaman</h6>

                    <div class="row">
                      <div class="col-md-3 mb-3">
                        <small class="text-muted d-block">Luas Baku</small>
                        <div class="fw-semibold"><?= $data['luas_baku'] ?> Ha</div>
                      </div>

                      <div class="col-md-3 mb-3">
                        <small class="text-muted d-block">Jenis Tanaman</small>
                        <div class="fw-semibold"><?= $data['jenis_tanaman'] ?></div>
                      </div>

                      <div class="col-md-3 mb-3">
                        <small class="text-muted d-block">Jarak Tanam</small>
                        <div class="fw-semibold"><?= $data['jarak_tanam'] ?></div>
                      </div>

                      <div class="col-md-3 mb-3">
                        <small class="text-muted d-block">Tenaga Kerja</small>
                        <div class="fw-semibold"><?= $data['tenaga_kerja'] ?> Orang</div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

            </div>

          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer bg-white">
          <a target="_blank" href="../laporan/export_awal.php?data_awal_id=<?= $data['data_awal_id'] ?>"
            class="btn btn-success px-4">
            <i class="bi bi-printer me-1"></i> Cetak Data
          </a>
          <button class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Tutup</button>
        </div>

      </div>
    </div>
  </div>


<?php } ?>