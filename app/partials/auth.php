<?php

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

if (($_SESSION['role'] ?? '') !== 'customer') {
    header('Location: /admin/dashboard.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Cliente';