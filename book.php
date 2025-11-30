<?php
require_once __DIR__ . '/includes/bootstrap.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$book = $id ? get_book($id) : null;

if (!$book) {
    set_flash('danger', 'Buku tidak ditemukan.');
    redirect('index.php');
}

$user = current_user();

// Handle favorite, borrow, return, and review actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$user) {
        set_flash('warning', 'Silakan masuk untuk melakukan tindakan ini.');
        redirect('login.php');
    }

    // Admin users cannot perform these actions
    if (!empty($user['is_admin'])) {
        set_flash('warning', 'Admin tidak dapat melakukan tindakan ini.');
        redirect('book.php?id=' . $id);
    }

    $userId = (int)$user['id'];

    if (isset($_POST['action']) && $_POST['action'] === 'favorite') {
        if (is_favorite($userId, $id)) {
            remove_favorite($userId, $id);
            set_flash('success', 'Removed from favorites.');
        } else {
            add_favorite($userId, $id);
            set_flash('success', 'Added to favorites.');
        }
        redirect('book.php?id=' . $id);
    }

    if (isset($_POST['action']) && $_POST['action'] === 'borrow') {
        borrow_book($userId, $id);
        set_flash('success', 'Book borrowed successfully.');
        redirect('book.php?id=' . $id);
    }

    if (isset($_POST['action']) && $_POST['action'] === 'return') {
        return_book($userId, $id);
        set_flash('success', 'Book returned successfully.');
        redirect('book.php?id=' . $id);
    }

    if (isset($_POST['action']) && $_POST['action'] === 'review') {
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        if ($rating < 1 || $rating > 5) {
            set_flash('danger', 'Please select a rating between 1 and 5.');
        } else {
            add_review($userId, $id, $rating, $comment);
            set_flash('success', 'Thank you for your review!');
        }
        redirect('book.php?id=' . $id);
    }
}

$isFavorite = $user ? is_favorite((int)$user['id'], $id) : false;
$activeBorrow = $user ? get_active_borrow((int)$user['id'], $id) : null;
$reviews = get_book_reviews($id);
$recommended = get_recommended_books($book, 4);

include __DIR__ . '/includes/layout-header.php';
?>
<div class="row">
    <div class="col-md-4 mb-3">
        <?php if (!empty($book['cover'])): ?>
            <img src="uploads/images/<?= htmlspecialchars($book['cover'], ENT_QUOTES) ?>" class="img-fluid rounded shadow-sm mb-3" alt="Cover">
        <?php else: ?>
            <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" style="height:320px;">No Cover</div>
        <?php endif; ?>
        <ul class="list-group">
            <li class="list-group-item"><strong>Penulis:</strong> <?= htmlspecialchars($book['author'], ENT_QUOTES) ?></li>
            <li class="list-group-item"><strong>Tahun:</strong> <?= htmlspecialchars((string)$book['year'], ENT_QUOTES) ?></li>
            <li class="list-group-item"><strong>Kategori:</strong> <?= htmlspecialchars((string)$book['category'], ENT_QUOTES) ?></li>
            <li class="list-group-item"><strong>Rating:</strong> <?= number_format((float)($book['rating'] ?? 0), 1) ?> ★</li>
        </ul>
        <?php if ($user && empty($user['is_admin'])): ?>
            <form method="post" class="mt-3 d-grid gap-2">
                <input type="hidden" name="action" value="favorite">
                <button type="submit" class="btn btn-outline-<?= $isFavorite ? 'danger' : 'primary' ?>">
                    <?= $isFavorite ? 'Hapus dari Favorit' : 'Tambah ke Favorit' ?>
                </button>
            </form>
            <?php if (!empty($book['pdf'])): ?>
                <form method="post" class="mt-2 d-grid gap-2">
                    <?php if ($activeBorrow): ?>
                        <input type="hidden" name="action" value="return">
                        <button type="submit" class="btn btn-warning">Kembalikan Buku</button>
                    <?php else: ?>
                        <input type="hidden" name="action" value="borrow">
                        <button type="submit" class="btn btn-success">Pinjam Buku</button>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        <?php elseif ($user && !empty($user['is_admin'])): ?>
            <p class="mt-3 text-muted"><em>Admin tidak dapat meminjam atau menambah favorit.</em></p>
        <?php else: ?>
            <p class="mt-3"><a href="login.php" class="btn btn-outline-primary w-100">Masuk untuk menambah favorit atau meminjam</a></p>
        <?php endif; ?>
    </div>
    <div class="col-md-8 mb-3">
        <h1 class="h3 mb-2"><?= htmlspecialchars($book['title'], ENT_QUOTES) ?></h1>
        <p class="text-muted mb-3">Oleh <?= htmlspecialchars($book['author'], ENT_QUOTES) ?></p>
        <p><?= nl2br(htmlspecialchars($book['description'], ENT_QUOTES)) ?></p>
        <?php if (!empty($book['pdf'])): ?>
            <a href="read.php?id=<?= (int)$book['id'] ?>" class="btn btn-primary mt-2">Baca Buku</a>
        <?php endif; ?>

        <hr class="mt-4">
        <h2 class="h5 mb-3">Ulasan</h2>
        <?php if (empty($reviews)): ?>
            <p class="text-muted">Belum ada ulasan.</p>
        <?php else: ?>
            <?php foreach ($reviews as $rev):
                $revUser = find_user_by_id((int)$rev['user_id']);
                ?>
                <div class="mb-3">
                    <strong><?= htmlspecialchars($revUser['name'] ?? 'User', ENT_QUOTES) ?></strong>
                    <span class="text-warning ms-2">
                        <?= str_repeat('★', (int)$rev['rating']) ?>
                    </span>
                    <div class="small text-muted"><?= format_date($rev['created_at']) ?></div>
                    <?php if (trim($rev['comment']) !== ''): ?>
                        <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($rev['comment'], ENT_QUOTES)) ?></p>
                    <?php endif; ?>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($user && empty($user['is_admin'])): ?>
            <h3 class="h5 mt-4">Tulis Ulasan</h3>
            <form method="post" class="needs-validation" novalidate>
                <input type="hidden" name="action" value="review">
                <div class="mb-3">
                    <label for="rating" class="form-label">Rating</label>
                    <select class="form-select" id="rating" name="rating" required>
                        <option value="">Pilih rating</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>"><?= $i ?> bintang<?= $i > 1 ? '' : '' ?></option>
                        <?php endfor; ?>
                    </select>
                    <div class="invalid-feedback">Silakan pilih rating.</div>
                </div>
                <div class="mb-3">
                    <label for="comment" class="form-label">Komentar (opsional)</label>
                    <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
            </form>
        <?php elseif ($user && !empty($user['is_admin'])): ?>
            <p class="mt-3 text-muted"><em>Admin tidak dapat menulis ulasan.</em></p>
        <?php else: ?>
            <p class="mt-3">Silakan <a href="login.php">masuk</a> untuk menulis ulasan.</p>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($recommended)): ?>
    <hr class="mt-4">
    <h2 class="h5 mb-3">Buku Rekomendasi</h2>
    <div class="row g-3">
        <?php foreach ($recommended as $r): ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($r['cover'])): ?>
                        <img src="uploads/images/<?= htmlspecialchars($r['cover'], ENT_QUOTES) ?>" class="card-img-top card-book-cover" alt="Cover">
                    <?php else: ?>
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center card-book-cover">No Cover</div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h3 class="h6 card-title mb-1"><?= htmlspecialchars($r['title'], ENT_QUOTES) ?></h3>
                        <p class="text-muted mb-2 small"><?= htmlspecialchars($r['author'], ENT_QUOTES) ?></p>
                        <div class="mt-auto">
                            <a href="book.php?id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-primary w-100">View</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/layout-footer.php'; ?>