<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('~[^a-z0-9]+~', '-', $text);
    $text = trim($text, '-');
    return $text ?: 'category';
}

$categories = get_categories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            set_flash('danger', 'Category name is required.');
        } else {
            $slug = slugify($name);
            $categories[] = [
                'id'   => next_id($categories),
                'name' => $name,
                'slug' => $slug,
            ];
            save_categories($categories);
            $admin = current_user();
            add_log((int)$admin['id'], 'add_category', 'category', null, 'Added category "' . $name . '"');
            set_flash('success', 'Category added.');
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        foreach ($categories as &$cat) {
            if ((int)$cat['id'] === $id) {
                $cat['name'] = $name;
                $cat['slug'] = slugify($name);
                break;
            }
        }
        unset($cat);
        save_categories($categories);
        $admin = current_user();
        add_log((int)$admin['id'], 'edit_category', 'category', $id, 'Edited category');
        set_flash('success', 'Category updated.');
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $categories = array_filter($categories, function ($cat) use ($id) {
            return (int)$cat['id'] !== $id;
        });
        save_categories(array_values($categories));
        $admin = current_user();
        add_log((int)$admin['id'], 'delete_category', 'category', $id, 'Deleted category');
        set_flash('success', 'Category deleted.');
    }

    redirect('categories.php');
}

include __DIR__ . '/../includes/layout-header.php';
?>
<h1 class="h4 mb-3">Categories</h1>
<div class="row">
    <div class="col-md-5">
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h5 mb-3">Add Category</h2>
                <form method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback">Please enter a name.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <h2 class="h5 mb-3">Existing Categories</h2>
                <div class="table-responsive">
                    <table class="table table-striped align-middle table-sm">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($categories)): ?>
                            <tr><td colspan="4" class="text-muted">No categories.</td></tr>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?= (int)$cat['id'] ?></td>
                                    <td>
                                        <form method="post" class="d-flex gap-2">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                                            <input type="text" class="form-control form-control-sm" name="name" value="<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                        </form>
                                    </td>
                                    <td><?= htmlspecialchars($cat['slug'], ENT_QUOTES) ?></td>
                                    <td>
                                        <form method="post" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/layout-footer.php'; ?>