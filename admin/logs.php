<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$logs = get_logs();

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;

usort($logs, function ($a, $b) {
    return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
});

$pagination = paginate_array($logs, $page, $perPage);
$items = $pagination['items'];

include __DIR__ . '/../includes/layout-header.php';
?>
<h1 class="h4 mb-3">Activity Logs</h1>
<div class="table-responsive">
    <table class="table table-striped table-sm align-middle">
        <thead>
        <tr>
            <th>Time</th>
            <th>Admin</th>
            <th>Action</th>
            <th>Entity</th>
            <th>Message</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($items)): ?>
            <tr><td colspan="5" class="text-muted">No logs.</td></tr>
        <?php else: ?>
            <?php foreach ($items as $log):
                $adminUser = $log['user_id'] ? find_user_by_id((int)$log['user_id']) : null;
                ?>
                <tr>
                    <td><?= format_date($log['created_at']) ?></td>
                    <td><?= htmlspecialchars($adminUser['email'] ?? '-', ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($log['action'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($log['entity_type'] . ' #' . (string)($log['entity_id'] ?? ''), ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($log['message'], ENT_QUOTES) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($pagination['total_page'] > 1): ?>
    <nav aria-label="Logs pagination" class="mt-3">
        <ul class="pagination">
            <?php for ($p = 1; $p <= $pagination['total_page']; $p++): ?>
                <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php include __DIR__ . '/../includes/layout-footer.php'; ?>