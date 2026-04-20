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
$days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
$labels = [
    'monday' => 'Lunes',
    'tuesday' => 'Martes',
    'wednesday' => 'Miércoles',
    'thursday' => 'Jueves',
    'friday' => 'Viernes',
    'saturday' => 'Sábado',
    'sunday' => 'Domingo',
];

$summary = [];

foreach ($days as $d) {
    $summary[$d] = [
        'items' => [],
        'calories' => 0,
        'protein' => 0,
        'carbs' => 0,
        'fats' => 0,
    ];
}

if ($sub) {
    $sel = $pdo->prepare("
        SELECT ms.id AS selection_id, mi.*
        FROM meal_selections ms
        JOIN menu_items mi ON mi.id = ms.menu_item_id
        WHERE ms.subscription_id = ?
        ORDER BY FIELD(mi.day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday'), mi.category, mi.id
    ");
    $sel->execute([$sub['id']]);

    foreach ($sel->fetchAll() as $item) {
        $d = $item['day_of_week'];

        if (!isset($summary[$d])) {
            continue;
        }

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
                <a class="button-secondary" href="/app/plans.php">Planes</a>
                <a class="button-secondary" href="/app/select-meals.php">Seleccionar comidas</a>
                <a class="button-secondary" href="/app/address.php">Mi dirección</a>
            </div>
        </div>

        <a class="button-secondary" href="/logout.php">Cerrar sesión</a>
    </div>

    <?php if ($sub): ?>

        <div class="customer-grid">
            <div class="mini-card">
                <span class="label">Plan</span>
                <strong><?= htmlspecialchars($sub['name']) ?></strong>
            </div>

            <div class="mini-card">
                <span class="label">Comidas</span>
                <strong><?= (int)$sub['meals_per_week'] ?></strong>
            </div>

            <div class="mini-card">
                <span class="label">Vigencia</span>
                <strong><?= htmlspecialchars($sub['start_date']) ?> → <?= htmlspecialchars($sub['end_date']) ?></strong>
            </div>
        </div>

        <section style="margin-top: 24px;">
            <div class="customer-grid">

                <div class="mini-card">
                    <span class="label">Dirección</span>

                    <?php if ($address): ?>
                        <strong>
                            <?= htmlspecialchars((string)$address['street']) ?>
                            <?= htmlspecialchars((string)$address['ext_number']) ?>
                        </strong>
                        <p class="helper-text">
                            <?= htmlspecialchars((string)$address['city']) ?>,
                            <?= htmlspecialchars((string)$address['state']) ?>
                        </p>
                    <?php else: ?>
                        <p class="helper-text">No agregada</p>
                    <?php endif; ?>

                    <a class="button-secondary" href="/app/address.php">Editar</a>
                </div>

                <div class="mini-card">
                    <span class="label">Menú semanal</span>

                    <?php if ($menu): ?>
                        <strong><?= htmlspecialchars((string)$menu['title']) ?></strong>

                        <?php if (!empty($menu['selection_deadline'])): ?>
                            <p class="helper-text">
                                Selecciona antes de:
                                <strong><?= htmlspecialchars(date('d/m h:i A', strtotime((string)$menu['selection_deadline']))) ?></strong>
                            </p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="helper-text">No disponible</p>
                    <?php endif; ?>
                </div>

            </div>
        </section>

        <section class="week-section">
            <h2>Tu semana</h2>

            <div class="week-list">
                <?php foreach ($days as $d): ?>
                    <div class="card day-card">
                        <h3 class="day-title"><?= htmlspecialchars($labels[$d]) ?></h3>

                        <?php if (!$summary[$d]['items']): ?>
                            <p class="helper-text">No seleccionaste platillos</p>
                        <?php else: ?>
                            <ul>
                                <?php foreach ($summary[$d]['items'] as $i): ?>
                                    <li>
                                        <?= htmlspecialchars((string)$i['name']) ?>
                                        (<?= htmlspecialchars((string)$i['category']) ?>)
                                        <a class="button-secondary" style="margin-left:10px; padding:4px 10px; font-size:12px;" href="/app/remove-selection.php?id=<?= (int)$i['selection_id'] ?>">Quitar</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="meal-meta">
                                <span><?= (int)$summary[$d]['calories'] ?> kcal</span>
                                <span><?= number_format((float)$summary[$d]['protein'], 2) ?>g proteína</span>
                                <span><?= number_format((float)$summary[$d]['carbs'], 2) ?>g carbs</span>
                                <span><?= number_format((float)$summary[$d]['fats'], 2) ?>g grasas</span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

    <?php else: ?>

        <div class="empty-state">
            <p>No tienes plan activo</p>
            <a class="button" href="/app/plans.php">Elegir plan</a>
        </div>

    <?php endif; ?>

</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
