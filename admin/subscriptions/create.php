<?php
require __DIR__ . '/../partials/auth.php';

$userId = (int)$_GET['user_id'];

$plans = $pdo->query("SELECT * FROM meal_plans")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO subscriptions (user_id, meal_plan_id, start_date, end_date, status)
        VALUES (?, ?, ?, ?, 'active')
    ");

    $stmt->execute([
        $userId,
        $_POST['plan_id'],
        $_POST['start_date'],
        $_POST['end_date']
    ]);

    header('Location: /admin/customers/index.php');
    exit;
}

require __DIR__ . '/../partials/header.php';
?>

<h2>Asignar plan</h2>

<form method="POST">

<select name="plan_id">
<?php foreach ($plans as $p): ?>
<option value="<?= $p['id'] ?>">
<?= $p['name'] ?> - $<?= $p['price'] ?>
</option>
<?php endforeach; ?>
</select>

<input type="date" name="start_date">
<input type="date" name="end_date">

<button class="button">Asignar</button>

</form>