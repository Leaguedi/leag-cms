<?php
function render_header(string $title = ''): void {
    $config = require __DIR__ . '/config.php';
    $site = $config['site_name'];
    $user = function_exists('current_user') ? current_user() : null;
    ?><!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ? "$title - $site" : $site) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="topbar">
    <div class="wrap nav">
        <a class="brand" href="/"><?= e($site) ?></a>
        <nav>
            <a href="/">Start</a>
            <a href="/page.php?slug=team">Team</a>
            <a href="/page.php?slug=radio">Radio</a>
            <a href="/page.php?slug=regeln">Regeln</a>
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
<?php }
function render_footer(): void { ?>
</main>
<footer class="footer"><div class="wrap">© <?= date('Y') ?> Community CMS · Modernes PHP 8 Fansite-System</div></footer>
<script src="/assets/js/editor.js"></script>
</body></html><?php }
