<? php/** @var mysqli $conn */

    /** @var array $data */ ?>
<div class="col d-flex align-items-strech">
    <div class="card w-100">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div class="">
                    <h5 class="card-title fw-semibold mb-5">
                        My Profile
                    </h5>
                </div>
            </div>
            <div>
                <div class="row">
                    <!-- FOTO PROFIL -->
                    <div class="col-lg-5 col-md-6 d-flex justify-content-center align-items-center mb-3">
                        <img src="../image/<?= $profil['foto']; ?>" alt="" width="200" height="200"
                            class="rounded-circle" style="object-fit: cover;" />
                    </div>

                    <!-- FORM PROFIL -->
                    <div class="col">
                        <h5>Informasi Akun</h5>
                        <hr>
                        <form class="row g-3" action="../function/fungsi_mandor.php?aksi=updatemandor" method="post">
                            <input type="hidden" name="mandor_id" value="<?= $profil['mandor_id']; ?>">
                            <input type="hidden" name="user_id" value="<?= $profil['user_id']; ?>">

                            <div class="col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?= $profil['username']; ?>">
                            </div>

                            <div class="col-md-6">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" class="form-control" id="role" value="<?= $profil['role']; ?>"
                                    disabled>
                            </div>

                            <div class="col-12">
                                <label for="no_hp" class="form-label">No. HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp"
                                    value="<?= $profil['no_hp']; ?>">
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= $profil['email']; ?>">
                            </div>
                            <div class="col-12">
                                <label for="alamat" class="form-label">Alamat</label>
                                <input type="alamat" class="form-control" id="alamat" name="alamat"
                                    value="<?= $profil['alamat']; ?>">
                            </div>

                            <div class="col-12 mb-4">
                                <a href="#" class="link-primary" data-bs-toggle="modal"
                                    data-bs-target="#updatePassword">
                                    <iconify-icon icon="solar:pen-2-bold-duotone"></iconify-icon>
                                    <span class="hide-menu">Update Password</span>
                                </a>
                            </div>

                            <div class="col-12">
                                <a href="index.php" class="btn btn-danger">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card w-100">
    <div class="card-body">
        <div class="align-items-center justify-content-between mb-1">
            <div class="row">
                <!-- DATA DIRI mandor -->
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="card-title fw-semibold mb-0">Data Diri</h5>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="../laporan/export_mandor.php?mandor_id=<?= $profil['mandor_id'] ?>" target="_blank"
                                class="btn btn-success">
                                <iconify-icon icon="solar:printer-bold-duotone" style="vertical-align: -0.5em;"
                                    height="25"></iconify-icon>
                                Cetak
                            </a>

                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editprofeilmandor<?= $profil['mandor_id'] ?? '' ?>">
                                <i class="bi bi-pencil-square me-1"></i>
                                Edit Profil
                            </button>
                        </div>
                    </div>

                    <hr>
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr>
                                <th scope="row">ID mandor</th>
                                <td><?= $profil['mandor_id']; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Nama</th>
                                <td><?= $profil['nama']; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">NIP</th>
                                <td><?= $profil['nip']; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Alamat</th>
                                <td><?= $profil['alamat']; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">No. HP</th>
                                <td><?= $profil['no_hp']; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">BKPH</th>
                                <td><?= $profil['bkph']; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Status</th>
                                <td><?= ucfirst($profil['status']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




<?php

$mandor_id = $profil['mandor_id'] ?? '';

$sql = "
    SELECT
        m.*,
        u.username,
        u.email
    FROM mandor m
    LEFT JOIN users u ON m.user_id = u.user_id
    WHERE m.mandor_id = '$mandor_id'
";
$queryModal = mysqli_query($conn, $sql);

while ($data = mysqli_fetch_assoc($queryModal)) {
    ?>
    <div class="modal fade" id="editprofeilmandor<?= $data['mandor_id']; ?>" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Edit Mandor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="../function/fungsi_mandor.php?aksi=editprofil" method="POST" enctype="multipart/form-data">
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

                        <input type="hidden" name="mandor_id" value="<?= $data['mandor_id']; ?>">
                        <input type="hidden" name="fotolama" value="<?= $data['foto']; ?>">

                        <!-- isi form -->
                        <div class="row mb-3">
                            <div>
                                <h4>Data Diri</h4>
                                <hr>
                            </div>
                            <div class="col-4 d-flex justify-content-center">
                                <img id="preview" src="../image/<?= $data['foto']; ?>" alt="Preview Foto" width="150">
                            </div>
                            <div class="col align-self-center">
                                <input type="file" class="form-control" name="foto" accept="image/*"
                                    oninput="preview.src = window.URL.createObjectURL(this.files[0])">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>NIP</label>
                            <input type="text" class="form-control" name="nip" value="<?= $data['nip']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" class="form-control" name="nama" value="<?= $data['nama']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="alamat">Alamat</label>
                            <textarea class="form-control" name="alamat" rows="3"
                                required><?= $data['alamat']; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="no_hp">No. HP</label>
                            <input type="text" class="form-control" name="no_hp" value="<?= $data['no_hp']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="bkph">BKPH</label>
                            <input type="text" class="form-control" name="bkph" value="<?= $data['bkph']; ?>" required>
                        </div>
                        <!-- field lainnya -->

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


<!-- MODAL UPDATE PASSWORD -->
<div class="modal fade" id="updatePassword" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">UpdatePassword</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="../function/fungsi_users.php?aksi=updatePassword&user_id=<?= $profil['user_id']; ?>"
                    method="post">
                    <div class="col-12 mb-3">
                        <label for="passwordLama" class="form-label">Password Lama</label>
                        <input type="text" class="form-control" id="passwordLama" name="password_lama"
                            placeholder="Masukkan Password Lama">
                    </div>
                    <div class="col-12">
                        <label for="passwordBaru" class="form-label">Password Baru</label>
                        <input type="text" class="form-control" id="passwordBaru" name="password_baru"
                            placeholder="Masukkan Password Baru">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button></form>
            </div>
        </div>
    </div>
</div>