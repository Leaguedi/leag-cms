<?php

function activity_log(string $action, string $description = ''): void
{
    try {
        $user = function_exists('current_user') ? current_user() : null;

        $stmt = db()->prepare("
            INSERT INTO activity_logs
            (user_id, action, description, ip_address)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $user['id'] ?? null,
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    } catch (Throwable $e) {
        // Log darf niemals die Seite kaputt machen
    }
}