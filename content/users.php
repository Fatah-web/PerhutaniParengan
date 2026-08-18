<?php error_reporting(0); 
/** @var mysqli $conn */ // 🔥 biar VS Code tidak merah
?>

<div class="col d-flex align-items-stretch">
  <div class="card w-100">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-1">
        <div>
          <!-- <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#formUserModal"><iconify-icon icon="solar:add-square-bold" height="25" style="vertical-align: -0.5em;"></iconify-icon> Tambah</button> -->
          <h5 class="card-title fw-semibold">Data User</h5>

          <!-- Modal Tambah User --
          <div class="modal fade" id="formUserModal" tabindex="-1" aria-labelledby="formUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Tambah Admin</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <form action="../function/fungsi_users.php?aksi=simpan" method="POST">
                    <div class="mb-3">
                      <label for="username">Username</label>
                      <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>
                    <div class="mb-3">
                      <label for="password">Password</label>
                      <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="mb-3">
                      <label for="email">Email</label>
                      <input type="email" name="email" class="form-control" placeholder="Email">
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
        </div>
      </div>

      <!-- Table Data User -->
      <div class="table-responsive">
        <table id="example" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>No</th>
              <th>Username</th>
              <th>Role</th>
              <th>Email</th>
              <th>Dibuat Pada</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            $query = mysqli_query($conn, "SELECT * FROM users");
            while ($data = mysqli_fetch_array($query)) {
            ?>
              <tr>
                <td><?= $no++; ?></td>
                <td><?= $data['username']; ?></td>
                <td><?= ucfirst($data['role']); ?></td>
                <td><?= $data['email']; ?></td>
                <td><?= $data['created_at']; ?></td>
                <td class="text-center">
                  <div class="d-inline-flex gap-1">
                    <a href="#" class="btne btn-edit" data-bs-toggle="modal" data-bs-target="#editUser<?= $data['user_id']; ?>">
                      <iconify-icon icon="solar:pen-2-bold-duotone" height="20"></iconify-icon>
                    </a>
                    <a href="../function/fungsi_users.php?aksi=hapus&user_id=<?= $data['user_id']; ?>" class="btne btn-hapus">
                      <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone" height="20"></iconify-icon>
                    </a>
                  </div>
                </td>
              </tr>

              <!-- Modal Edit User -->
              <div class="modal fade" id="editUser<?= $data['user_id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header bg-warning">
                      <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <form action="../function/fungsi_users.php?aksi=edit" method="POST">
                        <input type="hidden" name="user_id" value="<?= $data['user_id']; ?>">
                        <div class="mb-3">
                          <label for="username">Username</label>
                          <input type="text" name="username" class="form-control" value="<?= $data['username']; ?>" required>
                        </div>
                        <div class="mb-3">
                          <label for="password">Password</label>
                          <input type="password" name="password" class="form-control" placeholder="Password Baru (biarkan kosong jika tidak diubah)">
                        </div>
                        <div class="mb-3">
                          <label for="role">Role</label>

                          <?php if ($data['role'] == 'mandor'): ?>
                            <!-- Mandor: role dikunci -->
                            <select name="role" class="form-select" disabled>
                              <option value="mandor" selected>Mandor</option>
                            </select>
                            <input type="hidden" name="role" value="mandor">
                          <?php else: ?>
                            <select name="role" class="form-select" disabled>
                              <option value="admin" selected>Admin</option>
                            </select>
                            <input type="hidden" name="role" value="admin">
                            <!-- Selain mandor 
                            <select name="role" class="form-select" required>
                              <option value="admin" <?= ($data['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                              <option value="asper" <?= ($data['role'] == 'asper') ? 'selected' : ''; ?>>Asper</option>
                              <option value="pimpinan" <?= ($data['role'] == 'pimpinan') ? 'selected' : ''; ?>>Pimpinan</option>
                               mandor sengaja DIHILANGKAN 
                            </select>-->

                          <?php endif; ?>
                        </div>
                        <div class="mb-3">
                          <label for="email">Email</label>
                          <input type="email" name="email" class="form-control" value="<?= $data['email']; ?>">
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                          <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </div>
                    </form>
                  </div>
                </div>
                <!-- End Modal Edit -->
              <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>