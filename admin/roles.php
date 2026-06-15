<?php

require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/layout.php';

require_permission('roles.manage');

$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Rang erstellen
    if (isset($_POST['create_role'])) {

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');

        if ($name && $slug) {

            $stmt = $db->prepare("
                INSERT INTO roles (name, slug)
                VALUES (?, ?)
            ");

            $stmt->execute([
                $name,
                $slug
            ]);
        }
    }

    // Rechte speichern
    if (isset($_POST['save_permissions'])) {

        $roleId = (int)($_POST['role_id'] ?? 0);

        // Alte Rechte löschen
        $stmt = $db->prepare("
            DELETE FROM role_permissions
            WHERE role_id = ?
        ");

        $stmt->execute([$roleId]);

        // Neue Rechte speichern
        foreach ($_POST['permissions'] ?? [] as $permissionId) {

            $stmt = $db->prepare("
                INSERT INTO role_permissions
                (role_id, permission_id)
                VALUES (?, ?)
            ");

            $stmt->execute([
                $roleId,
                (int)$permissionId
            ]);
        }
    }

    header('Location: /admin/roles.php');
    exit;
}

// Rollen laden
$roles = $db->query("
    SELECT *
    FROM roles
    ORDER BY id ASC
")->fetchAll();

// Rechte laden
$permissions = $db->query("
    SELECT *
    FROM permissions
    ORDER BY name ASC
")->fetchAll();

render_header('Rängeverwaltung');

?>

<div class="card">

    <h1>Rängeverwaltung</h1>

    <h2>Neuen Rang erstellen</h2>

    <form method="POST" style="margin-bottom:30px;">

        <div style="margin-bottom:10px;">
            <input
                type="text"
                name="name"
                placeholder="Rangname"
                required
            >
        </div>

        <div style="margin-bottom:10px;">
            <input
                type="text"
                name="slug"
                placeholder="z.B. moderator"
                required
            >
        </div>

        <button type="submit" name="create_role">
            Rang erstellen
        </button>

    </form>

</div>

<div class="card" style="margin-top:20px;">
    <h2>Vorhandene Ränge</h2>
    <p>Klicke auf einen Rang, um die Rechte zu bearbeiten.</p>

    <?php foreach ($roles as $role): ?>

        <?php
        $stmt = $db->prepare("
            SELECT permission_id
            FROM role_permissions
            WHERE role_id = ?
        ");
        $stmt->execute([$role['id']]);
        $assigned = $stmt->fetchAll(PDO::FETCH_COLUMN);
        ?>

        <details style="margin-bottom:12px; border:1px solid rgba(255,255,255,.12); border-radius:10px; padding:14px;">
            <summary style="cursor:pointer; font-weight:bold; font-size:18px;">
                <?= e($role['name']) ?>
                <small style="opacity:.7;">(<?= e($role['slug']) ?>)</small>
            </summary>

            <form method="POST" style="margin-top:20px;">
                <input type="hidden" name="role_id" value="<?= (int)$role['id'] ?>">

                <?php foreach ($permissions as $permission): ?>
                    <label style="display:block; margin-bottom:10px;">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="<?= (int)$permission['id'] ?>"
                            <?= in_array($permission['id'], $assigned) ? 'checked' : '' ?>
                        >
                        <?= e($permission['name']) ?>
                    </label>
                <?php endforeach; ?>

                <button type="submit" name="save_permissions">
                    Rechte speichern
                </button>
            </form>
        </details>

    <?php endforeach; ?>
</div>

<?php render_footer(); ?>