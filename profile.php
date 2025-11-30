<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$user = current_user();
$users = get_users();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($name === '' || $email === '') {
        set_flash('danger', 'Nama dan email diperlukan.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('danger', 'Silakan masukkan alamat email yang valid.');
    } else {
        // Ensure unique email (except for current user)
        foreach ($users as $u) {
            if ((int)$u['id'] !== (int)$user['id'] && strtolower($u['email']) === strtolower($email)) {
                set_flash('danger', 'Akun lain sudah menggunakan email tersebut.');
                include __DIR__ . '/includes/layout-header.php';
                goto render_profile;
            }
        }

        foreach ($users as &$u) {
            if ((int)$u['id'] === (int)$user['id']) {
                $u['name'] = $name;
                $u['email'] = $email;
                if ($password !== '' || $password_confirm !== '') {
                    if ($password !== $password_confirm) {
                        set_flash('danger', 'Kata sandi tidak cocok.');
                        include __DIR__ . '/includes/layout-header.php';
                        goto render_profile;
                    }
                    $u['password'] = password_hash($password, PASSWORD_DEFAULT);
                }
                $u['updated_at'] = now_iso();
                $user = $u;
                break;
            }
        }
        unset($u);

        save_users($users);
        login_user($user); // refresh session data
        set_flash('success', 'Profil berhasil diperbarui.');
    }
}

include __DIR__ . '/includes/layout-header.php';

render_profile:
?>
<h1 class="h4 mb-3">Profil Anda</h1>
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Detail Akun</h2>
                <form method="post" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name'], ENT_QUOTES) ?>" required>
                        <div class="invalid-feedback">Silakan masukkan nama Anda.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>" required>
                        <div class="invalid-feedback">Silakan masukkan email yang valid.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Sandi Baru (opsional)</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="6">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Buku Favorit</h2>
                <?php
                $books = get_books();
                $booksById = [];
                foreach ($books as $b) {
                    $booksById[$b['id']] = $b;
                }
                $favorites = get_user_favorites((int)$user['id']);
                $favorites = array_slice($favorites, 0, 5);
                ?>
                <?php if (empty($favorites)): ?>
                    <p class="text-muted mb-0">Belum ada favorit.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($favorites as $fav):
                            $b = $booksById[$fav['book_id']] ?? null;
                            if (!$b) continue;
                            ?>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small"><?= htmlspecialchars($b['title'], ENT_QUOTES) ?></span>
                                <a href="book.php?id=<?= (int)$b['id'] ?>" class="btn btn-sm btn-outline-secondary">View</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/layout-footer.php'; ?>
