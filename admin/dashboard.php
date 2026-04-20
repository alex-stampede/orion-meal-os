<?php

declare(strict_types=1);

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Usuario';
$userRole = $_SESSION['role'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Orion Meal OS</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <main class="shell">
        <section class="card page-card">
            <div class="page-top">
                <div>
                    <div class="badge">Panel interno</div>
                    <h1>Bienvenido, <?= htmlspecialchars($userName) ?></h1>
                    <p class="helper-text">Ya estás dentro de Orion Meal OS. Desde aquí puedes gestionar planes, menús, clientes y suscripciones.</p>
                </div>
                <a class="button" href="/logout.php">Cerrar sesión</a>
            </div>

<div class="customer-grid">
    <div class="mini-card">
        <span class="label">Rol</span>
        <strong><?= htmlspecialchars($userRole) ?></strong>
    </div>

    <div class="mini-card">
        <span class="label">Módulo</span>
        <strong><a href="/admin/plans/index.php">Gestión de planes</a></strong>
    </div>

    <div class="mini-card">
        <span class="label">Clientes</span>
        <strong><a href="/admin/customers/index.php">Ver clientes</a></strong>
    </div>

    <div class="mini-card">
        <span class="label">Menús</span>
        <strong><a href="/admin/menus/index.php">Ver menús</a></strong>
    </div>

    <div class="mini-card">
        <span class="label">Sistema</span>
        <strong>Activo</strong>
    </div>
</div>
        </section>
    </main>
</body>
</html>
