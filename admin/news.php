<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/layout.php';
require_once __DIR__ . '/../app/activity.php';

if (
    !has_permission('news.create') &&
    !has_permission('news.edit') &&
    !has_permission('news.delete')
) {
    http_response_code(403);
    exit('Kein Zugriff.');
}

$db = db();

function slugify($string) {
    $string = strtolower(trim($string));

    $replace = [
        'ä' => 'ae',
        'ö' => 'oe',
        'ü' => 'ue',
        'ß' => 'ss'
    ];

    $string = str_replace(
        array_keys($replace),
        array_values($replace),
        $string
    );

    $string = preg_replace('/[^a-z0-9]+/', '-', $string);

    return trim($string, '-') ?: uniqid('news-');
}

/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

if (
    isset($_GET['delete']) &&
    has_permission('news.delete')
) {
    $stmt = $db->prepare("
        DELETE FROM news
        WHERE id = ?
    ");

    $logNews = $db->prepare("
        SELECT title
        FROM news
        WHERE id = ?
    ");

    $logNews->execute([
        (int)$_GET['delete']
    ]);

    $logNews = $logNews->fetch();

    $stmt->execute([
        (int)$_GET['delete']
    ]);

    activity_log(
        'news.delete',
        'News gelöscht: ' . ($logNews['title'] ?? 'Unbekannt')
    );

    header('Location: /admin/news.php');
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
        FROM news
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([
        (int)$_GET['edit']
    ]);

    $edit = $stmt->fetch();
}

/*
|--------------------------------------------------------------------------
| SAVE
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int)($_POST['id'] ?? 0);

    $title = trim($_POST['title'] ?? '');

    $slug = trim($_POST['slug'] ?? '');

    $slug = $slug ?: slugify($title);

    $category = trim($_POST['category'] ?? '');

    $image = trim($_POST['image'] ?? '');

    $teaser = trim($_POST['teaser'] ?? '');

    $body = $_POST['body'] ?? '';

    $published = isset($_POST['published'])
        ? 1
        : 0;

    if ($id) {

        require_permission('news.edit');

        $stmt = $db->prepare("
            UPDATE news
            SET
                title = ?,
                slug = ?,
                category = ?,
                image = ?,
                teaser = ?,
                body = ?,
                published = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $title,
            $slug,
            $category,
            $image,
            $teaser,
            $body,
            $published,
            $id
        ]);

        activity_log(
            'news.edit',
            'News bearbeitet: ' . $title
        );

    } else {

        require_permission('news.create');

        $stmt = $db->prepare("
            INSERT INTO news (
                title,
                slug,
                category,
                image,
                teaser,
                body,
                published,
                author_id
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $title,
            $slug,
            $category,
            $image,
            $teaser,
            $body,
            $published,
            current_user()['id']
        ]);
        activity_log(
        'news.create',
        'News erstellt: ' . $title
        );
    }

    header('Location: /admin/news.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| NEWS LIST
|--------------------------------------------------------------------------
*/

$items = $db->query("
    SELECT
        n.*,
        u.username AS author
    FROM news n
    LEFT JOIN users u
        ON u.id = n.author_id
    ORDER BY n.created_at DESC
")->fetchAll();

render_header('News verwalten');
?>

<div class="card">

    <h1>
        <?= $edit ? 'News bearbeiten' : 'News erstellen' ?>
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

        <label>Slug</label>

        <input
            name="slug"
            value="<?= e($edit['slug'] ?? '') ?>"
            placeholder="automatisch"
        >

        <label>Kategorie</label>

        <input
            name="category"
            value="<?= e($edit['category'] ?? '') ?>"
            placeholder="Update, Event, Community..."
        >

        <label>Titelbild URL</label>

        <input
            name="image"
            value="<?= e($edit['image'] ?? '') ?>"
            placeholder="/uploads/media/bild.jpg"
        >

        <a
            href="/admin/media.php"
            class="btn secondary"
            target="_blank"
            style="margin-bottom:20px;"
        >
            Medienmanager öffnen
        </a>

        <label>Teaser</label>

        <textarea name="teaser"><?= e($edit['teaser'] ?? '') ?></textarea>

        <label>Inhalt</label>

        <textarea
            class="html-editor"
            name="body"
            required
        ><?= e($edit['body'] ?? '') ?></textarea>

        <label>
            <input
                type="checkbox"
                name="published"
                style="width:auto"
                <?= !isset($edit['published']) || $edit['published'] ? 'checked' : '' ?>
            >
            Veröffentlicht
        </label>

        <br><br>

        <button type="submit">
            Speichern
        </button>

    </form>
</div>

<div class="card">

    <h2>Alle News</h2>

    <table class="admin-table">

        <tr>
            <th>Titel</th>
            <th>Kategorie</th>
            <th>Autor</th>
            <th>Status</th>
            <th>Aktionen</th>
        </tr>

        <?php foreach ($items as $item): ?>

            <tr>

                <td>
                    <?= e($item['title']) ?>
                </td>

                <td>
                    <?= e($item['category'] ?: '-') ?>
                </td>

                <td>
                    <?= e($item['author'] ?: 'System') ?>
                </td>

                <td>
                    <?= $item['published']
                        ? 'Online'
                        : 'Entwurf' ?>
                </td>

                <td>

                    <?php if (has_permission('news.edit')): ?>
                        <a href="?edit=<?= (int)$item['id'] ?>">
                            Bearbeiten
                        </a>
                    <?php endif; ?>

                    <?php if (has_permission('news.delete')): ?>
                        ·
                        <a
                            href="?delete=<?= (int)$item['id'] ?>"
                            onclick="return confirm('Wirklich löschen?')"
                        >
                            Löschen
                        </a>
                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>

<?php render_footer(); ?>
