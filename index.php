<?php
require_once __DIR__ . '/includes/bootstrap.php';

$q = $_GET['q'] ?? null;
$category = $_GET['category'] ?? null;
$year = $_GET['year'] ?? null;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 8;

$filteredBooks = search_filter_books($q, $category, $year);
$pagination = paginate_array($filteredBooks, $page, $perPage);
$books = $pagination['items'];
$categories = get_categories();

// Get top-rated books for featured carousel
$allBooks = get_books();
usort($allBooks, function ($a, $b) {
    $ratingA = (float)($a['rating'] ?? 0);
    $ratingB = (float)($b['rating'] ?? 0);
    return $ratingB <=> $ratingA;
});
$featuredBooks = array_slice($allBooks, 0, 4);

include __DIR__ . '/includes/layout-header.php';
?>
    <div class="hero mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="mb-2">Selamat Datang di Perpustakaan Digital</h1>
            <p class="mb-0">Jelajahi, baca, dan kelola buku favorit Anda dalam satu tempat.</p>
        </div>
        <div class="col-md-6 mt-3 mt-md-0">
            <div class="featured-books-carousel">
                <h5 class="text-white mb-3">Buku Pilihan</h5>
                <div class="row g-2">
                    <?php foreach ($featuredBooks as $fbook): ?>
                        <div class="col-6 col-lg-3">
                            <a href="book.php?id=<?= (int)$fbook['id'] ?>" class="text-decoration-none featured-book-card">
                                <?php if (!empty($fbook['cover'])): ?>
                                    <div class="featured-book-image">
                                        <img src="uploads/images/<?= htmlspecialchars($fbook['cover'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($fbook['title'], ENT_QUOTES) ?>">
                                    </div>
                                <?php else: ?>
                                    <div class="featured-book-image bg-secondary text-white d-flex align-items-center justify-content-center">
                                        <small>No Cover</small>
                                    </div>
                                <?php endif; ?>
                                <div class="featured-book-info mt-2">
                                    <p class="mb-1 small text-white" title="<?= htmlspecialchars($fbook['title'], ENT_QUOTES) ?>"><?= substr(htmlspecialchars($fbook['title'], ENT_QUOTES), 0, 30) . (strlen($fbook['title']) > 30 ? '...' : '') ?></p>
                                    <p class="mb-0 small text-warning">★ <?= number_format((float)($fbook['rating'] ?? 0), 1) ?></p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<form class="row g-3 mb-3 filter-bar" method="get" action="index.php">
    <div class="col-md-4">
        <input type="search" class="form-control" name="q" placeholder="Cari" value="<?= isset($q) ? htmlspecialchars($q, ENT_QUOTES) : '' ?>">
    </div>
    <div class="col-md-3">
        <select name="category" class="form-select">
            <option value="">Semua kategori</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['slug'], ENT_QUOTES) ?>" <?= $category === ($cat['slug'] ?? '') ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <input type="number" name="year" class="form-control" placeholder="Tahun" value="<?= htmlspecialchars((string)$year, ENT_QUOTES) ?>">
    </div>
    <div class="col-md-2 d-grid">
        <button type="submit" class="btn btn-primary">Filter</button>
    </div>
</form>

<?php if (empty($books)): ?>
    <p class="text-muted">Tidak ada buku ditemukan. Coba sesuaikan pencarian atau filter Anda.</p>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($books as $book): ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($book['cover'])): ?>
                        <img src="uploads/images/<?= htmlspecialchars($book['cover'], ENT_QUOTES) ?>" class="card-img-top card-book-cover" alt="Sampul">
                    <?php else: ?>
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center card-book-cover">Tanpa Sampul</div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h2 class="h6 card-title mb-1"><?= htmlspecialchars($book['title'], ENT_QUOTES) ?></h2>
                        <p class="text-muted mb-1 small"><?= htmlspecialchars($book['author'], ENT_QUOTES) ?></p>
                        <p class="text-muted mb-2 small">Tahun: <?= htmlspecialchars((string)($book['year'] ?? ''), ENT_QUOTES) ?></p>
                        <p class="mb-2 small text-warning">Rating: <?= number_format((float)($book['rating'] ?? 0), 1) ?> ★</p>
                        <div class="mt-auto">
                            <div class="d-grid gap-2">
                                <a href="book.php?id=<?= (int)$book['id'] ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                <?php if (!empty($book['pdf'])): ?>
                                    <a href="read.php?id=<?= (int)$book['id'] ?>" class="btn btn-sm btn-primary">Baca</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($pagination['total_page'] > 1): ?>
        <nav aria-label="Books pagination" class="mt-3">
            <ul class="pagination">
                <?php for ($p = 1; $p <= $pagination['total_page']; $p++): ?>
                    <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(['q' => $q, 'category' => $category, 'year' => $year, 'page' => $p]) ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/includes/layout-footer.php'; ?>