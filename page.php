<?php
require_once __DIR__ . '/app/auth.php';
require_once __DIR__ . '/app/layout.php';
$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare('SELECT * FROM pages WHERE slug=? LIMIT 1');
$stmt->execute([$slug]);
$page = $stmt->fetch();
if (!$page) { http_response_code(404); exit('Seite nicht gefunden.'); }
render_header($page['title']);
?>
<article class="card"><h1><?= e($page['title']) ?></h1><div><?= $page['body'] ?></div></article>
<?php render_footer(); ?>
