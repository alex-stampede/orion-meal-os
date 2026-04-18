<?php

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$planId = (int)($_GET['plan_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM meal_plans WHERE id = ? AND status = 'active' LIMIT 1");
$stmt->execute([$planId]);
$plan = $stmt->fetch();

if (!$plan) {
    die('Plan no encontrado');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start = date('Y-m-d');
    $end = date('Y-m-d', strtotime('+' . (int)$plan['duration_weeks'] . ' weeks'));

    $stmt = $pdo->prepare("
        INSERT INTO subscriptions (
            user_id,
            meal_plan_id,
            start_date,
            end_date,
            status,
            payment_status,
            total_amount
        ) VALUES (?, ?, ?, ?, 'active', 'paid', ?)
    ");

    $stmt->execute([
        $userId,
        $planId,
        $start,
        $end,
        $plan['price']
    ]);

    header('Location: /app/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar plan | Orion Meal OS</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <main class="shell">
        <section class="card page-card" style="max-width: 700px; margin: 40px auto;">
            <div class="badge">Confirmar plan</div>
            <h1><?= htmlspecialchars($plan['name']) ?></h1>
            <p>$<?= number_format((float)$plan['price'], 2) ?></p>
            <p><?= (int)$plan['meals_per_week'] ?> comidas por semana</p>
            <p><?= (int)$plan['duration_weeks'] ?> semana(s)</p>

            <form method="POST" class="actions-row">
                <button class="button" type="submit">Confirmar plan</button>
                <a class="button-secondary" href="/app/plans.php">Volver</a>
            </form>
        </section>
    </main>
</body>
</html>