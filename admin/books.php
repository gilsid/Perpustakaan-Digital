<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$books = get_books();
$categories = get_categories();
$categoriesBySlug = [];
foreach ($categories as $cat) {
    $categoriesBySlug[$cat['slug']] = $cat['name'];
}

include __DIR__ . '/../includes/layout-header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Kelola Buku</h1>
    <a href="add-book.php" class="btn btn-primary">Tambah Buku</a>
</div>

<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
        <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>Penulis</th>
            <th>Kategori</th>
            <th>Tahun</th>
            <th>Rating</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($books)): ?>
            <tr><td colspan="7" class="text-muted">Tidak ada buku ditemukan.</td></tr>
        <?php else: ?>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= (int)$book['id'] ?></td>
                    <td><?= htmlspecialchars($book['title'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($book['author'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($categoriesBySlug[$book['category']] ?? $book['category'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars((string)$book['year'], ENT_QUOTES) ?></td>
                    <td><?= number_format((float)($book['rating'] ?? 0), 1) ?></td>
                    <td>
                        <a href="../book.php?id=<?= (int)$book['id'] ?>" class="btn btn-sm btn-outline-secondary">Lihat</a>
                        <a href="edit-book.php?id=<?= (int)$book['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= (int)$book['id'] ?>">Hapus</button>

                        <div class="modal fade" id="deleteModal<?= (int)$book['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= (int)$book['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel<?= (int)$book['id'] ?>">Hapus Buku</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus "<?= htmlspecialchars($book['title'], ENT_QUOTES) ?>"?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form method="post" action="delete-book.php" class="d-inline">
                                            <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/layout-footer.php'; ?>