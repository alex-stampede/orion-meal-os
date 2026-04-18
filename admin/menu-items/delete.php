<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$id = (int)($_GET['id'] ?? 0);
$menuId = (int)($_GET['menu_id'] ?? 0);

if ($id > 0 && $menuId > 0) {
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ? AND weekly_menu_id = ?");
    $stmt->execute([$id, $menuId]);
}

header('Location: /admin/menu-items/index.php?menu_id=' . $menuId . '&deleted=1');
exit;