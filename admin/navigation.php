<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/layout.php';
require_once __DIR__ . '/../app/activity.php';

require_permission('navigation.manage');

$db = db();

/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $stmt = $db->prepare("
        SELECT title
        FROM navigation
        WHERE id = ?
    ");
    $stmt->execute([$id]);
    $deletedItem = $stmt->fetch();

    $stmt = $db->prepare("
        DELETE FROM navigation
        WHERE id = ?
    ");
    $stmt->execute([$id]);

    activity_log(
        'navigation.delete',
        'Navigationseintrag gelöscht: ' . ($deletedItem['title'] ?? 'Unbekannt')
    );

    header('Location: /admin/navigation.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| SAVE
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int)($_POST['id'] ?? 0);

    $title = trim($_POST['title'] ?? '');
    $url = trim($_POST['url'] ?? '');

    $sortOrder = (int)($_POST['sort_order'] ?? 0);

    $visible = isset($_POST['visible'])
        ? 1
        : 0;

    if ($id) {

        $stmt = $db->prepare("
            UPDATE navigation
            SET
                title = ?,
                url = ?,
                sort_order = ?,
                visible = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $title,
            $url,
            $sortOrder,
            $visible,
            $id
        ]);

        activity_log(
            'navigation.edit',
            'Navigation geändert: ' . $title
        );

    } else {

        $stmt = $db->prepare("
            INSERT INTO navigation (
                title,
                url,
                sort_order,
                visible
            )
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $title,
            $url,
            $sortOrder,
            $visible
        ]);

        activity_log(
            'navigation.create',
            'Navigation erstellt: ' . $title
        );
    }

    header('Location: /admin/navigation.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| EDIT
|--------------------------------------------------------------------------
*/

$edit = null;

if (isset($_GET['edit'])) {

    $stmt = $db->prepare("
        SELECT *
        FROM navigation
        WHERE id = ?
    ");

    $stmt->execute([
        (int)$_GET['edit']
    ]);

    $edit = $stmt->fetch();
}

$items = $db->query("
    SELECT *
    FROM navigation
    ORDER BY sort_order ASC
")->fetchAll();

render_header('Navigation');
?>

<div class="card">

    <h1>
        <?= $edit
            ? 'Menüpunkt bearbeiten'
            : 'Menüpunkt erstellen' ?>
    </h1>

    <form method="POST">

        <input
            type="hidden"
            name="id"
            value="<?= e((string)($edit['id'] ?? '')) ?>"
        >

        <label>Titel</label>

        <input
            name="title"
            value="<?= e($edit['title'] ?? '') ?>"
            required
        >

        <label>URL</label>

        <input
            name="url"
            value="<?= e($edit['url'] ?? '') ?>"
            placeholder="/seite/team"
            required
        >

        <label>Reihenfolge</label>

        <input
            type="number"
            name="sort_order"
            value="<?= e((string)($edit['sort_order'] ?? 0)) ?>"
        >

        <label>
            <input
                type="checkbox"
                name="visible"
                style="width:auto"
                <?= !isset($edit['visible']) || $edit['visible'] ? 'checked' : '' ?>
            >
            Sichtbar
        </label>

        <br><br>

        <button type="submit">
            Speichern
        </button>

    </form>

</div>

<div class="card">

    <h2>Navigation</h2>

    <table class="admin-table">

        <tr>
            <th>Titel</th>
            <th>URL</th>
            <th>Sortierung</th>
            <th>Status</th>
            <th>Aktionen</th>
        </tr>

        <?php foreach ($items as $item): ?>

            <tr>

                <td><?= e($item['title']) ?></td>

                <td><?= e($item['url']) ?></td>

                <td><?= (int)$item['sort_order'] ?></td>

                <td>
                    <?= $item['visible']
                        ? 'Sichtbar'
                        : 'Versteckt' ?>
                </td>

                <td>
                    <a href="?edit=<?= (int)$item['id'] ?>">
                        Bearbeiten
                    </a>

                    ·

                    <a
                        href="?delete=<?= (int)$item['id'] ?>"
                        onclick="return confirm('Wirklich löschen?')"
                    >
                        Löschen
                    </a>
                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>

<?php render_footer(); ?>