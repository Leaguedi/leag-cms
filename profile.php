<?php
require_once __DIR__ . '/app/auth.php';
require_once __DIR__ . '/app/layout.php';

require_login();

$db = db();
$user = current_user();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'profile') {
        $email = trim($_POST['email'] ?? '');
        $bio = trim($_POST['bio'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Bitte gib eine gültige E-Mail-Adresse ein.';
        } else {
            $stmt = $db->prepare("
                UPDATE users
                SET email = ?, bio = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $email,
                $bio,
                $user['id']
            ]);

            $message = 'Profil wurde gespeichert.';
            $user = current_user();
        }
    }

    if ($action === 'password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $newPasswordRepeat = $_POST['new_password_repeat'] ?? '';

        $stmt = $db->prepare("
            SELECT password
            FROM users
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->execute([$user['id']]);
        $passwordHash = $stmt->fetchColumn();

        if (!$passwordHash || !password_verify($currentPassword, $passwordHash)) {
            $error = 'Aktuelles Passwort ist falsch.';
        } elseif (strlen($newPassword) < 8) {
            $error = 'Das neue Passwort muss mindestens 8 Zeichen lang sein.';
        } elseif ($newPassword !== $newPasswordRepeat) {
            $error = 'Die neuen Passwörter stimmen nicht überein.';
        } else {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $db->prepare("
                UPDATE users
                SET password = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $newHash,
                $user['id']
            ]);

            $message = 'Passwort wurde geändert.';
        }
    }
}

render_header('Mein Profil');
?>

<div class="card">
    <h1>Mein Profil</h1>
    <p class="meta">
        Benutzername: <strong><?= e($user['username']) ?></strong><br>
        Rang: <strong><?= e($user['role_name'] ?? 'User') ?></strong>
    </p>

    <?php if ($message): ?>
        <div class="notice"><?= e($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="notice" style="border-color:rgba(255,95,95,.35);background:rgba(255,95,95,.12);color:#ffdada;">
            <?= e($error) ?>
        </div>
    <?php endif; ?>
</div>

<div class="grid">
    <div class="card">
        <h2>Profil bearbeiten</h2>

        <form method="POST">
            <input type="hidden" name="action" value="profile">

            <label>E-Mail</label>
            <input
                type="email"
                name="email"
                value="<?= e($user['email']) ?>"
                required
            >

            <label>Profiltext / Motto</label>
            <textarea name="bio" placeholder="Schreib etwas über dich..."><?= e($user['bio'] ?? '') ?></textarea>

            <button type="submit">Profil speichern</button>
        </form>
    </div>

    <div class="card">
        <h2>Passwort ändern</h2>

        <form method="POST">
            <input type="hidden" name="action" value="password">

            <label>Aktuelles Passwort</label>
            <input type="password" name="current_password" required>

            <label>Neues Passwort</label>
            <input type="password" name="new_password" required>

            <label>Neues Passwort wiederholen</label>
            <input type="password" name="new_password_repeat" required>

            <button type="submit">Passwort ändern</button>
        </form>
    </div>
</div>

<?php render_footer(); ?>