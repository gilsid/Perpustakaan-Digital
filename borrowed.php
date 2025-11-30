<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_regular_user();

$user = current_user();
$records = get_user_borrow_records((int)$user['id']);
$books = get_books();
$booksById = [];
foreach ($books as $b) {
    $booksById[$b['id']] = $b;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $bookId = (int)$_POST['book_id'];
    return_book((int)$user['id'], $bookId);
    set_flash('success', 'Book returned successfully.');
    redirect('borrowed.php');
}

include __DIR__ . '/includes/layout-header.php';
?>
<h1 class="h4 mb-3">Buku Pinjaman</h1>
<?php if (empty($records)): ?>
    <p class="text-muted">Anda belum meminjam buku apapun.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
            <tr>
                <th>Judul</th>
                <th>Tanggal Peminjaman</th>
                <th>Tanggal Pengembalian</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $row):
                $book = $booksById[$row['book_id']] ?? null;
                if (!$book) continue;
                ?>
                <tr>
                    <td><?= htmlspecialchars($book['title'], ENT_QUOTES) ?></td>
                    <td><?= format_date($row['borrowed_at']) ?></td>
                    <td><?= $row['returned_at'] ? format_date($row['returned_at']) : '<span class="badge bg-success">Aktif</span>' ?></td>
                    <td>
                        <?php if (empty($row['returned_at'])): ?>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Kembalikan</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php include __DIR__ . '/includes/layout-footer.php'; ?>