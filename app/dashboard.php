<?php
require __DIR__ . '/../admin/partials/auth.php';

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
SELECT s.*, mp.name
FROM subscriptions s
JOIN meal_plans mp ON mp.id = s.meal_plan_id
WHERE s.user_id = ? AND s.status = 'active'
LIMIT 1
");
$stmt->execute([$userId]);
$sub = $stmt->fetch();
?>

<h1>Mi cuenta</h1>

<?php if ($sub): ?>
<p>Plan: <?= $sub['name'] ?></p>
<p>Vigencia: <?= $sub['start_date'] ?> - <?= $sub['end_date'] ?></p>

<a href="/app/select-meals.php">
Seleccionar comidas
</a>

<?php else: ?>
<p>No tienes plan activo</p>

<a href="/app/plans.php">Ver planes</a>
<?php endif; ?>