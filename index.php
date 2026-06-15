<?php
require_once __DIR__ . '/app/auth.php';
require_once __DIR__ . '/app/layout.php';
$stmt = db()->query('SELECT n.*, u.username author FROM news n LEFT JOIN users u ON u.id=n.author_id WHERE published=1 ORDER BY created_at DESC LIMIT 10');
$news = $stmt->fetchAll();
render_header('Home');
?>
<section class="hero">
    <div>
        <span class="badge">Community Projekt</span>
        <h1>Deine moderne Leag CMS</h1>
        <p>Ein dunkler, moderner Landingpage-Look mit großem Hero-Bereich, klarer Navigation, Community-Boxen und News — inspiriert vom Aufbau moderner RP-/Gaming-Projektseiten.</p>
        <div class="hero-actions">
            <a class="btn" href="/register.php">Jetzt registrieren</a>
            <a class="btn secondary" href="/page.php?slug=team">Mehr erfahren</a>
        </div>
    </div>
</section>
<div class="grid">
    <section>
        <div class="section-title">Aktuelle News</div>
        <?php foreach ($news as $item): ?>
            <article class="card">
                <span class="badge">Update</span>
                <h2><?= e($item['title']) ?></h2>
                <p><?= e($item['teaser'] ?: '') ?></p>
                <small>Von <?= e($item['author'] ?: 'System') ?> · <?= e($item['created_at']) ?></small><br><br>
                <a class="btn" href="/news.php?slug=<?= e($item['slug']) ?>">Lesen</a>
            </article>
        <?php endforeach; ?>
        <?php if (!$news): ?>
            <article class="card"><h2>Noch keine News</h2><p>Lege im Adminpanel deinen ersten Beitrag an.</p></article>
        <?php endif; ?>
    </section>
    <aside>
        <div class="card">
            <span class="badge">Server Status</span>
            <h3>Community</h3>
            <div class="status-box">
                <div class="status-line"><span>Status</span><strong>Online</strong></div>
                <div class="status-line"><span>Mitglieder</span><strong>–</strong></div>
                <div class="status-line"><span>Discord</span><strong>Bereit</strong></div>
            </div>
        </div>
        <div class="card">
            <span class="badge">Mitmachen</span>
            <h3>Werde Teil der Community</h3>
            <p>Registriere dich, lies News, verfolge Events und baue deine Fansite Stück für Stück weiter aus.</p>
            <?php if (!current_user()): ?><a class="btn" href="/register.php">Account erstellen</a><?php endif; ?>
        </div>
    </aside>
</div>
<section class="feature-grid">
    <article class="card feature-card"><span class="badge">Gameplay</span><h3>Events & Aktionen</h3><p>Nutze Seiten oder News für Events, Gewinnspiele, Updates und Community-Ankündigungen.</p></article>
    <article class="card feature-card"><span class="badge">Team</span><h3>Team & Support</h3><p>Baue eine Teamseite mit Rängen, Aufgaben und Ansprechpartnern.</p></article>
    <article class="card feature-card"><span class="badge">Radio</span><h3>Widgets</h3><p>Discord, Serverstatus oder Shoutbox als Widget hinzu.</p></article>
    <article class="card feature-card"><span class="badge">Admin</span><h3>Einfach verwalten</h3><p>News, Seiten und Nutzer bleiben direkt im Adminpanel bearbeitbar.</p></article>
</section>
<?php render_footer(); ?>
