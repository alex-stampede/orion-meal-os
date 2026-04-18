<?php

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
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
            <div class="badge">Mi cuenta</div>
            <h1>Dashboard cliente</h1>

            <?php if ($sub): ?>
                <p>Plan: <strong><?= htmlspecialchars($sub['name']) ?></strong></p>
                <p>Vigencia: <?= htmlspecialchars($sub['start_date']) ?> a <?= htmlspecialchars($sub['end_date']) ?></p>

                <div class="actions-row">
                    <a class="button" href="/app/select-meals.php">Seleccionar comidas</a>
                    <a class="button-secondary" href="/app/plans.php">Ver otros planes</a>
                </div>
            <?php else: ?>
                <p>No tienes plan activo.</p>
                <div class="actions-row">
                    <a class="button" href="/app/plans.php">Ver planes</a>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>