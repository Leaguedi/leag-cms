<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/layout.php';

require_permission('admin.access');

$db = db();
$user = current_user();

$userCount = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$newsCount = (int)$db->query("SELECT COUNT(*) FROM news")->fetchColumn();
$pageCount = (int)$db->query("SELECT COUNT(*) FROM pages")->fetchColumn();
$roleCount = (int)$db->query("SELECT COUNT(*) FROM roles")->fetchColumn();

$latestUsers = $db->query("
    SELECT username, email, created_at
    FROM users
    ORDER BY id DESC
    LIMIT 5
")->fetchAll();

$latestNews = $db->query("
    SELECT id, title, created_at
    FROM news
    ORDER BY id DESC
    LIMIT 5
")->fetchAll();

render_header('Admin Dashboard');
?>

<div class="card">
    <h1>Admin Dashboard</h1>
    <p class="meta">
        Willkommen zurück, <strong><?= e($user['username'] ?? 'User') ?></strong>.
    </p>
</div>

<div class="dashboard-stats">
    <div class="dashboard-stat-card">
        <span>Benutzer</span>
        <strong><?= $userCount ?></strong>
    </div>

    <div class="dashboard-stat-card">
        <span>News</span>
        <strong><?= $newsCount ?></strong>
    </div>

    <div class="dashboard-stat-card">
        <span>Seiten</span>
        <strong><?= $pageCount ?></strong>
    </div>

    <div class="dashboard-stat-card">
        <span>Ränge</span>
        <strong><?= $roleCount ?></strong>
    </div>
</div>

<div class="grid">
    <div>
        <div class="card">
            <h2>Letzte Registrierungen</h2>

            <?php if (!$latestUsers): ?>
                <p class="meta">Noch keine Benutzer vorhanden.</p>
            <?php else: ?>
                <?php foreach ($latestUsers as $latestUser): ?>
                    <div class="dashboard-list-item">
                        <div>
                            <strong><?= e($latestUser['username']) ?></strong>
                            <small><?= e($latestUser['email']) ?></small>
                        </div>
                        <span><?= e(date('d.m.Y', strtotime($latestUser['created_at']))) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Letzte News</h2>

            <?php if (!$latestNews): ?>
                <p class="meta">Noch keine News vorhanden.</p>
            <?php else: ?>
                <?php foreach ($latestNews as $news): ?>
                    <div class="dashboard-list-item">
                        <div>
                            <strong><?= e($news['title']) ?></strong>
                            <small>ID: <?= (int)$news['id'] ?></small>
                        </div>
                        <span><?= e(date('d.m.Y', strtotime($news['created_at']))) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <aside>
        <div class="card">
            <h2>Schnellaktionen</h2>
            <p class="meta">Nur Aktionen, für die du Rechte hast, werden angezeigt.</p>

            <div class="quick-actions">
                <?php if (has_permission('news.create')): ?>
                    <a class="btn" href="/admin/news.php">News verwalten</a>
                <?php endif; ?>

                <?php if (has_permission('pages.create')): ?>
                    <a class="btn secondary" href="/admin/pages.php">Seiten verwalten</a>
                <?php endif; ?>

                <?php if (has_permission('users.manage')): ?>
                    <a class="btn secondary" href="/admin/users.php">Benutzer verwalten</a>
                <?php endif; ?>

                <?php if (has_permission('roles.manage')): ?>
                    <a class="btn secondary" href="/admin/roles.php">Ränge & Rechte</a>
                <?php endif; ?>

                <?php if (has_permission('settings.manage')): ?>
                    <a class="btn secondary" href="/admin/settings.php">Einstellungen</a>
                <?php endif; ?>

                <?php if (
                    !has_permission('news.create') &&
                    !has_permission('pages.create') &&
                    !has_permission('users.manage') &&
                    !has_permission('roles.manage') &&
                    !has_permission('settings.manage')
                ): ?>
                    <p class="meta">Keine Schnellaktionen verfügbar.</p>
                <?php endif; ?>
            </div>
        </div>
    </aside>
</div>

<?php render_footer(); ?>