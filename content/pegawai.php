<?php /** @var mysqli $conn */ // 🔥 biar VS Code tidak merah ?>
<div class="col d-flex align-items-stretch">
  <div class="card w-100">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-1">
        <div>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formPegawaiModal">
            <iconify-icon icon="solar:add-square-bold" height="25" style="vertical-align: -0.5em;"></iconify-icon> Tambah
          </button>
          <h5 class="card-title fw-semibold mt-3">Data Pegawai</h5>
        </div>
        <a href="../laporan/export_data_pegawai.php" target="_blank" class="btn btn-success">
          <iconify-icon icon="solar:printer-bold-duotone" height="25" style="vertical-align: -0.5em;"></iconify-icon>
          Cetak
        </a>
      </div>

      <!-- Modal Tambah Pegawai -->
      <div class="modal fade" id="formPegawaiModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <h5 class="modal-title text-white">Tambah Pegawai</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <form action="../function/fungsi_pegawai.php?aksi=simpan" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                  <div>
                    <h4>Data Diri</h4>
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

                <div class="row">
                  <div class="mb-3">
                    <label for="nip">NIP</label>
                    <input type="text" id="nip" class="form-control" name="nip" placeholder="Masukkan NIP" required>
                  </div>
                  <div class="mb-3">
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" class="form-control" name="nama" placeholder="Masukkan Nama" required>
                  </div>
                  <div class="mb-3">
                    <label for="jabatan">Jabatan</label>
                    <input type="text" id="jabatan" class="form-control" name="jabatan" placeholder="Masukkan Jabatan" required>
                  </div>
                  <div class="mb-3">
                    <label for="alamat">Alamat</label>
                    <textarea id="alamat" class="form-control" name="alamat" rows="3" placeholder="Masukkan Alamat Lengkap" required></textarea>
                  </div>
                  <div class="mb-3">
                    <label for="no_hp">No hp</label>
                    <input type="text" id="no_hp" class="form-control" name="no_hp" required>
                  </div>
                  <div class="mb-3">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-select" required>
                      <option value="aktif">Aktif</option>
                      <option value="nonaktif">Nonaktif</option>
                    </select>
                  </div>

                  <div>
                    <h4>Informasi Akun</h4>
                    <hr>
                  </div>
                  <div class="mb-3">
                    <label for="username">Username</label>
                    <input type="text" id="username" class="form-control" name="username" placeholder="Masukkan Username" required>
                  </div>
                  <div class="mb-3">
                    <label for="password">Password</label>
                    <input type="password" id="password" class="form-control" name="password" placeholder="Masukkan Password" required>
                  </div>
                  <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="selectinput" required>
                      <option value="admin">Admin</option>
                      <option value="asper">Asper</option>
                      <option value="pimpinan">Pimpinan</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="email">Email</label>
                    <input type="email" id="email" class="form-control" name="email" placeholder="Masukkan Email" required>
                  </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </div>
          </form>
        </div>
      </div>
      <!-- End Modal Tambah -->

      <!-- Tabel Data Pegawai -->
      <div class="table-responsive">
        <table id="example" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>No.</th>
              <th>Foto</th>
              <th>NIP</th>
              <th>Nama</th>
              <th>Jabatan</th>
              <th>No hp</th>
              <th>ID</th>
              <th>Status</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            $query = mysqli_query($conn, "SELECT p.*, u.username, u.role , u.email FROM pegawai p 
                                          LEFT JOIN users u ON u.user_id = p.user_id");
            while ($data = mysqli_fetch_array($query)) {
            ?>
              <tr>
                <td><?= $no++; ?></td>
                <td><img src="../image/<?= $data['foto']; ?>" alt="" width="100" style="object-fit: cover; aspect-ratio:1/1; border-radius:6px;"></td>
                <td><?= $data['nip']; ?></td>
                <td><?= $data['nama']; ?></td>
                <td><?= $data['jabatan']; ?></td>
                <td><?= $data['no_hp']; ?></td>
                <td><?= $data['pegawai_id']; ?></td>
                <td><?= ucfirst($data['status']); ?></td>
                <td class="text-center">
                  <div class="d-inline-flex gap-1">
                    <a href="#" class="btne btn-detail" data-bs-toggle="modal" data-bs-target="#detailPegawai<?= $data['pegawai_id'] ?>">
                      <iconify-icon icon="solar:eye-bold-duotone" height="20"></iconify-icon>
                    </a>
                      <?php if ($_SESSION['role'] == 'admin') { ?>
                    <a href="#" class="btne btn-edit fw-medium" data-bs-toggle="modal" data-bs-target="#editPegawai<?= $data['pegawai_id'] ?>">
                      <iconify-icon icon="solar:pen-2-bold-duotone" height="20"></iconify-icon>
                    </a>
                    <a href="../function/fungsi_pegawai.php?aksi=hapus&pegawai_id=<?= $data['pegawai_id']; ?>&hapus=<?= $data['foto']; ?>" class="btne btn-hapus fw-medium">
                      <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone" height="20"></iconify-icon>
                    </a>
                    <?php } ?>
                  </div>
                </td>
              </tr>

              <!-- Modal Detail Pegawai -->
              <div class="modal fade" id="detailPegawai<?= $data['pegawai_id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                  <div class="modal-content border-0 shadow-lg">

                    <!-- Header -->
                    <div class="modal-header bg-primary text-white">
                      <h5 class="modal-title fw-semibold text-white">
                        <i class="bi bi-person-badge me-2"></i>Detail Data Pegawai
                      </h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body bg-light">

                      <div class="container-fluid">

                        <!-- PROFILE HEADER -->
                        <div class="card border-0 shadow-sm mb-4">
                          <div class="card-body">
                            <div class="row align-items-center">
                              <div class="col-md-2 text-center">
                                <img src="../image/<?= $data['foto'] ?>"
                                  class="rounded-circle img-thumbnail shadow-sm"
                                  width="120" height="120" style="object-fit:cover;" alt="Foto Pegawai">
                              </div>
                              <div class="col-md-7">
                                <h4 class="fw-bold mb-1"><?= $data['nama'] ?></h4>
                                <div class="text-muted mb-2"><?= $data['jabatan'] ?></div>
                                <span class="badge bg-<?= $data['status'] == 'aktif' ? 'success' : 'secondary' ?> px-3 py-2">
                                  <?= ucfirst($data['status']) ?>
                                </span>
                              </div>
                              <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                <div class="text-muted small">NIP</div>
                                <div class="fw-semibold fs-5"><?= $data['nip'] ?></div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- DETAIL SECTIONS -->
                        <div class="row g-4">

                          <!-- Informasi Kontak -->
                          <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                              <div class="card-body">
                                <h6 class="text-uppercase text-muted mb-3">Informasi Kontak</h6>

                                <div class="mb-3">
                                  <small class="text-muted d-block">No. HP</small>
                                  <div class="fw-semibold"><?= $data['no_hp'] ?></div>
                                </div>

                                <div>
                                  <small class="text-muted d-block">Alamat</small>
                                  <div class="fw-semibold"><?= $data['alamat'] ?></div>
                                </div>

                              </div>
                            </div>
                          </div>

                          <!-- Informasi Akun -->
                          <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                              <div class="card-body">
                                <h6 class="text-uppercase text-muted mb-3">Informasi Akun</h6>

                                <div class="mb-3">
                                  <small class="text-muted d-block">Username</small>
                                  <div class="fw-semibold"><?= $data['username'] ?></div>
                                </div>
                                <div class="mb-3">
                                  <small class="text-muted d-block">Role</small>
                                  <div class="fw-semibold"><?= $data['role'] ?></div>
                                </div>
                                <div>
                                  <small class="text-muted d-block">Email</small>
                                  <div class="fw-semibold"><?= $data['email'] ?></div>
                                </div>

                              </div>
                            </div>
                          </div>

                        </div>

                      </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer bg-white">
                      <a target="_blank"
                        href="../laporan/export_pegawai.php?pegawai_id=<?= $data['pegawai_id'] ?>"
                        class="btn btn-success px-4">
                        <i class="bi bi-printer me-1"></i> Cetak Data
                      </a>
                      <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        Tutup
                      </button>
                    </div>

                  </div>
                </div>
              </div>

              <!-- Modal Edit -->
              <div class="modal fade" id="editPegawai<?= $data['pegawai_id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header bg-warning">
                      <h5 class="modal-title">Edit pegawai</h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <form action="../function/fungsi_pegawai.php?aksi=edit" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="pegawai_id" value="<?= $data['pegawai_id'] ?>">
                        <input type="hidden" name="fotolama" value="<?= $data['foto'] ?>">

                        <div class="row mb-3">
                          <div>
                            <h4>Data Diri</h4>
                            <hr>
                          </div>
                          <div class="col-4 d-flex justify-content-center">
                            <img id="preview" src="../image/<?= $data['foto']; ?>" alt="Preview Foto" width="150">
                          </div>
                          <div class="col align-self-center">
                            <input type="file" class="form-control" name="foto" accept="image/*" oninput="preview.src = window.URL.createObjectURL(this.files[0])">
                            <div class="form-text">Maksimal ukuran file 2MB. Format: JPG, JPEG, PNG</div>
                          </div>
                        </div>

                        <div class="mb-3">
                          <label for="nip">NIP</label>
                          <input type="text" class="form-control" name="nip" value="<?= $data['nip'] ?>" required>
                        </div>
                        <div class="mb-3">
                          <label for="nama">Nama</label>
                          <input type="text" class="form-control" name="nama" value="<?= $data['nama'] ?>" required>
                        </div>
                        <div class="mb-3">
                          <label for="jabatan">Jabatan</label>
                          <input type="text" class="form-control" name="jabatan" value="<?= $data['jabatan'] ?>" required>
                        </div>
                        <div class="mb-3">
                          <label for="alamat">Alamat</label>
                          <textarea class="form-control" name="alamat" rows="3" required><?= $data['alamat'] ?></textarea>
                        </div>
                        <div class="mb-3">
                          <label for="no_hp">No hp</label>
                          <input type="text" class="form-control" name="no_hp" value="<?= $data['no_hp'] ?>" required>
                        </div>
                        <div class="mb-3">
                          <label for="status">Status</label>
                          <select name="status" class="form-select" required>
                            <option value="aktif" <?= $data['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= $data['status'] == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
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