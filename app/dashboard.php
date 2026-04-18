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

require __DIR__ . '/partials/header.php';
?>

<section class="card page-card">
    <div class="customer-topbar">
        <div>
            <div class="badge">Cliente</div>
            <h1>Hola, <?= htmlspecialchars($userName) ?></h1>
            <p class="helper-text">Aquí puedes revisar tu plan y seleccionar tus comidas.</p>

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