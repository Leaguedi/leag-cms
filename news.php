<?php
require_once __DIR__ . '/app/auth.php';
require_once __DIR__ . '/app/layout.php';
$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare('SELECT n.*, u.username author FROM news n LEFT JOIN users u ON u.id=n.author_id WHERE n.slug=? AND n.published=1 LIMIT 1');
$stmt->execute([$slug]);
$item = $stmt->fetch();
if (!$item) { http_response_code(404); exit('News nicht gefunden.'); }
render_header($item['title']);
?>
<article class="card"><h1><?= e($item['title']) ?></h1><small>Von <?= e($item['author'] ?: 'System') ?> · <?= e($item['created_at']) ?></small><div><?= $item['body'] ?></div></article>
<?php render_footer(); ?>
