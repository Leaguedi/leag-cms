<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/layout.php';
require_once __DIR__ . '/../app/activity.php';

require_permission('users.manage');

$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int)($_POST['user_id'] ?? 0);
    $roleId = (int)($_POST['role_id'] ?? 4);

    if ($userId > 0) {
        $stmt = $db->prepare("UPDATE users SET role_id = ? WHERE id = ?");
        $stmt->execute([$roleId, $userId]);
        $userInfo = $db->prepare("
            SELECT username
            FROM users
            WHERE id = ?
        ");
        $userInfo->execute([$userId]);
        $userInfo = $userInfo->fetch();

        $roleInfo = $db->prepare("
            SELECT name
            FROM roles
            WHERE id = ?
        ");
        $roleInfo->execute([$roleId]);
        $roleInfo = $roleInfo->fetch();

        activity_log(
            'user.role.change',
            'Benutzer "' .
            ($userInfo['username'] ?? 'Unbekannt') .
            '" erhielt Rolle "' .
            ($roleInfo['name'] ?? 'Unbekannt') .
            '"'
        );
    }

    header('Location: /admin/users.php');
    exit;
}

$users = $db->query("
    SELECT 
        u.id,
        u.username,
        u.email,
        u.created_at,
        r.name AS role_name,
        u.role_id
    FROM users u
    LEFT JOIN roles r ON r.id = u.role_id
    ORDER BY u.id ASC
")->fetchAll();

$roles = $db->query("
    SELECT id, name
    FROM roles
    ORDER BY id ASC
")->fetchAll();

render_header('Benutzer verwalten');
?>

<div class="card">
    <h1>Benutzer verwalten</h1>
    <p class="meta">Hier kannst du Benutzern einen Rang zuweisen.</p>

    <div class="user-admin-list">
        <?php foreach ($users as $user): ?>
            <form method="POST" class="user-admin-row">
                <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">

                <div class="user-admin-info">
                    <strong><?= e($user['username']) ?></strong>
                    <span><?= e($user['email']) ?></span>
                    <small>Aktueller Rang: <?= e($user['role_name'] ?? 'Kein Rang') ?></small>
                </div>

                <div class="user-admin-actions">
                    <select name="role_id" class="user-role-select">
                        <?php foreach ($roles as $role): ?>
                            <option
                                value="<?= (int)$role['id'] ?>"
                                <?= (int)$user['role_id'] === (int)$role['id'] ? 'selected' : '' ?>
                            >
                                <?= e($role['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit">Speichern</button>
                </div>
            </form>
        <?php endforeach; ?>
    </div>
</div>

<?php render_footer(); ?>