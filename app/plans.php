<?php

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';

$pageTitle = 'Planes';

$stmt = $pdo->query("SELECT * FROM meal_plans WHERE status = 'active' ORDER BY id DESC");
$plans = $stmt->fetchAll();

$isCustomer = isset($_SESSION['role']) && $_SESSION['role'] === 'customer';

require __DIR__ . '/partials/header.php';
?>

<section class="card page-card">
    <div class="customer-topbar">
        <div>
            <div class="badge">Planes</div>
            <h1>Elige tu plan</h1>
            <p class="helper-text">Selecciona el plan que mejor se adapte a tus comidas semanales.</p>

            <?php if ($isCustomer): ?>
                <div class="customer-nav">
                    <a class="button-secondary" href="/app/dashboard.php">Mi cuenta</a>
                    <a class="button-secondary" href="/app/plans.php">Planes</a>
                    <a class="button-secondary" href="/app/select-meals.php">Seleccionar comidas</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($isCustomer): ?>
            <a class="button-secondary" href="/logout.php">Cerrar sesión</a>
        <?php else: ?>
            <a class="button-secondary" href="/login.php">Iniciar sesión</a>
        <?php endif; ?>
    </div>

    <div class="meals-grid">
        <?php foreach ($plans as $p): ?>
            <article class="card meal-card">
                <div class="badge">Plan</div>
                <h3><?= htmlspecialchars($p['name']) ?></h3>
                <p class="helper-text"><?= htmlspecialchars((string)$p['description']) ?></p>

                <div class="meal-meta">
                    <span><?= (int)$p['meals_per_week'] ?> comidas/semana</span>
                    <span><?= (int)$p['duration_weeks'] ?> semana(s)</span>
                </div>

                <p><strong>$<?= number_format((float)$p['price'], 2) ?></strong></p>

                <div class="actions-row">
                    <?php if ($isCustomer): ?>
                        <a class="button" href="/app/choose-plan.php?plan_id=<?= (int)$p['id'] ?>">Elegir plan</a>
                    <?php else: ?>
                        <a class="button" href="/app/register.php">Crear cuenta</a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>