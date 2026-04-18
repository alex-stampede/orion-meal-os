<?php
require __DIR__ . '/../admin/partials/auth.php';

$userId = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
SELECT s.*, mp.name 
FROM subscriptions s
JOIN meal_plans mp ON mp.id = s.meal_plan_id
WHERE s.user_id = ? AND s.status = 'active'
LIMIT 1
");
$stmt->execute([$userId]);
$sub = $stmt->fetch();

require __DIR__ . '/../admin/partials/header.php';
?>

<h1>Tu plan</h1>

<?php if ($sub): ?>
<p><?= $sub['name'] ?></p>
<a href="/app/select-meals.php">Seleccionar comidas</a>
<?php else: ?>
<p>No tienes plan activo</p>
<?php endif; ?>