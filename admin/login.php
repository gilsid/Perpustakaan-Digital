<?php
require_once __DIR__ . '/../includes/bootstrap.php';

// Separate admin login: only users with is_admin = true are allowed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        set_flash('danger', 'Please provide both email and password.');
    } else {
        $user = find_user_by_email($email);
        if (!$user || empty($user['is_admin']) || !password_verify($password, $user['password'])) {
            set_flash('danger', 'Kredensial admin tidak valid.');
        } else {
            login_user($user);
            set_flash('success', 'Login admin berhasil.');
            redirect('dashboard.php');
        }
    }
}

include __DIR__ . '/../includes/layout-header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3 text-center">Masuk Admin</h1>
                <form method="post" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Please enter your email.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">Please enter your password.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/layout-footer.php'; ?>