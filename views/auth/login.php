<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="card shadow-sm border-0 auth-card">
            <div class="card-header bg-navy text-white py-3">
                <h5 class="mb-0">🔧 Masuk ke BengkelShop</h5>
            </div>
            <div class="card-body p-4">

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="index.php?page=login" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label text-muted">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= e($username) ?>" autocomplete="username" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label text-muted">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               autocomplete="current-password" required>
                    </div>
                    <button type="submit" class="btn btn-custom-primary w-100 py-2">Masuk</button>
                </form>

                <p class="text-center small mt-3 mb-0">
                    Belum punya akun?
                    <a href="index.php?page=register" class="text-decoration-none">Daftar di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
