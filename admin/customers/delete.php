<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("
        DELETE FROM users
        WHERE id = ? AND role = 'customer'
    ");
    $stmt->execute([$id]);
}

header('Location: /admin/customers/index.php?deleted=1');
exit;