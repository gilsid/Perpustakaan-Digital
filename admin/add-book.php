<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

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
        $books = get_books();

        // Handle uploads
        $coverName = null;
        if (!empty($_FILES['cover']['name'])) {
            $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
            $coverName = 'cover_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            @move_uploaded_file($_FILES['cover']['tmp_name'], UPLOADS_PATH . '/images/' . $coverName);
        }

        $pdfName = null;
        if (!empty($_FILES['pdf']['name'])) {
            $ext = pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION);
            $pdfName = 'book_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            @move_uploaded_file($_FILES['pdf']['tmp_name'], UPLOADS_PATH . '/pdf/' . $pdfName);
        }

        $newBook = [
            'id'          => next_id($books),
            'title'       => $title,
            'author'      => $author,
            'year'        => $year,
            'category'    => $category,
            'description' => $description,
            'cover'       => $coverName,
            'pdf'         => $pdfName,
            'rating'      => 0.0,
            'created_at'  => now_iso(),
            'updated_at'  => now_iso(),
        ];
        $books[] = $newBook;

        if (save_books($books)) {
            $admin = current_user();
            add_log((int)$admin['id'], 'add_book', 'book', (int)$newBook['id'], 'Added book "' . $newBook['title'] . '"');
            set_flash('success', 'Book added successfully.');
            redirect('books.php');
        } else {
            set_flash('danger', 'Failed to save book.');
        }
    }
}

include __DIR__ . '/../includes/layout-header.php';
?>
<h1 class="h4 mb-3">Add Book</h1>
<form method="post" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
    <div class="col-md-6">
        <label class="form-label" for="title">Title</label>
        <input type="text" class="form-control" id="title" name="title" required>
        <div class="invalid-feedback">Please enter the title.</div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="author">Author</label>
        <input type="text" class="form-control" id="author" name="author" required>
        <div class="invalid-feedback">Please enter the author.</div>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="year">Year</label>
        <input type="number" class="form-control" id="year" name="year">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="category">Category</label>
        <select class="form-select" id="category" name="category" required>
            <option value="">Select category</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['slug'], ENT_QUOTES) ?>"><?= htmlspecialchars($cat['name'], ENT_QUOTES) ?></option>
            <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Please select a category.</div>
    </div>
    <div class="col-12">
        <label class="form-label" for="description">Description</label>
        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="cover">Cover Image</label>
        <input type="file" class="form-control" id="cover" name="cover" accept="image/*">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="pdf">PDF File</label>
        <input type="file" class="form-control" id="pdf" name="pdf" accept="application/pdf">
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Save Book</button>
        <a href="books.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>
<?php include __DIR__ . '/../includes/layout-footer.php'; ?>