<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$users = get_users();
$message = null;
$messageType = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int)($_POST['user_id'] ?? 0);
    $newPassword = trim($_POST['new_password'] ?? '');
    $newPasswordConfirm = trim($_POST['new_password_confirm'] ?? '');

    if (!$userId) {
        $messageType = 'danger';
        $message = 'Silakan pilih pengguna.';
    } elseif ($newPassword === '' || $newPasswordConfirm === '') {
        $messageType = 'danger';
        $message = 'Kata sandi tidak boleh kosong.';
    } elseif ($newPassword !== $newPasswordConfirm) {
        $messageType = 'danger';
        $message = 'Kata sandi tidak cocok.';
    } else {
        $found = false;
        foreach ($users as &$user) {
            if ((int)$user['id'] === $userId) {
                $user['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                $user['updated_at'] = now_iso();
                $found = true;
                set_flash('success', 'Kata sandi pengguna berhasil direset ke: ' . $newPassword);
                break;
            }
        }
        unset($user);

        if ($found) {
            save_users($users);
            $admin = current_user();
            add_log((int)$admin['id'], 'reset_password', 'user', $userId, 'Reset password pengguna');
            redirect('reset-password.php');
        } else {
            $messageType = 'danger';
            $message = 'Pengguna tidak ditemukan.';
        }
    }
}

include __DIR__ . '/../includes/layout-header.php';
?>

<h1 class="h4 mb-3">Reset Kata Sandi Pengguna</h1>

<?php if ($message): ?>
    <div class="alert alert-<?= htmlspecialchars($messageType, ENT_QUOTES) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message, ENT_QUOTES) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h2 class="h5 mb-3">Reset Kata Sandi</h2>
                <form method="post" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Pilih Pengguna</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">-- Pilih pengguna --</option>
                            <?php foreach ($users as $u): ?>
                                <?php if (!(bool)($u['is_admin'] ?? false)): ?>
                                    <option value="<?= (int)$u['id'] ?>">
                                        <?= htmlspecialchars($u['name'], ENT_QUOTES) ?> (<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Silakan pilih pengguna.</div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Kata Sandi Baru</label>
                        <input type="text" class="form-control" id="new_password" name="new_password" required>
                        <small class="text-muted">Gunakan kata sandi yang mudah diingat pengguna atau buat secara acak.</small>
                        <div class="invalid-feedback">Kata sandi tidak boleh kosong.</div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirm" class="form-label">Konfirmasi Kata Sandi</label>
                        <input type="text" class="form-control" id="new_password_confirm" name="new_password_confirm" required>
                        <div class="invalid-feedback">Kata sandi tidak cocok.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Reset Kata Sandi</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h2 class="h5 mb-3">Daftar Pengguna</h2>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <?php if (!(bool)($u['is_admin'] ?? false)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($u['name'], ENT_QUOTES) ?></td>
                                        <td><?= htmlspecialchars($u['email'], ENT_QUOTES) ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/layout-footer.php'; ?>
