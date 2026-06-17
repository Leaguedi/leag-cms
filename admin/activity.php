<?php

require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/layout.php';

require_permission('activity.view');

$db = db();

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 50;
$offset = ($page - 1) * $perPage;

$search = trim($_GET['search'] ?? '');
$action = trim($_GET['action'] ?? '');
$userId = (int)($_GET['user_id'] ?? 0);

$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(l.description LIKE ? OR l.action LIKE ? OR u.username LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if ($action !== '') {
    $where[] = "l.action = ?";
    $params[] = $action;
}

if ($userId > 0) {
    $where[] = "l.user_id = ?";
    $params[] = $userId;
}

$whereSql = $where
    ? 'WHERE ' . implode(' AND ', $where)
    : '';

$countStmt = $db->prepare("
    SELECT COUNT(*)
    FROM activity_logs l
    LEFT JOIN users u ON u.id = l.user_id
    $whereSql
");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

$stmt = $db->prepare("
    SELECT
        l.*,
        u.username
    FROM activity_logs l
    LEFT JOIN users u ON u.id = l.user_id
    $whereSql
    ORDER BY l.created_at DESC
    LIMIT ?
    OFFSET ?
");

$bindIndex = 1;

foreach ($params as $param) {
    $stmt->bindValue($bindIndex, $param);
    $bindIndex++;
}

$stmt->bindValue($bindIndex, $perPage, PDO::PARAM_INT);
$bindIndex++;

$stmt->bindValue($bindIndex, $offset, PDO::PARAM_INT);
$stmt->execute();

$logs = $stmt->fetchAll();

$totalPages = max(1, (int)ceil($total / $perPage));

$actions = $db->query("
    SELECT DISTINCT action
    FROM activity_logs
    ORDER BY action ASC
")->fetchAll(PDO::FETCH_COLUMN);

$users = $db->query("
    SELECT DISTINCT
        u.id,
        u.username
    FROM activity_logs l
    JOIN users u ON u.id = l.user_id
    ORDER BY u.username ASC
")->fetchAll();

function activity_query(array $extra = []): string {
    $query = array_merge($_GET, $extra);
    return '?' . http_build_query($query);
}

render_header('Aktivitäten');
?>

<div class="card">

    <h1>Aktivitäten</h1>

    <p class="meta">
        Insgesamt <?= (int)$total ?> Einträge
    </p>

    <form method="GET" class="activity-filter">

        <div>
            <label>Suche</label>
            <input
                type="text"
                name="search"
                value="<?= e($search) ?>"
                placeholder="Beschreibung, Aktion oder Benutzer..."
            >
        </div>

        <div>
            <label>Aktion</label>
            <select name="action">
                <option value="">Alle Aktionen</option>

                <?php foreach ($actions as $act): ?>
                    <option
                        value="<?= e($act) ?>"
                        <?= $action === $act ? 'selected' : '' ?>
                    >
                        <?= e($act) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Benutzer</label>
            <select name="user_id">
                <option value="0">Alle Benutzer</option>

                <?php foreach ($users as $u): ?>
                    <option
                        value="<?= (int)$u['id'] ?>"
                        <?= $userId === (int)$u['id'] ? 'selected' : '' ?>
                    >
                        <?= e($u['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="activity-filter-actions">
            <button type="submit">Filtern</button>
            <a class="btn secondary" href="/admin/activity.php">Zurücksetzen</a>
        </div>

    </form>

</div>

<div class="card">

    <table class="admin-table">

        <tr>
            <th>Datum</th>
            <th>Benutzer</th>
            <th>Aktion</th>
            <th>Beschreibung</th>
            <th>IP</th>
        </tr>

        <?php foreach ($logs as $log): ?>

            <tr>

                <td>
                    <?= e(date(
                        'd.m.Y H:i',
                        strtotime($log['created_at'])
                    )) ?>
                </td>

                <td>
                    <?= e($log['username'] ?: 'System') ?>
                </td>

                <td>
                    <?= e($log['action']) ?>
                </td>

                <td>
                    <?= e($log['description']) ?>
                </td>

                <td>
                    <?= e($log['ip_address'] ?? '-') ?>
                </td>

            </tr>

        <?php endforeach; ?>

        <?php if (!$logs): ?>
            <tr>
                <td colspan="5">
                    Keine Aktivitäten gefunden.
                </td>
            </tr>
        <?php endif; ?>

    </table>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                <a
                    class="btn <?= $i === $page ? '' : 'secondary' ?>"
                    href="<?= e(activity_query(['page' => $i])) ?>"
                >
                    <?= $i ?>
                </a>

            <?php endfor; ?>

        </div>
    <?php endif; ?>

</div>

<?php render_footer(); ?>