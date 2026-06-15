<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/layout.php';
require_admin();
function slugify($s){$s=strtolower(trim($s));$s=preg_replace('/[^a-z0-9äöüß]+/u','-',$s);$s=str_replace(['ä','ö','ü','ß'],['ae','oe','ue','ss'],$s);return trim($s,'-') ?: uniqid('news-');}
if (isset($_GET['delete'])) { $stmt=db()->prepare('DELETE FROM news WHERE id=?'); $stmt->execute([(int)$_GET['delete']]); header('Location: /admin/news.php'); exit; }
$edit = null;
if (isset($_GET['edit'])) { $stmt=db()->prepare('SELECT * FROM news WHERE id=?'); $stmt->execute([(int)$_GET['edit']]); $edit=$stmt->fetch(); }
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id=(int)($_POST['id']??0); $title=trim($_POST['title']??''); $slug=trim($_POST['slug']??'') ?: slugify($title); $teaser=trim($_POST['teaser']??''); $body=$_POST['body']??''; $published=isset($_POST['published'])?1:0;
    if ($id) {$stmt=db()->prepare('UPDATE news SET title=?,slug=?,teaser=?,body=?,published=? WHERE id=?'); $stmt->execute([$title,$slug,$teaser,$body,$published,$id]);}
    else {$stmt=db()->prepare('INSERT INTO news (title,slug,teaser,body,published,author_id) VALUES (?,?,?,?,?,?)'); $stmt->execute([$title,$slug,$teaser,$body,$published,current_user()['id']]);}
    header('Location: /admin/news.php'); exit;
}
$items=db()->query('SELECT * FROM news ORDER BY created_at DESC')->fetchAll();
render_header('News verwalten');
?>
<div class="card"><h1><?= $edit?'News bearbeiten':'News erstellen' ?></h1><form method="post"><input type="hidden" name="id" value="<?= e((string)($edit['id']??'')) ?>"><label>Titel</label><input name="title" value="<?= e($edit['title']??'') ?>" required><label>Slug</label><input name="slug" value="<?= e($edit['slug']??'') ?>"><label>Teaser</label><textarea name="teaser" placeholder="Kurzbeschreibung für die News-Karte"><?= e($edit['teaser']??'') ?></textarea><label>Inhalt HTML erlaubt</label><textarea class="html-editor" name="body" required><?= e($edit['body']??'') ?></textarea><label><input type="checkbox" name="published" style="width:auto" <?= !isset($edit['published'])||$edit['published']?'checked':'' ?>> Veröffentlicht</label><button>Speichern</button></form></div>
<div class="card"><h2>Alle News</h2><table class="admin-table"><tr><th>Titel</th><th>Status</th><th>Aktion</th></tr><?php foreach($items as $i): ?><tr><td><?= e($i['title']) ?></td><td><?= $i['published']?'online':'offline' ?></td><td><a href="?edit=<?= $i['id'] ?>">Bearbeiten</a> · <a href="?delete=<?= $i['id'] ?>" onclick="return confirm('Löschen?')">Löschen</a></td></tr><?php endforeach; ?></table></div>
<?php render_footer(); ?>
