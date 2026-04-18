<?php

declare(strict_types=1);

require __DIR__ . '/partials/auth.php';

$pageTitle = 'Mi cuenta';

/* ===== SUSCRIPCIÓN ACTIVA ===== */
$stmt = $pdo->prepare("
    SELECT s.*, mp.name, mp.meals_per_week
    FROM subscriptions s
    JOIN meal_plans mp ON mp.id = s.meal_plan_id
    WHERE s.user_id = ? AND s.status = 'active'
    ORDER BY s.id DESC
    LIMIT 1
");
$stmt->execute([$userId]);
$sub = $stmt->fetch();

/* ===== DIRECCIÓN ===== */
$addressStmt = $pdo->prepare("
    SELECT *
    FROM customer_addresses
    WHERE user_id = ? AND is_default = 1
    ORDER BY id DESC
    LIMIT 1
");
$addressStmt->execute([$userId]);
$address = $addressStmt->fetch();

/* ===== MENÚ ACTIVO ===== */
$menuStmt = $pdo->query("
    SELECT *
    FROM weekly_menus
    WHERE status = 'published'
    ORDER BY week_start DESC
    LIMIT 1
");
$menu = $menuStmt->fetch();

/* ===== RESUMEN POR DÍA ===== */
$days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
$labels = [
    'monday'=>'Lunes','tuesday'=>'Martes','wednesday'=>'Miércoles',
    'thursday'=>'Jueves','friday'=>'Viernes','saturday'=>'Sábado','sunday'=>'Domingo'
];

$summary = [];

foreach ($days as $d) {
    $summary[$d] = [
        'items'=>[],
        'calories'=>0,
        'protein'=>0,
        'carbs'=>0,
        'fats'=>0
    ];
}

if ($sub) {
    $sel = $pdo->prepare("
        SELECT mi.*
        FROM meal_selections ms
        JOIN menu_items mi ON mi.id = ms.menu_item_id
        WHERE ms.subscription_id = ?
    ");
    $sel->execute([$sub['id']]);

    foreach ($sel->fetchAll() as $item) {
        $d = $item['day_of_week'];

        $summary[$d]['items'][] = $item;

        $summary[$d]['calories'] += (int)($item['calories'] ?? 0);
        $summary[$d]['protein'] += (float)($item['protein_g'] ?? 0);
        $summary[$d]['carbs'] += (float)($item['carbs_g'] ?? 0);
        $summary[$d]['fats'] += (float)($item['fats_g'] ?? 0);
    }
}

require __DIR__ . '/partials/header.php';
?>

<section class="card page-card">

<div class="customer-topbar">
    <div>
        <div class="badge">Cliente</div>
        <h1>Hola, <?= htmlspecialchars($userName) ?></h1>

        <div class="customer-nav">
            <a class="button-secondary" href="/app/dashboard.php">Mi cuenta</a>
            <a class="button-secondary" href="/app/select-meals.php">Seleccionar comidas</a>
            <a class="button-secondary" href="/app/address.php">Mi dirección</a>
        </div>
    </div>

    <a class="button-secondary" href="/logout.php">Cerrar sesión</a>
</div>

<?php if ($sub): ?>

<!-- PLAN -->
<div class="customer-grid">
    <div class="mini-card">
        <span class="label">Plan</span>
        <strong><?= $sub['name'] ?></strong>
    </div>

    <div class="mini-card">
        <span class="label">Comidas</span>
        <strong><?= $sub['meals_per_week'] ?></strong>
    </div>

    <div class="mini-card">
        <span class="label">Vigencia</span>
        <strong><?= $sub['start_date'] ?> → <?= $sub['end_date'] ?></strong>
    </div>
</div>

<!-- DIRECCIÓN + MENÚ -->
<section style="margin-top:24px;">
<div class="customer-grid">

<div class="mini-card">
<span class="label">Dirección</span>

<?php if ($address): ?>
<strong><?= $address['street'] ?> <?= $address['ext_number'] ?></strong>
<p class="helper-text"><?= $address['city'] ?>, <?= $address['state'] ?></p>
<?php else: ?>
<p>No agregada</p>
<?php endif; ?>

<a class="button-secondary" href="/app/address.php">Editar</a>
</div>

<div class="mini-card">
<span class="label">Menú semanal</span>

<?php if ($menu): ?>
<strong><?= $menu['title'] ?></strong>

<?php if ($menu['selection_deadline']): ?>
<p>
Selecciona antes de:
<strong><?= date('d/m h:i A', strtotime($menu['selection_deadline'])) ?></strong>
</p>
<?php endif; ?>

<?php else: ?>
<p>No disponible</p>
<?php endif; ?>

</div>

</div>
</section>

<!-- RESUMEN -->
<section style="margin-top:30px;">
<h2>Tu semana</h2>

<?php foreach ($days as $d): ?>
<div class="card" style="margin-top:12px;">

<h3><?= $labels[$d] ?></h3>

<?php if (!$summary[$d]['items']): ?>
<p>No seleccionaste platillos</p>
<?php else: ?>

<ul>
<?php foreach ($summary[$d]['items'] as $i): ?>
<li><?= $i['name'] ?> (<?= $i['category'] ?>)</li>
<?php endforeach; ?>
</ul>

<div class="meal-meta">
<span><?= $summary[$d]['calories'] ?> kcal</span>
<span><?= $summary[$d]['protein'] ?>g proteína</span>
<span><?= $summary[$d]['carbs'] ?>g carbs</span>
<span><?= $summary[$d]['fats'] ?>g grasas</span>
</div>

<?php endif; ?>

</div>
<?php endforeach; ?>

</section>

<?php else: ?>

<p>No tienes plan activo</p>
<a class="button" href="/app/plans.php">Elegir plan</a>

<?php endif; ?>

</section>

<?php require __DIR__ . '/partials/footer.php'; ?>