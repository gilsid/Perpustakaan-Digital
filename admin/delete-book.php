<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('books.php');
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if (!$id) {
    set_flash('danger', 'Invalid book ID.');
    redirect('books.php');
}

$books = get_books();
$book = null;
$newBooks = [];
foreach ($books as $b) {
    if ((int)$b['id'] === $id) {
        $book = $b;
        continue;
    }
    $newBooks[] = $b;
}

if (!$book) {
    set_flash('danger', 'Book not found.');
    redirect('books.php');
}

// Optionally clean up related entries
$favorites = array_filter(get_favorites(), function ($fav) use ($id) {
    return (int)$fav['book_id'] !== $id;
});
$borrow = array_filter(get_borrow_records(), function ($row) use ($id) {
    return (int)$row['book_id'] !== $id;
});
$reviews = array_filter(get_reviews(), function ($row) use ($id) {
    return (int)$row['book_id'] !== $id;
});

save_books(array_values($newBooks));
save_favorites(array_values($favorites));
save_borrow_records(array_values($borrow));
save_reviews(array_values($reviews));

$admin = current_user();
add_log((int)$admin['id'], 'delete_book', 'book', $id, 'Deleted book "' . $book['title'] . '"');

set_flash('success', 'Book deleted successfully.');
redirect('books.php');