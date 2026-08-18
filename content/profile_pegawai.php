<div class="col d-flex align-items-stretch">
    <div class="card w-100">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div class="">
                    <h5 class="card-title fw-semibold mb-5">My Profile</h5>
                </div>
            </div>
            <div>
                <div class="row">
                    <!-- FOTO PROFIL -->
                    <div class="col-lg-5 col-md-6 d-flex justify-content-center align-items-center mb-3">
                        <img src="../image/<?= $data['foto']; ?>" alt="Foto Pegawai" width="200" height="200"
                            class="rounded-circle" style="object-fit: cover;" />
                    </div>

                    <!-- FORM PROFIL -->
                    <div class="col">
                        <h5>Informasi Akun</h5>
                        <hr>
                        <form class="row g-3" action="../function/fungsi_pegawai.php?aksi=updatepegawai" method="post">
                            <input type="hidden" name="pegawai_id" value="<?= $data['pegawai_id']; ?>">
                            <input type="hidden" name="user_id" value="<?= $data['user_id']; ?>">

                            <div class="col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?= $data['username']; ?>">
                            </div>

                            <div class="col-md-6">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" class="form-control" id="role" value="<?= $data['role']; ?>"
                                    disabled>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= $data['email']; ?>">
                            </div>

                            <div class="col-12">
                                <label for="alamat" class="form-label">alamat</label>
                                <input type="alamat" class="form-control" id="alamat" name="alamat"
                                    value="<?= $data['alamat']; ?>">
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

                <!-- DATA DIRI PETUGAS -->
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="card-title fw-semibold mb-0">Data Diri</h5>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="../laporan/export_pegawai.php?pegawai_id=<?= $data['pegawai_id'] ?>"
                                target="_blank" class="btn btn-success">
                                <iconify-icon icon="solar:printer-bold-duotone" height="20"
                                    style="vertical-align: middle;"></iconify-icon>
                                <span class="ms-1">Cetak</span>
                            </a>

                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editprofeil<?= $data['pegawai_id'] ?? '' ?>">
                                <i class="bi bi-pencil-square me-1"></i>
                                Edit Profil
                            </button>
                        </div>
                    </div>

                    <hr>
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr>
                                <th scope="row">ID Pegawai</th>
                                <td><?= $data['pegawai_id']; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Nama</th>
                                <td><?= $data['nama']; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">NIP</th>
                                <td><?= $data['nip']; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Jabatan</th>
                                <td><?= $data['jabatan']; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Alamat</th>
                                <td><?= $data['alamat']; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">No hp</th>
                                <td><?= $data['no_hp']; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Status</th>
                                <td><?= ucfirst($data['status']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


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
                        <form action="../function/fungsi_users.php?aksi=updatePassword&user_id=<?= $data['user_id']; ?>"
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
    </div>


    <?php

    $pegawai = $data['pegawai_id'] ?? '';

    $sql = "
    SELECT
        m.*,
        u.username,
        u.email
    FROM pegawai m
    LEFT JOIN users u ON m.user_id = u.user_id
    WHERE m.pegawai_id = '$pegawai'
";
    $queryModal = mysqli_query($conn, $sql);

    while ($data = mysqli_fetch_assoc($queryModal)) {
        ?>
        <div class="modal fade" id="editprofeil<?= $data['pegawai_id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Edit Pegawai</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="../function/fungsi_pegawai.php?aksi=editprofil" method="POST"
                        enctype="multipart/form-data">
                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

                            <input type="hidden" name="pegawai_id" value="<?= $data['pegawai_id']; ?>">
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
                                <input type="text" class="form-control" name="no_hp" value="<?= $data['no_hp']; ?>"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="jabatan">Jabatan</label>
                                <input type="text" class="form-control" name="jabatan" value="<?= $data['jabatan'] ?>"
                                    required>
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