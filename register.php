<?php
require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $password_confirm === '') {
        set_flash('danger', 'Semua field diperlukan.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('danger', 'Silakan masukkan alamat email yang valid.');
    } elseif ($password !== $password_confirm) {
        set_flash('danger', 'Kata sandi tidak cocok.');
    } elseif (find_user_by_email($email)) {
        set_flash('danger', 'Pengguna dengan email tersebut sudah terdaftar.');
    } else {
        $users = get_users();
        $newUser = [
            'id'        => next_id($users),
            'name'      => $name,
            'email'     => $email,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'is_admin'  => false,
            'created_at'=> now_iso(),
            'updated_at'=> now_iso(),
        ];
        $users[] = $newUser;
        if (save_users($users)) {
            login_user($newUser);
            set_flash('success', 'Akun dibuat. Selamat datang, ' . $name . '!');
            redirect('index.php');
        } else {
            set_flash('danger', 'Gagal membuat akun. Silakan coba lagi.');
        }
    }
}

include __DIR__ . '/includes/layout-header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3 text-center">Daftar</h1>
                <form method="post" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback">Silakan masukkan nama Anda.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Silakan masukkan email yang valid.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="6" required>
                        <div class="invalid-feedback">Silakan masukkan kata sandi (minimal 6 karakter).</div>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Konfirmasi Kata Sandi</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" minlength="6" required>
                        <div class="invalid-feedback">Silakan konfirmasi kata sandi Anda.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Buat Akun</button>
                </form>
                <p class="mt-3 mb-0 text-center small">Sudah punya akun? <a href="login.php">Masuk</a></p>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/layout-footer.php'; ?>