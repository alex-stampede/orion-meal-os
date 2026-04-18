<?php
require __DIR__ . '/../admin/partials/auth.php';

$userId = $_SESSION['user_id'];
$planId = (int)$_GET['plan_id'];

$stmt = $pdo->prepare("SELECT * FROM meal_plans WHERE id = ?");
$stmt->execute([$planId]);
$plan = $stmt->fetch();

if (!$plan) {
    die("Plan no encontrado");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $start = date('Y-m-d');
    $end = date('Y-m-d', strtotime("+{$plan['duration_weeks']} weeks"));

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

<h2><?= $plan['name'] ?></h2>
<p>$<?= $plan['price'] ?></p>

<form method="POST">
<button>Confirmar plan</button>
</form>