<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_regular_user();

$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $bookId = (int)$_POST['book_id'];
    remove_favorite((int)$user['id'], $bookId);
    set_flash('success', 'Removed from favorites.');
    redirect('favorites.php');
}

$favorites = get_user_favorites((int)$user['id']);
$books = get_books();
$booksById = [];
foreach ($books as $b) {
    $booksById[$b['id']] = $b;
}

include __DIR__ . '/includes/layout-header.php';
?>
<h1 class="h4 mb-3">Favorit Anda</h1>
<?php if (empty($favorites)): ?>
    <p class="text-muted">Anda belum memiliki buku favorit.</p>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($favorites as $fav):
            $book = $booksById[$fav['book_id']] ?? null;
            if (!$book) continue;
            ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($book['cover'])): ?>
                        <img src="uploads/images/<?= htmlspecialchars($book['cover'], ENT_QUOTES) ?>" class="card-img-top card-book-cover" alt="Cover">
                    <?php else: ?>
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center card-book-cover">No Cover</div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h2 class="h6 card-title mb-1"><?= htmlspecialchars($book['title'], ENT_QUOTES) ?></h2>
                        <p class="text-muted mb-1 small"><?= htmlspecialchars($book['author'], ENT_QUOTES) ?></p>
                        <div class="mt-auto">
                            <div class="d-grid gap-2">
                                <a href="book.php?id=<?= (int)$book['id'] ?>" class="btn btn-sm btn-outline-primary">Details</a>
                                <form method="post">
                                    <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php include __DIR__ . '/includes/layout-footer.php'; ?>