<?php
require_once __DIR__ . '/app/auth.php';
require_once __DIR__ . '/app/layout.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!preg_match('/^[a-zA-Z0-9_]{3,32}$/', $username)) $error = 'Username ungültig.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'E-Mail ungültig.';
    elseif (strlen($password) < 8) $error = 'Passwort muss mindestens 8 Zeichen haben.';
    else {
        try {
            $count = (int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
            $role = $count === 0 ? 'admin' : 'user';
            $stmt = db()->prepare('INSERT INTO users (username,email,password_hash,role) VALUES (?,?,?,?)');
            $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
            $_SESSION['user_id'] = (int)db()->lastInsertId();
            header('Location: /admin/'); exit;
        } catch (PDOException $e) { $error = 'Username oder E-Mail schon vergeben.'; }
    }
}
render_header('Registrieren');
?>
<div class="card"><h1>Registrieren</h1><?php if($error): ?><div class="notice"><?= e($error) ?></div><?php endif; ?>
<form method="post"><label>Username</label><input name="username" required><label>E-Mail</label><input type="email" name="email" required><label>Passwort</label><input type="password" name="password" required><button>Account erstellen</button></form></div>
<?php render_footer(); ?>
