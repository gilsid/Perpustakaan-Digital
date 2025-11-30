<?php
require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        set_flash('danger', 'Silakan masukkan email dan kata sandi.');
    } else {
        $user = find_user_by_email($email);
        if (!$user || !password_verify($password, $user['password'])) {
            set_flash('danger', 'Email atau kata sandi tidak valid.');
        } else {
            login_user($user);
            set_flash('success', 'Selamat datang kembali, ' . $user['name'] . '!');
            redirect('index.php');
        }
    }
}

include __DIR__ . '/includes/layout-header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3 text-center">Masuk</h1>
                <form method="post" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Silakan masukkan email Anda.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">Silakan masukkan kata sandi Anda.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Masuk</button>
                </form>
                <p class="mt-3 mb-0 text-center small">Belum punya akun? <a href="register.php">Daftar</a></p>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/layout-footer.php'; ?>