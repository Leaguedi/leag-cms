<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/layout.php';
require_admin();
function slugify_page($s){$s=strtolower(trim($s));$s=preg_replace('/[^a-z0-9äöüß]+/u','-',$s);$s=str_replace(['ä','ö','ü','ß'],['ae','oe','ue','ss'],$s);return trim($s,'-') ?: uniqid('page-');}
if (isset($_GET['delete'])) { $stmt=db()->prepare('DELETE FROM pages WHERE id=?'); $stmt->execute([(int)$_GET['delete']]); header('Location: /admin/pages.php'); exit; }
$edit = null;
if (isset($_GET['edit'])) { $stmt=db()->prepare('SELECT * FROM pages WHERE id=?'); $stmt->execute([(int)$_GET['edit']]); $edit=$stmt->fetch(); }
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id=(int)($_POST['id']??0); $title=trim($_POST['title']??''); $slug=trim($_POST['slug']??'') ?: slugify_page($title); $body=$_POST['body']??'';
    if ($id) {$stmt=db()->prepare('UPDATE pages SET title=?,slug=?,body=? WHERE id=?'); $stmt->execute([$title,$slug,$body,$id]);}
    else {$stmt=db()->prepare('INSERT INTO pages (title,slug,body) VALUES (?,?,?)'); $stmt->execute([$title,$slug,$body]);}
    header('Location: /admin/pages.php'); exit;
}
$items=db()->query('SELECT * FROM pages ORDER BY title')->fetchAll();
render_header('Seiten verwalten');
?>
<div class="card"><h1><?= $edit?'Seite bearbeiten':'Seite erstellen' ?></h1><form method="post"><input type="hidden" name="id" value="<?= e((string)($edit['id']??'')) ?>"><label>Titel</label><input name="title" value="<?= e($edit['title']??'') ?>" required><label>Slug</label><input name="slug" value="<?= e($edit['slug']??'') ?>"><label>Inhalt HTML erlaubt</label><textarea class="html-editor" name="body" required><?= e($edit['body']??'') ?></textarea><button>Speichern</button></form></div>
<div class="card"><h2>Alle Seiten</h2><table class="admin-table"><tr><th>Titel</th><th>Slug</th><th>Aktion</th></tr><?php foreach($items as $i): ?><tr><td><?= e($i['title']) ?></td><td><?= e($i['slug']) ?></td><td><a href="?edit=<?= $i['id'] ?>">Bearbeiten</a> · <a href="/page.php?slug=<?= e($i['slug']) ?>">Öffnen</a> · <a href="?delete=<?= $i['id'] ?>" onclick="return confirm('Löschen?')">Löschen</a></td></tr><?php endforeach; ?></table></div>
<?php render_footer(); ?>
