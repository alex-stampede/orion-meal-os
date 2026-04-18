<?php
require __DIR__ . '/../admin/partials/auth.php';

$stmt = $pdo->query("SELECT * FROM meal_plans WHERE status = 'active'");
$plans = $stmt->fetchAll();
?>

<h1>Planes disponibles</h1>

<?php foreach ($plans as $p): ?>
<div>
    <h3><?= $p['name'] ?></h3>
    <p><?= $p['meals_per_week'] ?> comidas</p>
    <p>$<?= $p['price'] ?></p>

    <a href="/app/choose-plan.php?plan_id=<?= $p['id'] ?>">
        Elegir plan
    </a>
</div>
<?php endforeach; ?>