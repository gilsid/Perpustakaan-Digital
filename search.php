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

include __DIR__ . '/includes/layout-header.php';
?>
<h1 class="h4 mb-3">Hasil Pencarian</h1>
<form class="row g-3 mb-3" method="get" action="search.php">
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
        <input type="number" name="year" class="form-control" placeholder="Year" value="<?= htmlspecialchars((string)$year, ENT_QUOTES) ?>">
    </div>
    <div class="col-md-2 d-grid">
        <button type="submit" class="btn btn-primary">Filter</button>
    </div>
</form>

<?php if (empty($books)): ?>
    <p class="text-muted">Tidak ada buku ditemukan untuk pencarian Anda.</p>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($books as $book): ?>
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
                        <p class="text-muted mb-2 small">Year: <?= htmlspecialchars((string)($book['year'] ?? ''), ENT_QUOTES) ?></p>
                        <p class="mb-2 small text-warning">Rating: <?= number_format((float)($book['rating'] ?? 0), 1) ?> ★</p>
                        <div class="mt-auto">
                            <div class="d-grid gap-2">
                                <a href="book.php?id=<?= (int)$book['id'] ?>" class="btn btn-sm btn-outline-primary">Details</a>
                                <?php if (!empty($book['pdf'])): ?>
                                    <a href="read.php?id=<?= (int)$book['id'] ?>" class="btn btn-sm btn-primary">Read</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($pagination['total_page'] > 1): ?>
        <nav aria-label="Search pagination" class="mt-3">
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