<div class="row g-4">

    <!-- ================= PROFILE CARD ================= -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">

            <!-- HEADER -->
            <div class="bg-primary bg-gradient text-white text-center p-4">
                <div class="position-relative d-inline-block mb-3">

                    <img src="../image/<?= $profil['foto']; ?>" alt="Foto Profil"
                        class="rounded-circle border border-4 border-white shadow" width="180" height="180"
                        style="object-fit: cover;">

                    <?php
                    $status = strtolower($profil['status'] ?? '');

                    if ($status == 'aktif') {
                        $statusColor = 'bg-success';

                    } elseif ($status == 'nonaktif') {
                        $statusColor = 'bg-danger';

                    } else {
                        $statusColor = 'bg-warning';
                    }
                    ?>

                    <span
                        class="position-absolute bottom-0 end-0 p-2 <?= $statusColor; ?> border border-3 border-white rounded-circle">
                    </span>
                </div>

                <h4 class="fw-bold mb-1">
                    <?= $profil['nama'] ?? '-'; ?>
                </h4>

                <p class="mb-0 opacity-75">
                    <?= ucfirst($profil['role'] ?? '-'); ?>
                </p>
            </div>

            <!-- BODY -->
            <div class="card-body p-4">

                <!-- ID -->
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-dark bg-opacity-10 text-dark rounded-3 p-2 me-3">
                        <iconify-icon icon="solar:hashtag-square-bold-duotone" width="22"></iconify-icon>
                    </div>

                    <div>
                        <small class="text-muted d-block">ID Mandor</small>
                        <span class="fw-semibold">
                            <?= $profil['mandor_id']; ?>
                        </span>
                    </div>
                </div>

                <!-- NIP -->
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 me-3">
                        <iconify-icon icon="solar:card-bold-duotone" width="22"></iconify-icon>
                    </div>

                    <div>
                        <small class="text-muted d-block">NIP</small>
                        <span class="fw-semibold">
                            <?= $profil['nip'] ?? '-'; ?>
                        </span>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                        <iconify-icon icon="solar:user-bold-duotone" width="22"></iconify-icon>
                    </div>

                    <div>
                        <small class="text-muted d-block">Username</small>
                        <span class="fw-semibold">
                            <?= $profil['username']; ?>
                        </span>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 me-3">
                        <iconify-icon icon="solar:letter-bold-duotone" width="22"></iconify-icon>
                    </div>

                    <div>
                        <small class="text-muted d-block">Email</small>
                        <span class="fw-semibold">
                            <?= $profil['email']; ?>
                        </span>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 me-3">
                        <iconify-icon icon="solar:phone-bold-duotone" width="22"></iconify-icon>
                    </div>

                    <div>
                        <small class="text-muted d-block">No. HP</small>
                        <span class="fw-semibold">
                            <?= $profil['no_hp'] ?? '-'; ?>
                        </span>
                    </div>
                </div>

                <div class="d-flex align-items-start">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 me-3">
                        <iconify-icon icon="solar:map-point-bold-duotone" width="22"></iconify-icon>
                    </div>

                    <div>
                        <small class="text-muted d-block">Alamat</small>
                        <span class="fw-semibold">
                            <?= $profil['alamat'] ?? '-'; ?>
                        </span>
                    </div>
                </div>

                <hr class="my-4">

                <div class="text-center">

                    <?php
                    $status = strtolower($profil['status'] ?? '');

                    if ($status == 'aktif') {
                        $badgeClass = 'bg-success-subtle text-success';
                        $icon = 'solar:check-circle-bold-duotone';

                    } elseif ($status == 'nonaktif') {
                        $badgeClass = 'bg-danger-subtle text-danger';
                        $icon = 'solar:close-circle-bold-duotone';

                    } else {
                        $badgeClass = 'bg-warning-subtle text-warning';
                        $icon = 'solar:danger-triangle-bold-duotone';
                    }
                    ?>

                    <span class="badge <?= $badgeClass; ?> px-4 py-2 rounded-pill fw-semibold">

                        <iconify-icon icon="<?= $icon; ?>" class="me-1">
                        </iconify-icon>

                        <?= ucfirst($profil['status'] ?? 'Belum Diatur'); ?>

                    </span>

                </div>

            </div>
        </div>
    </div>

    <!-- ================= FORM PROFILE ================= -->
    <div class="col-lg-8 align-self-start">

        <!-- CARD INFORMASI AKUN -->
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body p-4">

                <!-- HEADER -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">
                            Informasi Akun
                        </h4>

                        <p class="text-muted mb-0">
                            Kelola informasi akun dan data profil Anda
                        </p>
                    </div>

                    <a href="../laporan/export_mandor.php?mandor_id=<?= $profil['mandor_id'] ?>" target="_blank"
                        class="btn btn-success rounded-pill px-4">

                        <iconify-icon icon="solar:printer-bold-duotone" class="me-1" height="25" style="vertical-align: -0.5em;"></iconify-icon>

                        Cetak
                    </a>
                </div>

                <!-- FORM -->
                <form class="row g-4" action="../function/fungsi_mandor.php?aksi=updatemandor" method="post">

                    <input type="hidden" name="mandor_id" value="<?= $profil['mandor_id']; ?>">

                    <input type="hidden" name="user_id" value="<?= $profil['user_id']; ?>">

                    <!-- USERNAME -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Username
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <iconify-icon icon="solar:user-bold-duotone"></iconify-icon>
                            </span>

                            <input type="text" class="form-control" name="username" value="<?= $profil['username']; ?>">
                        </div>
                    </div>

                    <!-- ROLE -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Role
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <iconify-icon icon="solar:shield-user-bold-duotone"></iconify-icon>
                            </span>

                            <input type="text" class="form-control bg-light" value="<?= ucfirst($profil['role']); ?>"
                                disabled>
                        </div>
                    </div>

                    <!-- NO HP -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            No. HP
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <iconify-icon icon="solar:phone-bold-duotone"></iconify-icon>
                            </span>

                            <input type="text" class="form-control" name="no_hp" value="<?= $profil['no_hp']; ?>">
                        </div>
                    </div>

                    <!-- EMAIL -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Email
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <iconify-icon icon="solar:letter-bold-duotone"></iconify-icon>
                            </span>

                            <input type="email" class="form-control" name="email" value="<?= $profil['email']; ?>">
                        </div>
                    </div>

                    <!-- ALAMAT -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Alamat
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <iconify-icon icon="solar:map-point-bold-duotone"></iconify-icon>
                            </span>

                            <textarea class="form-control" rows="3" name="alamat"><?= $profil['alamat']; ?></textarea>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 flex-wrap">

                            <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#updatePassword">
                                <iconify-icon icon="solar:lock-password-bold-duotone" class="me-1"></iconify-icon>
                                Update Password
                            </a>

                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editprofeilmandor<?= $_SESSION['mandor_id'] ?? '' ?>">
                                <i class="bi bi-pencil-square me-1"></i>
                                Edit Profil
                            </button>

                             <button type="submit" class="btn btn-primary px-4">

                                <iconify-icon icon="solar:diskette-bold-duotone" class="me-1"></iconify-icon>

                                Simpan Perubahan
                            </button>

                        </div>
                    </div>
                </form>

            </div>
        </div>

        <!-- ================= CARD LOGO PERUSAHAAN ================= -->
        <div class="card border-0 shadow-sm overflow-hidden">

            <!-- HEADER -->
            <div class="bg-success bg-gradient text-white p-3 text-center">
                <h5 class="fw-bold mb-0 text-white">
                    Sistem Informasi Monitoring Tanaman KPH Parengan
                </h5>
            </div>

            <!-- BODY -->
            <div class="card-body text-center p-5">

                <!-- LOGO -->
                <img src="../assets/images/logos/logo.png" alt="Logo Perhutani" class="img-fluid mb-4"
                    style="max-height: 120px;">
                <!-- INFO -->
                <div class="row text-center g-3">

                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 h-100">
                            <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="35"
                                class="text-primary mb-2"></iconify-icon>

                            <h6 class="fw-bold mb-1">
                                Team Work
                            </h6>

                            <small class="text-muted">
                                Kolaborasi data petugas lapangan
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 h-100">
                            <iconify-icon icon="solar:shield-check-bold-duotone" width="35"
                                class="text-success mb-2"></iconify-icon>

                            <h6 class="fw-bold mb-1">
                                Keamanan
                            </h6>

                            <small class="text-muted">
                                Sistem aman dan terintegrasi
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 h-100">
                            <iconify-icon icon="solar:chart-2-bold-duotone" width="35"
                                class="text-warning mb-2"></iconify-icon>

                            <h6 class="fw-bold mb-1">
                                Monitoring
                            </h6>

                            <small class="text-muted">
                                Monitoring progres tanaman realtime
                            </small>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

    <!-- ================= MODAL UPDATE PASSWORD ================= -->
    <div class="modal fade" id="updatePassword" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                <!-- HEADER -->
                <div class="modal-header border-0 bg-primary bg-gradient text-white p-4">

                    <div>
                        <h5 class="modal-title fw-bold mb-1">
                            <iconify-icon icon="solar:lock-password-bold-duotone" class="me-1"></iconify-icon>

                            Update Password
                        </h5>

                        <small class="opacity-75">
                            Gunakan password yang aman dan mudah diingat
                        </small>
                    </div>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body p-4">

                    <form action="../function/fungsi_users.php?aksi=updatePassword&user_id=<?= $profil['user_id']; ?>"
                        method="post">

                        <!-- PASSWORD LAMA -->
                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Password Lama
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-light">
                                    <iconify-icon icon="solar:key-bold-duotone"></iconify-icon>
                                </span>

                                <input type="password" class="form-control" id="passwordLama" name="password_lama"
                                    placeholder="Masukkan password lama" required>

                                <button class="btn btn-light border" type="button"
                                    onclick="togglePassword('passwordLama', this)">

                                    <iconify-icon icon="solar:eye-bold"></iconify-icon>
                                </button>
                            </div>
                        </div>

                        <!-- PASSWORD BARU -->
                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Password Baru
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-light">
                                    <iconify-icon icon="solar:lock-password-bold-duotone"></iconify-icon>
                                </span>

                                <input type="password" class="form-control" id="passwordBaru" name="password_baru"
                                    placeholder="Masukkan password baru" required>

                                <button class="btn btn-light border" type="button"
                                    onclick="togglePassword('passwordBaru', this)">

                                    <iconify-icon icon="solar:eye-bold"></iconify-icon>
                                </button>
                            </div>

                            <small class="text-muted">
                                Minimal 8 karakter kombinasi huruf dan angka
                            </small>
                        </div>

                        <!-- FOOTER -->
                        <div class="d-flex justify-content-end gap-2 mt-4">

                            <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">

                                Batal
                            </button>

                            <button type="submit" class="btn btn-primary px-4">

                                <iconify-icon icon="solar:diskette-bold-duotone" class="me-1"></iconify-icon>

                                Simpan Password
                            </button>

                        </div>

                    </form>

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
                                <input type="text" class="form-control" name="no_hp" value="<?= $data['no_hp']; ?>"
                                    required>
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

    <!-- SHOW / HIDE PASSWORD -->
    <script>
        function togglePassword(id, btn) {

            const input = document.getElementById(id);
            const icon = btn.querySelector('iconify-icon');

            if (input.type === "password") {

                input.type = "text";

                icon.setAttribute(
                    "icon",
                    "solar:eye-closed-bold"
                );

            } else {

                input.type = "password";

                icon.setAttribute(
                    "icon",
                    "solar:eye-bold"
                );
            }
        }
    </script>