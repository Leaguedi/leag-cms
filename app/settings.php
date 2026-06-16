<?php

function setting(string $key, $default = null) {

    static $settings = null;

    if ($settings === null) {

        $settings = [];

        try {

            $stmt = db()->query("
                SELECT `key`, `value`
                FROM settings
            ");

            foreach ($stmt->fetchAll() as $row) {
                $settings[$row['key']] = $row['value'];
            }

        } catch (Throwable $e) {
            $settings = [];
        }
    }

    return $settings[$key] ?? $default;
}