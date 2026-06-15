<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/layout.php';

require_permission('media.manage');

$uploadDir = __DIR__ . '/../uploads/media/';
$webDir = '/uploads/media/';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_FILES['image']['name'])) {

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif'
        ];

        $fileType = mime_content_type($_FILES['image']['tmp_name']);

        if (!isset($allowed[$fileType])) {
            $error = 'Nur JPG, PNG, WEBP und GIF erlaubt.';
        } else {

            $extension = $allowed[$fileType];

            $filename =
                time() . '_' .
                bin2hex(random_bytes(6)) .
                '.' . $extension;

            $target = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $message = 'Bild erfolgreich hochgeladen.';
            } else {
                $error = 'Upload fehlgeschlagen.';
            }
        }
    }
}

$files = [];

if (is_dir($uploadDir)) {

    $scan = scandir($uploadDir);

    foreach ($scan as $file) {

        if ($file === '.' || $file === '..') {
            continue;
        }

        $path = $uploadDir . $file;

        if (is_file($path)) {
            $files[] = $file;
        }
    }

    rsort($files);
}

render_header('Medienmanager');
?>

<div class="card">
    <h1>Medienmanager</h1>

    <p class="meta">
        Lade Bilder hoch oder kopiere eine URL für den HTML Editor.
    </p>

    <?php if ($message): ?>
        <div class="notice">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="notice" style="background:rgba(255,95,95,.14);border-color:rgba(255,95,95,.22);">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <label>Bild auswählen</label>

        <input
            type="file"
            name="image"
            accept=".jpg,.jpeg,.png,.gif,.webp"
            required
        >

        <button type="submit">
            Bild hochladen
        </button>

    </form>
</div>

<div class="card">
    <h2>Hochgeladene Bilder</h2>

    <?php if (!$files): ?>
        <p class="meta">
            Noch keine Bilder vorhanden.
        </p>
    <?php else: ?>

        <div class="media-grid">

            <?php foreach ($files as $file): ?>

                <?php
                $url = $webDir . $file;
                ?>

                <div class="media-card">

                    <img
                        src="<?= e($url) ?>"
                        alt=""
                    >

                    <input
                        type="text"
                        value="<?= e($url) ?>"
                        readonly
                        onclick="this.select()"
                    >

                    <button
                        type="button"
                        onclick="copyMediaUrl('<?= e($url) ?>')"
                    >
                        URL kopieren
                    </button>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>
</div>

<script>
function copyMediaUrl(url) {
    navigator.clipboard.writeText(url);

    alert('Bild URL kopiert!');
}
</script>

<?php render_footer(); ?>