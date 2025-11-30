<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$book = $id ? get_book($id) : null;

if (!$book || empty($book['pdf'])) {
    set_flash('danger', 'Book or PDF not found.');
    redirect('index.php');
}

include __DIR__ . '/includes/layout-header.php';
?>
<h1 class="h4 mb-3">Membaca: <?= htmlspecialchars($book['title'], ENT_QUOTES) ?></h1>

<div class="ratio ratio-4x3 mb-3">
    <iframe src="uploads/pdf/<?= htmlspecialchars($book['pdf'], ENT_QUOTES) ?>" title="PDF Reader" allowfullscreen></iframe>
</div>

<?php include __DIR__ . '/includes/layout-footer.php'; ?>