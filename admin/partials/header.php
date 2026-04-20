<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Panel') ?> | Orion Meal OS</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<div class="admin-shell">
    <aside class="admin-sidebar card">
<div class="admin-brand">
    <div class="badge"><?= htmlspecialchars((string)($branding['business_name'] ?? 'Orion Meal OS')) ?></div>

    <?php if (!empty($branding['logo_path'])): ?>
        <div style="margin:14px 0;">
            <img src="<?= htmlspecialchars($branding['logo_path']) ?>" alt="Logo" style="max-width:180px; max-height:70px; object-fit:contain;">
        </div>
    <?php else: ?>
        <h2>Panel</h2>
    <?php endif; ?>
</div>

<nav class="admin-nav">
    <a href="/admin/dashboard.php">Dashboard</a>
    <a href="/admin/plans/index.php">Planes</a>
    <a href="/admin/menus/index.php">Menús</a>
    <a href="/admin/customers/index.php">Clientes</a>
    <a href="/admin/orders/index.php">Pedidos</a>
    <a href="/admin/settings/branding.php">Branding</a>
</nav>

        <div class="admin-user-box">
            <span><?= htmlspecialchars($userName) ?></span>
            <small><?= htmlspecialchars($userRole) ?></small>
            <a class="button" href="/logout.php">Cerrar sesión</a>
        </div>
    </aside>

    <main class="admin-content">
