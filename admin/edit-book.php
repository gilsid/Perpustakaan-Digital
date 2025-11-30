<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$books = get_books();
$book = null;
foreach ($books as $b) {
    if ((int)$b['id'] === $id) {
        $book = $b;
        break;
    }
}

if (!$book) {
    set_flash('danger', 'Book not found.');
    redirect('books.php');
}

$categories = get_categories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $year = (int)($_POST['year'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '' || $author === '' || $category === '') {
        set_flash('danger', 'Title, author, and category are required.');
    } else {
        foreach ($books as &$b) {
            if ((int)$b['id'] === $id) {
                $b['title'] = $title;
                $b['author'] = $author;
                $b['year'] = $year;
                $b['category'] = $category;
                $b['description'] = $description;

                if (!empty($_FILES['cover']['name'])) {
                    $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
                    $coverName = 'cover_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                    @move_uploaded_file($_FILES['cover']['tmp_name'], UPLOADS_PATH . '/images/' . $coverName);
                    $b['cover'] = $coverName;
                }

                if (!empty($_FILES['pdf']['name'])) {
                    $ext = pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION);
                    $pdfName = 'book_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                    @move_uploaded_file($_FILES['pdf']['tmp_name'], UPLOADS_PATH . '/pdf/' . $pdfName);
                    $b['pdf'] = $pdfName;
                }

                $b['updated_at'] = now_iso();
                $book = $b;
                break;
            }
        }
        unset($b);

        if (save_books($books)) {
            $admin = current_user();
            add_log((int)$admin['id'], 'edit_book', 'book', $id, 'Edited book "' . $book['title'] . '"');
            set_flash('success', 'Book updated successfully.');
            redirect('books.php');
        } else {
            set_flash('danger', 'Failed to update book.');
        }
    }
}

include __DIR__ . '/../includes/layout-header.php';
?>
<h1 class="h4 mb-3">Edit Book</h1>
<form method="post" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
    <div class="col-md-6">
        <label class="form-label" for="title">Title</label>
        <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($book['title'], ENT_QUOTES) ?>" required>
        <div class="invalid-feedback">Please enter the title.</div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="author">Author</label>
        <input type="text" class="form-control" id="author" name="author" value="<?= htmlspecialchars($book['author'], ENT_QUOTES) ?>" required>
        <div class="invalid-feedback">Please enter the author.</div>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="year">Year</label>
        <input type="number" class="form-control" id="year" name="year" value="<?= htmlspecialchars((string)$book['year'], ENT_QUOTES) ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="category">Category</label>
        <select class="form-select" id="category" name="category" required>
            <option value="">Select category</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['slug'], ENT_QUOTES) ?>" <?= $book['category'] === $cat['slug'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Please select a category.</div>
    </div>
    <div class="col-12">
        <label class="form-label" for="description">Description</label>
        <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($book['description'], ENT_QUOTES) ?></textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="cover">Cover Image (optional)</label>
        <input type="file" class="form-control" id="cover" name="cover" accept="image/*">
        <?php if (!empty($book['cover'])): ?>
            <div class="small text-muted mt-1">Current: <?= htmlspecialchars($book['cover'], ENT_QUOTES) ?></div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="pdf">PDF File (optional)</label>
        <input type="file" class="form-control" id="pdf" name="pdf" accept="application/pdf">
        <?php if (!empty($book['pdf'])): ?>
            <div class="small text-muted mt-1">Current: <?= htmlspecialchars($book['pdf'], ENT_QUOTES) ?></div>
        <?php endif; ?>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Update Book</button>
        <a href="books.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>
<?php include __DIR__ . '/../includes/layout-footer.php'; ?>