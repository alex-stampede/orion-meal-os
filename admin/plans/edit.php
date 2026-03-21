<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Editar plan';
$error = '';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM meal_plans WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$plan = $stmt->fetch();

if (!$plan) {
    header('Location: /admin/plans/index.php');
    exit;
}

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
        $update = $pdo->prepare("
            UPDATE meal_plans
            SET name = ?, description = ?, meals_per_week = ?, duration_weeks = ?, price = ?, status = ?
            WHERE id = ?
        ");
        $update->execute([$name, $description ?: null, $mealsPerWeek, $durationWeeks, $price, $status, $id]);

        header('Location: /admin/plans/index.php?updated=1');
        exit;
    }
}

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <div class="page-top">
        <div>
            <div class="badge">Planes</div>
            <h1>Editar plan</h1>
            <p>Actualiza la información del plan seleccionado.</p>
        </div>
        <a class="button-secondary" href="/admin/plans/index.php">Volver</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="message-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-grid">
        <div class="form-group">
            <label>Nombre</label>
            <input class="input" type="text" name="name" value="<?= htmlspecialchars($plan['name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Estatus</label>
            <select class="select" name="status">
                <option value="active" <?= $plan['status'] === 'active' ? 'selected' : '' ?>>Activo</option>
                <option value="inactive" <?= $plan['status'] === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>

        <div class="form-group">
            <label>Comidas por semana</label>
            <input class="input" type="number" name="meals_per_week" min="1" value="<?= (int)$plan['meals_per_week'] ?>" required>
        </div>

        <div class="form-group">
            <label>Duración (semanas)</label>
            <input class="input" type="number" name="duration_weeks" min="1" value="<?= (int)$plan['duration_weeks'] ?>" required>
        </div>

        <div class="form-group">
            <label>Precio</label>
            <input class="input" type="number" step="0.01" name="price" min="0.01" value="<?= htmlspecialchars((string)$plan['price']) ?>" required>
        </div>

        <div class="form-group full">
            <label>Descripción</label>
            <textarea class="textarea" name="description"><?= htmlspecialchars((string)$plan['description']) ?></textarea>
        </div>

        <div class="actions-row">
            <button class="button" type="submit">Actualizar plan</button>
            <a class="button-secondary" href="/admin/plans/index.php">Cancelar</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>