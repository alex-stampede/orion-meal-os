<?php

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';

$stmt = $pdo->query("SELECT * FROM meal_plans WHERE status = 'active' ORDER BY id DESC");
$plans = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planes | Orion Meal OS</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <main class="shell">
        <section class="card page-card">
            <div class="page-top">
                <div>
                    <div class="badge">Planes</div>
                    <h1>Elige tu plan</h1>
                    <p>Selecciona el plan que mejor se adapte a tus comidas semanales.</p>
                </div>
            </div>

            <div class="grid">
                <?php foreach ($plans as $p): ?>
                    <div class="mini-card">
                        <span class="label"><?= htmlspecialchars($p['name']) ?></span>
                        <strong>$<?= number_format((float)$p['price'], 2) ?></strong>
                        <p><?= (int)$p['meals_per_week'] ?> comidas por semana</p>
                        <p><?= (int)$p['duration_weeks'] ?> semana(s)</p>
                        <p><?= htmlspecialchars((string)$p['description']) ?></p>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a class="button" href="/app/choose-plan.php?plan_id=<?= (int)$p['id'] ?>">Elegir plan</a>
                        <?php else: ?>
                            <a class="button" href="/app/register.php">Crear cuenta</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</body>
</html>