<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/layout.php';
require_admin();
render_header('Admin');
?>
<div class="card"><h1>Adminpanel</h1><p>Willkommen, <?= e(current_user()['username']) ?>.</p>
<p><a class="btn" href="/admin/news.php">News verwalten</a> <a class="btn" href="/admin/pages.php">Seiten verwalten</a> <a class="btn" href="/admin/users.php">User</a></p></div>
<?php render_footer(); ?>
