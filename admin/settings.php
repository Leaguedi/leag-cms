<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/layout.php';
require_once __DIR__ . '/../app/settings.php';

require_permission('settings.manage');

$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] ?? [] as $key => $value) {
        $stmt = $db->prepare("
            INSERT INTO settings (`key`, `value`)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE
            `value` = VALUES(`value`)
        ");

        $stmt->execute([
            $key,
            trim($value)
        ]);
    }

    header('Location: /admin/settings.php?saved=1');
    exit;
}

render_header('Einstellungen');
?>

<div class="card">

    <h1>Einstellungen</h1>

    <?php if (isset($_GET['saved'])): ?>
        <div class="notice">Einstellungen gespeichert.</div>
    <?php endif; ?>

    <form method="POST">

        <label>Seitenname</label>
        <input
            name="settings[site_name]"
            value="<?= e(setting('site_name')) ?>"
        >

        <label>Footer Text</label>
        <input
            name="settings[footer_text]"
            value="<?= e(setting('footer_text')) ?>"
        >

        <label>Discord URL</label>
        <input
            name="settings[discord_url]"
            value="<?= e(setting('discord_url')) ?>"
        >

        <hr>

        <h2>Branding</h2>

        <label>Logo URL</label>
        <input
            name="settings[site_logo]"
            value="<?= e(setting('site_logo')) ?>"
            placeholder="/uploads/media/logo.png"
        >

        <label>Favicon URL</label>
        <input
            name="settings[favicon]"
            value="<?= e(setting('favicon')) ?>"
            placeholder="/uploads/media/favicon.png"
        >

        <label>Apple Touch Icon</label>
        <input
            name="settings[apple_touch_icon]"
            value="<?= e(setting('apple_touch_icon')) ?>"
            placeholder="/uploads/media/apple-icon.png"
        >

        <a
            href="/admin/media.php"
            class="btn secondary"
            target="_blank"
            style="margin-bottom:20px;"
        >
            Medienmanager öffnen
        </a>

        <label>Hero Hintergrund</label>
        <input
            name="settings[hero_background]"
            value="<?= e(setting('hero_background')) ?>"
            placeholder="/uploads/media/background.jpg"
        >

        <hr>

        <h2>Hero Bereich</h2>

        <label>Badge</label>
        <input
            name="settings[hero_badge]"
            value="<?= e(setting('hero_badge')) ?>"
        >

        <label>Titel</label>
        <input
            name="settings[hero_title]"
            value="<?= e(setting('hero_title')) ?>"
        >

        <label>Beschreibung</label>
        <textarea name="settings[hero_text]"><?= e(setting('hero_text')) ?></textarea>

        <label>Button Text</label>
        <input
            name="settings[hero_button_text]"
            value="<?= e(setting('hero_button_text')) ?>"
        >

        <label>Button URL</label>
        <input
            name="settings[hero_button_url]"
            value="<?= e(setting('hero_button_url')) ?>"
        >

        <label>2. Button Text</label>
        <input
            name="settings[hero_second_button_text]"
            value="<?= e(setting('hero_second_button_text')) ?>"
        >

        <label>2. Button URL</label>
        <input
            name="settings[hero_second_button_url]"
            value="<?= e(setting('hero_second_button_url')) ?>"
        >

        <br><br>

        <button type="submit">Speichern</button>

    </form>

</div>

<?php render_footer(); ?>