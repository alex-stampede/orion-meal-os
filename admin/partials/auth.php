<?php

declare(strict_types=1);

require __DIR__ . '/../../config/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Usuario';
$userRole = $_SESSION['role'] ?? 'admin';
