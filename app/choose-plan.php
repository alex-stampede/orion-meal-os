<?php

declare(strict_types=1);

require __DIR__ . '/partials/auth.php';

$pageTitle = 'Confirmar plan';

$planId = (int)($_GET['plan_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM meal_plans WHERE id = ? AND status = 'active' LIMIT 1");
$stmt->execute([$planId]);
$plan = $stmt->fetch();

if (!$plan) {
    die('Plan no encontrado');
}

$activeSubStmt = $pdo->prepare("
    SELECT id
    FROM subscriptions
    WHERE user_id = ? AND status = 'active'
    ORDER BY id DESC
    LIMIT 1
");
$activeSubStmt->execute([$userId]);
$activeSubscription = $activeSubStmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($activeSubscription) {
        $disable = $pdo->prepare("
            UPDATE subscriptions
            SET status = 'cancelled'
            WHERE id = ?
        ");
        $disable->execute([(int)$activeSubscription['id']]);
    }

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

require __DIR__ . '/partials/header.php';
?>

<section class="card page-card" style="max-width: 700px; margin: 40px auto;">
    <div class="badge">Confirmar plan</div>
    <h1><?= htmlspecialchars($plan['name']) ?></h1>
    <p><strong>$<?= number_format((float)$plan['price'], 2) ?></strong></p>
    <p class="helper-text"><?= htmlspecialchars((string)$plan['description']) ?></p>

    <div class="meal-meta">
        <span><?= (int)$plan['meals_per_week'] ?> comidas por semana</span>
        <span><?= (int)$plan['duration_weeks'] ?> semana(s)</span>
    </div>

    <form method="POST" class="actions-row">
        <button class="button" type="submit">Confirmar plan</button>
        <a class="button-secondary" href="/app/plans.php">Volver</a>
    </form>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
