<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/activity.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function current_user(): ?array {
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = db()->prepare("
        SELECT 
            u.id,
            u.username,
            u.email,
            u.role,
            u.role_id,
            u.bio,
            u.avatar,
            u.created_at,
            r.name AS role_name,
            r.slug AS role_slug
        FROM users u
        LEFT JOIN roles r ON r.id = u.role_id
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);

    return $stmt->fetch() ?: null;
}

function is_logged_in(): bool {
    return current_user() !== null;
}

function is_admin(): bool {
    $user = current_user();

    return $user && (
        ($user['role'] ?? '') === 'admin'
        || ($user['role_slug'] ?? '') === 'admin'
        || (int)($user['role_id'] ?? 0) === 1
    );
}

function has_permission(string $permission): bool {
    $user = current_user();

    if (!$user) {
        return false;
    }

    if (is_admin()) {
        return true;
    }

    if (empty($user['role_id'])) {
        return false;
    }

    $stmt = db()->prepare("
        SELECT COUNT(*)
        FROM role_permissions rp
        JOIN permissions p ON p.id = rp.permission_id
        WHERE rp.role_id = ?
        AND p.slug = ?
    ");
    $stmt->execute([(int)$user['role_id'], $permission]);

    return (int)$stmt->fetchColumn() > 0;
}

function require_login(): void {
    if (!current_user()) {
        header('Location: /login.php');
        exit;
    }
}

function require_admin(): void {
    require_permission('admin.access');
}

function require_permission(string $permission): void {
    if (!has_permission($permission)) {
        http_response_code(403);
        echo 'Kein Zugriff.';
        exit;
    }
}

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}