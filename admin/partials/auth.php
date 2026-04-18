<?php

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

if (!in_array($_SESSION['role'] ?? '', ['super_admin', 'admin'], true)) {
    header('Location: /app/dashboard.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Usuario';
$userRole = $_SESSION['role'] ?? 'admin';