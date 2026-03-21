<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Nuevo plan';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $mealsPerWeek = (int)($_POST['meals_per_week'] ?? 0);
    $durationWeeks = (int)($_POST['duration_weeks'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $status = $_POST['status'] ?? 'active';

    if ($name === '' || $mealsPerWeek <= 0 || $durationWeeks <= 0 || $price <= 0) {
        $error = 'Completa correctamente todos los campos obligatorios.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO meal_plans (name, description, meals_per_week, duration_weeks, price, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $description ?: null, $mealsPerWeek, $durationWeeks, $price, $status]);

        header('Location: /admin/plans/index.php?created=1');
        exit;
    }
}

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <div class="page-top">
        <div>
            <div class="badge">Planes</div>
            <h1>Nuevo plan</h1>
            <p>Crea un nuevo plan comercial para el sistema.</p>
        </div>
        <a class="button-secondary" href="/admin/plans/index.php">Volver</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="message-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-grid">
        <div class="form-group">
            <label>Nombre</label>
            <input class="input" type="text" name="name" required>
        </div>

        <div class="form-group">
            <label>Estatus</label>
            <select class="select" name="status">
                <option value="active">Activo</option>
                <option value="inactive">Inactivo</option>
            </select>
        </div>

        <div class="form-group">
            <label>Comidas por semana</label>
            <input class="input" type="number" name="meals_per_week" min="1" required>
        </div>

        <div class="form-group">
            <label>Duración (semanas)</label>
            <input class="input" type="number" name="duration_weeks" min="1" required>
        </div>

        <div class="form-group">
            <label>Precio</label>
            <input class="input" type="number" step="0.01" name="price" min="0.01" required>
        </div>

        <div class="form-group full">
            <label>Descripción</label>
            <textarea class="textarea" name="description"></textarea>
        </div>

        <div class="actions-row">
            <button class="button" type="submit">Guardar plan</button>
            <a class="button-secondary" href="/admin/plans/index.php">Cancelar</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>