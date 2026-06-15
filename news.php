<?php
require_once __DIR__ . '/app/auth.php';
require_once __DIR__ . '/app/layout.php';

$slug = $_GET['slug'] ?? '';

$stmt = db()->prepare("
    SELECT
        n.*,
        u.username AS author
    FROM news n
    LEFT JOIN users u
        ON u.id = n.author_id
    WHERE n.slug = ?
    AND n.published = 1
    LIMIT 1
");

$stmt->execute([$slug]);

$item = $stmt->fetch();

if (!$item) {
    http_response_code(404);
    exit('News nicht gefunden.');
}

render_header($item['title']);
?>

<article class="card news-detail">

    <?php if (!empty($item['image'])): ?>
        <img
            class="news-detail-image"
            src="<?= e($item['image']) ?>"
            alt="<?= e($item['title']) ?>"
        >
    <?php endif; ?>

    <?php if (!empty($item['category'])): ?>
        <span class="badge">
            <?= e($item['category']) ?>
        </span>
    <?php endif; ?>

    <h1>
        <?= e($item['title']) ?>
    </h1>

    <p class="meta">
        Von <?= e($item['author'] ?: 'System') ?>
        · <?= e(date('d.m.Y H:i', strtotime($item['created_at']))) ?>
    </p>

    <?php if (!empty($item['teaser'])): ?>
        <p class="news-teaser">
            <?= e($item['teaser']) ?>
        </p>
    <?php endif; ?>

    <div class="news-content">
        <?= $item['body'] ?>
    </div>

</article>

<?php render_footer(); ?>