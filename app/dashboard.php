<?php

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

if (($_SESSION['role'] ?? '') !== 'customer') {
    header('Location: /admin/dashboard.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT s.*, mp.name
    FROM subscriptions s
    JOIN meal_plans mp ON mp.id = s.meal_plan_id
    WHERE s.user_id = ? AND s.status = 'active'
    ORDER BY s.id DESC
    LIMIT 1
");
$stmt->execute([$userId]);
$sub = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi cuenta | Orion Meal OS</title>
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
                    <div class="badge">Cliente</div>
                    <h1>Mi cuenta</h1>
                    <p>Bienvenido, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Cliente') ?></p>
                </div>
                <a class="button-secondary" href="/logout.php">Cerrar sesión</a>
            </div>

            <?php if ($sub): ?>
                <div class="grid">
                    <div class="mini-card">
                        <span class="label">Plan activo</span>
                        <strong><?= htmlspecialchars($sub['name']) ?></strong>
                    </div>
                    <div class="mini-card">
                        <span class="label">Inicio</span>
                        <strong><?= htmlspecialchars($sub['start_date']) ?></strong>
                    </div>
                    <div class="mini-card">
                        <span class="label">Fin</span>
                        <strong><?= htmlspecialchars($sub['end_date']) ?></strong>
                    </div>
                </div>

                <div class="actions-row" style="margin-top: 24px;">
                    <a class="button" href="/app/select-meals.php">Seleccionar comidas</a>
                    <a class="button-secondary" href="/app/plans.php">Ver planes</a>
                </div>
            <?php else: ?>
                <div class="message-error">No tienes un plan activo todavía.</div>

                <div class="actions-row">
                    <a class="button" href="/app/plans.php">Elegir plan</a>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
