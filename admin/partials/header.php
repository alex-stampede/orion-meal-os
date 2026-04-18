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
            <div class="badge">Orion Meal OS</div>
            <h2>Panel</h2>
        </div>

        <nav class="admin-nav">
            <a href="/admin/dashboard.php">Dashboard</a>
            <a href="/admin/plans/index.php">Planes</a>
            <a href="/admin/menus/index.php">Menús</a>
</nav>
        </nav>

        <div class="admin-user-box">
            <span><?= htmlspecialchars($userName) ?></span>
            <small><?= htmlspecialchars($userRole) ?></small>
            <a class="button" href="/logout.php">Cerrar sesión</a>
        </div>
    </aside>

    <main class="admin-content">
