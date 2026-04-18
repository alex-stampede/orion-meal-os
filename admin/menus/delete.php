<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM weekly_menus WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: /admin/menus/index.php?deleted=1');
exit;