<?php

declare(strict_types=1);

require __DIR__ . '/partials/auth.php';

$pageTitle = 'Seleccionar comidas';

$subStmt = $pdo->prepare("
    SELECT s.*, mp.name AS plan_name, mp.meals_per_week
    FROM subscriptions s
    JOIN meal_plans mp ON mp.id = s.meal_plan_id
    WHERE s.user_id = ? AND s.status = 'active'
    ORDER BY s.id DESC
    LIMIT 1
");
$subStmt->execute([$userId]);
$subscription = $subStmt->fetch();

if (!$subscription) {
    header('Location: /app/plans.php');
    exit;
}

$menuStmt = $pdo->query("
    SELECT *
    FROM weekly_menus
    WHERE status = 'published'
    ORDER BY week_start DESC, id DESC
    LIMIT 1
");
$menu = $menuStmt->fetch();

$selectedCount = 0;
$selectedItemIds = [];

if ($menu) {
    $countStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM meal_selections
        WHERE subscription_id = ?
    ");
    $countStmt->execute([(int)$subscription['id']]);
    $selectedCount = (int)$countStmt->fetchColumn();

    $selectedStmt = $pdo->prepare("
        SELECT menu_item_id
        FROM meal_selections
        WHERE subscription_id = ?
    ");
    $selectedStmt->execute([(int)$subscription['id']]);
    $selectedItemIds = array_map('intval', array_column($selectedStmt->fetchAll(), 'menu_item_id'));
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $menu) {
    $menuItemId = (int)($_POST['menu_item_id'] ?? 0);

    if ($selectedCount >= (int)$subscription['meals_per_week']) {
        $error = 'Ya alcanzaste el límite de comidas de tu plan.';
    } elseif (in_array($menuItemId, $selectedItemIds, true)) {
        $error = 'Ese platillo ya fue seleccionado.';
    } else {
        $itemStmt = $pdo->prepare("
            SELECT * FROM menu_items
            WHERE id = ? AND weekly_menu_id = ? AND status = 'active'
            LIMIT 1
        ");
        $itemStmt->execute([$menuItemId, (int)$menu['id']]);
        $item = $itemStmt->fetch();

        if ($item) {
            $insert = $pdo->prepare("
                INSERT INTO meal_selections (subscription_id, menu_item_id, delivery_date)
                VALUES (?, ?, ?)
            ");
            $insert->execute([
                (int)$subscription['id'],
                $menuItemId,
                date('Y-m-d')
            ]);

            header('Location: /app/select-meals.php?selected=1');
            exit;
        } else {
            $error = 'No se encontró el platillo seleccionado.';
        }
    }
}

if (isset($_GET['selected'])) {
    $message = 'Platillo agregado correctamente.';
}

$itemsByDay = [];

if ($menu) {
    $itemsStmt = $pdo->prepare("
        SELECT *
        FROM menu_items
        WHERE weekly_menu_id = ? AND status = 'active'
        ORDER BY FIELD(day_of_week,'monday','tuesday','wednesday','thursday','friday','saturday','sunday'), category, id DESC
    ");
    $itemsStmt->execute([(int)$menu['id']]);
    $items = $itemsStmt->fetchAll();

    foreach ($items as $item) {
        $itemsByDay[$item['day_of_week']][] = $item;
    }
}

$dayLabels = [
    'monday' => 'Lunes',
    'tuesday' => 'Martes',
    'wednesday' => 'Miércoles',
    'thursday' => 'Jueves',
    'friday' => 'Viernes',
    'saturday' => 'Sábado',
    'sunday' => 'Domingo',
];

require __DIR__ . '/partials/header.php';
?>

<section class="card page-card">
    <div class="customer-topbar">
        <div>
            <div class="badge">Cliente</div>
            <h1>Selecciona tus comidas</h1>
            <p class="helper-text">
                Plan: <strong><?= htmlspecialchars($subscription['plan_name']) ?></strong> ·
                Seleccionadas: <strong><?= $selectedCount ?></strong> / <strong><?= (int)$subscription['meals_per_week'] ?></strong>
            </p>

            <div class="customer-nav">
                <a class="button-secondary" href="/app/dashboard.php">Mi cuenta</a>
                <a class="button-secondary" href="/app/plans.php">Planes</a>
                <a class="button-secondary" href="/app/select-meals.php">Seleccionar comidas</a>
            </div>
        </div>

        <a class="button-secondary" href="/logout.php">Cerrar sesión</a>
    </div>

    <?php if ($message !== ''): ?>
        <div class="message-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="message-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!$menu): ?>
        <div class="empty-state">
            <p>No hay un menú publicado en este momento.</p>
        </div>
    <?php else: ?>
        <div class="mini-card" style="margin-top: 18px;">
            <span class="label">Menú actual</span>
            <strong><?= htmlspecialchars($menu['title']) ?></strong>
            <p class="helper-text">
                Semana: <?= htmlspecialchars($menu['week_start']) ?> a <?= htmlspecialchars($menu['week_end']) ?>
            </p>
        </div>

        <?php if (!$itemsByDay): ?>
            <div class="empty-state">
                <p>Este menú todavía no tiene platillos activos.</p>
            </div>
        <?php else: ?>
            <?php foreach ($itemsByDay as $day => $dayItems): ?>
                <section class="day-section">
                    <h2 class="day-title"><?= htmlspecialchars($dayLabels[$day] ?? $day) ?></h2>

                    <div class="meals-grid">
                        <?php foreach ($dayItems as $item): ?>
                            <article class="card meal-card">
                                <div class="badge"><?= htmlspecialchars($item['category']) ?></div>
                                <h3><?= htmlspecialchars($item['name']) ?></h3>

                                <?php if (!empty($item['description'])): ?>
                                    <p class="helper-text"><?= htmlspecialchars($item['description']) ?></p>
                                <?php endif; ?>

                                <div class="meal-meta">
                                    <?php if ($item['calories'] !== null): ?>
                                        <span><?= (int)$item['calories'] ?> kcal</span>
                                    <?php endif; ?>
                                    <?php if ($item['protein_g'] !== null): ?>
                                        <span><?= htmlspecialchars((string)$item['protein_g']) ?> g proteína</span>
                                    <?php endif; ?>
                                    <?php if ($item['carbs_g'] !== null): ?>
                                        <span><?= htmlspecialchars((string)$item['carbs_g']) ?> g carbs</span>
                                    <?php endif; ?>
                                    <?php if ($item['fats_g'] !== null): ?>
                                        <span><?= htmlspecialchars((string)$item['fats_g']) ?> g grasas</span>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($item['ingredients'])): ?>
                                    <p class="helper-text"><strong>Ingredientes:</strong> <?= htmlspecialchars($item['ingredients']) ?></p>
                                <?php endif; ?>

                                <div class="actions-row">
                                    <?php if (in_array((int)$item['id'], $selectedItemIds, true)): ?>
                                        <span class="button-secondary" style="pointer-events:none; opacity:.7;">Ya seleccionado</span>
                                    <?php elseif ($selectedCount >= (int)$subscription['meals_per_week']): ?>
                                        <span class="button-secondary" style="pointer-events:none; opacity:.7;">Límite alcanzado</span>
                                    <?php else: ?>
                                        <form method="POST">
                                            <input type="hidden" name="menu_item_id" value="<?= (int)$item['id'] ?>">
                                            <button class="button" type="submit">Seleccionar</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>