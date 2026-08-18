<div class="col d-flex align-items-strech">
    <div class="card w-100">
        <div class="card-body">
            <div
                class="d-flex align-items-center justify-content-between mb-1">
                <div class="">
                    <h5 class="card-title fw-semibold mb-5">
                        My Profile
                    </h5>
                </div>
            </div>
            <div>
                <div class="row">
                    <div class="col-lg-5 col-md-6 d-flex justify-content-center align-items-center">
                        <img
                            src="../assets/images/profile/user1.jpg"
                            alt=""
                            width="200"
                            height="200"
                            class="rounded-circle" />
                    </div>
                    <?php
                     /** @var mysqli $conn */ // 🔥 biar VS Code tidak merah
                    $id = $_SESSION['id'];
                    $query = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id'");
                    $data = mysqli_fetch_assoc($query);
                    ?>
                    <div class="col">
                        <h5>Informasi Akun</h5>
                        <hr>
                        <form class="row g-3" action="../function/fungsi_users.php?aksi=updateUser" method="post">
                            <input type="hidden" name="user_id" value="<?= $data['user_id']; ?>">
                            <div class="col-md-6">
                                <label for="inputEmail4" class="form-label">Username</label>
                                <input type="text" class="form-control" id="inputEmail4" name="username" value="<?php echo $data['username']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="inputPassword4" class="form-label">Role</label>
                                <input type="hidden" name="role" value="<?php echo $data['role']; ?>">
                                <input type="text" class="form-control" id="inputPassword4" value="<?php echo $data['role']; ?>" disabled>
                            </div>
                            <div class="col-12 mb-2">
                                <label for="inputAddress2" class="form-label">Email</label>
                                <input type="email" class="form-control" id="inputAddress2" name="email" value="<?php echo $data['email']; ?>">
                            </div>
                            <div class="col-12 mb-4">
                                <a href="#" class="link-primary" data-bs-toggle="modal" data-bs-target="#updatePassword"><iconify-icon icon="solar:pen-2-bold-duotone"></iconify-icon><span class="hide-menu">Update Password</span></a>
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
<!-- Modal -->
<div class="modal fade" id="updatePassword" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">UpdatePassword</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="../function/fungsi_users.php?aksi=updatePassword&user_id=<?= $data['user_id']; ?>" method="post">
                    <div class="col-12 mb-3">
                        <label for="passwordLama" class="form-label">Password Lama</label>
                        <input type="text" class="form-control" id="passwordLama" name="password_lama" placeholder="Masukkan Password Lama">
                    </div>
                    <div class="col-12">
                        <label for="passwordBaru" class="form-label">Password Baru</label>
                        <input type="text" class="form-control" id="passwordBaru" name="password_baru" placeholder="Masukkan Password Baru">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button></form>
            </div>
        </div>
    </div>
</div>