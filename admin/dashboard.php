<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$books = get_books();
$users = get_users();
$reviews = get_reviews();
$borrow = get_borrow_records();
$logs = get_logs();

$activeBorrow = array_filter($borrow, function ($row) {
    return empty($row['returned_at']);
});

// Latest 10 logs
usort($logs, function ($a, $b) {
    return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
});
$latestLogs = array_slice($logs, 0, 10);

include __DIR__ . '/../includes/layout-header.php';
?>
<h1 class="h4 mb-4">Dashboard Admin</h1>
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <div class="card-title h6">Total Buku</div>
                <div class="display-6"><?= count($books) ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card text-bg-success h-100">
            <div class="card-body">
                <div class="card-title h6">Total Pengguna</div>
                <div class="display-6"><?= count($users) ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card text-bg-warning h-100">
            <div class="card-body">
                <div class="card-title h6">Total Ulasan</div>
                <div class="display-6"><?= count($reviews) ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card text-bg-danger h-100">
            <div class="card-body">
                <div class="card-title h6">Peminjaman Aktif</div>
                <div class="display-6"><?= count($activeBorrow) ?></div>
            </div>
        </div>
    </div>
</div>

<h2 class="h5 mb-3">Manajemen Admin</h2>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="books.php" class="btn btn-outline-primary w-100">Kelola Buku</a>
    </div>
    <div class="col-6 col-md-3">
        <a href="categories.php" class="btn btn-outline-secondary w-100">Kelola Kategori</a>
    </div>
    <div class="col-6 col-md-3">
        <a href="users.php" class="btn btn-outline-success w-100">Lihat Pengguna</a>
    </div>
    <div class="col-6 col-md-3">
        <a href="reset-password.php" class="btn btn-outline-info w-100">Reset Password</a>
    </div>
    <div class="col-6 col-md-3">
        <a href="logs.php" class="btn btn-outline-warning w-100">Lihat Log</a>
    </div>
</div>

<h2 class="h5 mb-3">Aktivitas Admin Terkini</h2>
<div class="table-responsive mb-4">
    <table class="table table-striped table-sm align-middle">
        <thead>
        <tr>
            <th>Time</th>
            <th>Admin</th>
            <th>Action</th>
            <th>Entity</th>
            <th>Message</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($latestLogs)): ?>
            <tr><td colspan="5" class="text-muted">No logs yet.</td></tr>
        <?php else: ?>
            <?php foreach ($latestLogs as $log):
                $adminUser = $log['user_id'] ? find_user_by_id((int)$log['user_id']) : null;
                ?>
                <tr>
                    <td><?= format_date($log['created_at']) ?></td>
                    <td><?= htmlspecialchars($adminUser['email'] ?? '-', ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($log['action'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($log['entity_type'] . ' #' . (string)($log['entity_id'] ?? ''), ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($log['message'], ENT_QUOTES) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<h2 class="h5 mb-3">Borrow List</h2>
<div class="table-responsive">
    <table class="table table-striped table-sm align-middle">
        <thead>
        <tr>
            <th>User</th>
            <th>Book</th>
            <th>Borrowed At</th>
            <th>Returned At</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($borrow)): ?>
            <tr><td colspan="4" class="text-muted">No borrow records.</td></tr>
        <?php else: ?>
            <?php
            $booksById = [];
            foreach ($books as $b) {
                $booksById[$b['id']] = $b;
            }
            foreach ($borrow as $row):
                $u = find_user_by_id((int)$row['user_id']);
                $b = $booksById[$row['book_id']] ?? null;
                if (!$b) continue;
                ?>
                <tr>
                    <td><?= htmlspecialchars($u['email'] ?? 'User', ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($b['title'], ENT_QUOTES) ?></td>
                    <td><?= format_date($row['borrowed_at']) ?></td>
                    <td><?= $row['returned_at'] ? format_date($row['returned_at']) : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/layout-footer.php'; ?>