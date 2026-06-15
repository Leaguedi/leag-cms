<?php
require_once __DIR__ . '/app/auth.php';
require_once __DIR__ . '/app/layout.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT * FROM users WHERE username=? OR email=? LIMIT 1');
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = (int)$user['id'];
        header('Location: /admin/'); exit;
    }
    $error = 'Login ungültig.';
}
render_header('Login');
?>
<div class="card"><h1>Login</h1><?php if($error): ?><div class="notice"><?= e($error) ?></div><?php endif; ?>
<form method="post"><label>Username oder E-Mail</label><input name="login" required><label>Passwort</label><input type="password" name="password" required><button>Einloggen</button></form></div>
<?php render_footer(); ?>
