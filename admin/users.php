<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$users = get_users();

include __DIR__ . '/../includes/layout-header.php';
?>
<h1 class="h4 mb-3">Users</h1>
<div class="table-responsive">
    <table class="table table-striped align-middle table-sm">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Admin</th>
            <th>Created At</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr><td colspan="5" class="text-muted">No users found.</td></tr>
        <?php else: ?>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= (int)$u['id'] ?></td>
                    <td><?= htmlspecialchars($u['name'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($u['email'], ENT_QUOTES) ?></td>
                    <td><?= !empty($u['is_admin']) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td><?= htmlspecialchars($u['created_at'], ENT_QUOTES) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/layout-footer.php'; ?>