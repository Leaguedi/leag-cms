<?php
require_once __DIR__ . '/app/auth.php';
session_destroy();
header('Location: /');
