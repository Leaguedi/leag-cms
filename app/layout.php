<?php

function render_header(string $title = ''): void {

    $config = require __DIR__ . '/config.php';
    require_once __DIR__ . '/settings.php';

    $site = setting('site_name', $config['site_name']);
    $favicon = setting('favicon', '');
    $appleIcon = setting('apple_touch_icon', '');

    $user = function_exists('current_user') ? current_user() : null;

    $navigation = [];

    try {
        $stmt = db()->query("
            SELECT *
            FROM navigation
            WHERE visible = 1
            ORDER BY sort_order ASC
        ");
        $navigation = $stmt->fetchAll();
    } catch (Throwable $e) {
        $navigation = [];
    }

?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= e($title ? "$title - $site" : $site) ?></title>

    <?php if ($favicon): ?>
        <link rel="icon" type="image/png" href="<?= e($favicon) ?>">
    <?php endif; ?>

    <?php if ($appleIcon): ?>
        <link rel="apple-touch-icon" href="<?= e($appleIcon) ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>

<header class="topbar">
    <div class="wrap nav">

        <a class="brand <?= setting('site_logo') ? 'brand-with-logo' : '' ?>" href="/">
            <?php if (setting('site_logo')): ?>
                <img src="<?= e(setting('site_logo')) ?>" alt="<?= e($site) ?>" class="site-logo">
            <?php else: ?>
                <?= e($site) ?>
            <?php endif; ?>
        </a>

        <nav>
            <?php foreach ($navigation as $nav): ?>
                <a href="<?= e($nav['url']) ?>"><?= e($nav['title']) ?></a>
            <?php endforeach; ?>

            <?php if ($user): ?>

                <?php if (has_permission('admin.access')): ?>
                    <div class="nav-dropdown">
                        <a href="/admin/" class="nav-dropbtn">Admin ▾</a>

                        <div class="nav-dropdown-content">
                            <?php if (has_permission('news.create') || has_permission('news.edit') || has_permission('news.delete')): ?>
                                <a href="/admin/news.php">News verwalten</a>
                            <?php endif; ?>

                            <?php if (has_permission('pages.create') || has_permission('pages.edit') || has_permission('pages.delete')): ?>
                                <a href="/admin/pages.php">Seiten verwalten</a>
                            <?php endif; ?>

                            <?php if (has_permission('users.manage')): ?>
                                <a href="/admin/users.php">Benutzer verwalten</a>
                            <?php endif; ?>

                            <?php if (has_permission('roles.manage')): ?>
                                <a href="/admin/roles.php">Ränge & Rechte</a>
                            <?php endif; ?>

                            <?php if (has_permission('media.manage')): ?>
                                <a href="/admin/media.php">Medienmanager</a>
                            <?php endif; ?>

                            <?php if (has_permission('activity.view')): ?>
                                <a href="/admin/activity.php">
                                    Aktivitäten (Logs)
                                </a>
                            <?php endif; ?>

                            <?php if (has_permission('navigation.manage')): ?>
                                <a href="/admin/navigation.php">Navigation</a>
                            <?php endif; ?>

                            <?php if (has_permission('settings.manage')): ?>
                                <a href="/admin/settings.php">Einstellungen</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="/profile.php">Profil</a>
                <a href="/logout.php">Logout</a>

            <?php else: ?>

                <a href="/login.php">Login</a>
                <a href="/register.php">Registrieren</a>

            <?php endif; ?>
        </nav>

    </div>
</header>

<main class="wrap page">

<?php
}

function render_footer(): void {
?>

</main>

<footer class="footer">
    <div class="wrap">
        © <?= date('Y') ?>
        <?= e(setting('footer_text', 'LEAG CMS')) ?>
    </div>
</footer>

<script src="/assets/js/editor.js"></script>

</body>
</html>

<?php
}