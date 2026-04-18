<?php

declare(strict_types=1);

require __DIR__ . '/partials/auth.php';

$pageTitle = 'Mi cuenta';

$stmt = $pdo->prepare("
    SELECT s.*, mp.name, mp.meals_per_week, mp.duration_weeks
    FROM subscriptions s
    JOIN meal_plans mp ON mp.id = s.meal_plan_id
    WHERE s.user_id = ? AND s.status = 'active'
    ORDER BY s.id DESC
    LIMIT 1
");
$stmt->execute([$userId]);
$sub = $stmt->fetch();

$daysOrder = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
$dayLabels = [
    'monday' => 'Lunes',
    'tuesday' => 'Martes',
    'wednesday' => 'Miércoles',
    'thursday' => 'Jueves',
    'friday' => 'Viernes',
    'saturday' => 'Sábado',
    'sunday' => 'Domingo',
];

$summaryByDay = [];
foreach ($daysOrder as $day) {
    $summaryByDay[$day] = [
        'items' => [],
        'calories' => 0,
        'protein_g' => 0,
        'carbs_g' => 0,
        'fats_g' => 0,
    ];
}

if ($sub) {
    $selStmt = $pdo->prepare("
        SELECT mi.*
        FROM meal_selections ms
        JOIN menu_items mi ON mi.id = ms.menu_item_id
        WHERE ms.subscription_id = ?
        ORDER BY FIELD(mi.day_of_week,'monday','tuesday','wednesday','thursday','friday','saturday','sunday'), mi.category, mi.id ASC
    ");
    $selStmt->execute([(int)$sub['id']]);
    $selectedItems = $selStmt->fetchAll();

    foreach ($selectedItems as $item) {
        $day = $item['day_of_week'];

        if (!isset($summaryByDay[$day])) {
            continue;
        }

        $summaryByDay[$day]['items'][] = $item;
        $summaryByDay[$day]['calories'] += (int)($item['calories'] ?? 0);
        $summaryByDay[$day]['protein_g'] += (float)($item['protein_g'] ?? 0);
        $summaryByDay[$day]['carbs_g'] += (float)($item['carbs_g'] ?? 0);
        $summaryByDay[$day]['fats_g'] += (float)($item['fats_g'] ?? 0);
    }
}

require __DIR__ . '/partials/header.php';
?>

<section class="card page-card">
    <div class="customer-topbar">
        <div>
            <div class="badge">Cliente</div>
            <h1>Hola, <?= htmlspecialchars($userName) ?></h1>
            <p class="helper-text">Aquí puedes revisar tu plan y tus comidas seleccionadas.</p>

            <div class="customer-nav">
                <a class="button-secondary" href="/app/dashboard.php">Mi cuenta</a>
                <a class="button-secondary" href="/app/plans.php">Planes</a>
                <a class="button-secondary" href="/app/select-meals.php">Seleccionar comidas</a>
            </div>
        </div>

        <a class="button-secondary" href="/logout.php">Cerrar sesión</a>
    </div>

    <?php if ($sub): ?>
        <div class="customer-grid">
            <div class="mini-card">
                <span class="label">Plan activo</span>
                <strong><?= htmlspecialchars($sub['name']) ?></strong>
            </div>
            <div class="mini-card">
                <span class="label">Comidas por semana</span>
                <strong><?= (int)$sub['meals_per_week'] ?></strong>
            </div>
            <div class="mini-card">
                <span class="label">Vigencia</span>
                <strong><?= htmlspecialchars($sub['start_date']) ?> a <?= htmlspecialchars($sub['end_date']) ?></strong>
            </div>
        </div>

        <div class="actions-row" style="margin-top:24px;">
            <a class="button" href="/app/select-meals.php">Seleccionar comidas</a>
        </div>

        <section style="margin-top:32px;">
            <div class="page-top">
                <div>
                    <div class="badge">Resumen semanal</div>
                    <h2 style="margin:8px 0 0;">Tus platillos por día</h2>
                </div>
            </div>

            <?php foreach ($daysOrder as $day): ?>
                <?php $dayData = $summaryByDay[$day]; ?>
                <article class="card meal-card" style="margin-top:18px;">
                    <h3 style="margin-top:0;"><?= htmlspecialchars($dayLabels[$day]) ?></h3>

                    <?php if (!$dayData['items']): ?>
                        <p class="helper-text">No seleccionaste platillos para este día.</p>
                    <?php else: ?>
                        <ul style="margin:0 0 16px 18px; padding:0;">
                            <?php foreach ($dayData['items'] as $item): ?>
                                <li style="margin-bottom:8px;">
                                    <strong><?= htmlspecialchars($item['name']) ?></strong>
                                    <span class="helper-text"> · <?= htmlspecialchars($item['category']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="meal-meta">
                            <span><?= (int)$dayData['calories'] ?> kcal</span>
                            <span><?= number_format($dayData['protein_g'], 2) ?> g proteína</span>
                            <span><?= number_format($dayData['carbs_g'], 2) ?> g carbs</span>
                            <span><?= number_format($dayData['fats_g'], 2) ?> g grasas</span>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    <?php else: ?>
        <div class="empty-state">
            <p>No tienes un plan activo todavía.</p>
            <div class="actions-row">
                <a class="button" href="/app/plans.php">Elegir plan</a>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>