<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Editar platillo';
$error = '';

$id = (int)($_GET['id'] ?? 0);
$menuId = (int)($_GET['menu_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ? AND weekly_menu_id = ? LIMIT 1");
$stmt->execute([$id, $menuId]);
$item = $stmt->fetch();

if (!$item) {
    header('Location: /admin/menus/index.php');
    exit;
}

$menuStmt = $pdo->prepare("SELECT * FROM weekly_menus WHERE id = ? LIMIT 1");
$menuStmt->execute([$menuId]);
$menu = $menuStmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dayOfWeek = $_POST['day_of_week'] ?? '';
    $category = $_POST['category'] ?? 'lunch';
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $calories = $_POST['calories'] !== '' ? (int)$_POST['calories'] : null;
    $protein = $_POST['protein_g'] !== '' ? (float)$_POST['protein_g'] : null;
    $carbs = $_POST['carbs_g'] !== '' ? (float)$_POST['carbs_g'] : null;
    $fats = $_POST['fats_g'] !== '' ? (float)$_POST['fats_g'] : null;
    $status = $_POST['status'] ?? 'active';

    if ($name === '' || $dayOfWeek === '') {
        $error = 'Completa correctamente los campos obligatorios.';
    } else {
        $update = $pdo->prepare("
            UPDATE menu_items
            SET day_of_week = ?, category = ?, name = ?, description = ?, ingredients = ?,
                calories = ?, protein_g = ?, carbs_g = ?, fats_g = ?, status = ?
            WHERE id = ? AND weekly_menu_id = ?
        ");
        $update->execute([
            $dayOfWeek, $category, $name, $description ?: null, $ingredients ?: null,
            $calories, $protein, $carbs, $fats, $status, $id, $menuId
        ]);

        header('Location: /admin/menu-items/index.php?menu_id=' . $menuId . '&updated=1');
        exit;
    }
}

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <div class="page-top">
        <div>
            <div class="badge">Platillos</div>
            <h1>Editar platillo</h1>
            <p><?= htmlspecialchars($menu['title'] ?? '') ?></p>
        </div>
        <a class="button-secondary" href="/admin/menu-items/index.php?menu_id=<?= (int)$menuId ?>">Volver</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="message-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-grid">
        <div class="form-group">
            <label>Día</label>
            <select class="select" name="day_of_week" required>
                <option value="monday" <?= $item['day_of_week'] === 'monday' ? 'selected' : '' ?>>Monday</option>
                <option value="tuesday" <?= $item['day_of_week'] === 'tuesday' ? 'selected' : '' ?>>Tuesday</option>
                <option value="wednesday" <?= $item['day_of_week'] === 'wednesday' ? 'selected' : '' ?>>Wednesday</option>
                <option value="thursday" <?= $item['day_of_week'] === 'thursday' ? 'selected' : '' ?>>Thursday</option>
                <option value="friday" <?= $item['day_of_week'] === 'friday' ? 'selected' : '' ?>>Friday</option>
                <option value="saturday" <?= $item['day_of_week'] === 'saturday' ? 'selected' : '' ?>>Saturday</option>
                <option value="sunday" <?= $item['day_of_week'] === 'sunday' ? 'selected' : '' ?>>Sunday</option>
            </select>
        </div>

        <div class="form-group">
            <label>Categoría</label>
            <select class="select" name="category">
                <option value="breakfast" <?= $item['category'] === 'breakfast' ? 'selected' : '' ?>>Breakfast</option>
                <option value="lunch" <?= $item['category'] === 'lunch' ? 'selected' : '' ?>>Lunch</option>
                <option value="dinner" <?= $item['category'] === 'dinner' ? 'selected' : '' ?>>Dinner</option>
                <option value="snack" <?= $item['category'] === 'snack' ? 'selected' : '' ?>>Snack</option>
            </select>
        </div>

        <div class="form-group full">
            <label>Nombre</label>
            <input class="input" type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required>
        </div>

        <div class="form-group full">
            <label>Descripción</label>
            <textarea class="textarea" name="description"><?= htmlspecialchars((string)$item['description']) ?></textarea>
        </div>

        <div class="form-group full">
            <label>Ingredientes</label>
            <textarea class="textarea" name="ingredients"><?= htmlspecialchars((string)$item['ingredients']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Calorías</label>
            <input class="input" type="number" name="calories" min="0" value="<?= htmlspecialchars((string)($item['calories'] ?? '')) ?>">
        </div>

        <div class="form-group">
            <label>Proteína (g)</label>
            <input class="input" type="number" step="0.01" name="protein_g" min="0" value="<?= htmlspecialchars((string)($item['protein_g'] ?? '')) ?>">
        </div>

        <div class="form-group">
            <label>Carbs (g)</label>
            <input class="input" type="number" step="0.01" name="carbs_g" min="0" value="<?= htmlspecialchars((string)($item['carbs_g'] ?? '')) ?>">
        </div>

        <div class="form-group">
            <label>Grasas (g)</label>
            <input class="input" type="number" step="0.01" name="fats_g" min="0" value="<?= htmlspecialchars((string)($item['fats_g'] ?? '')) ?>">
        </div>

        <div class="form-group">
            <label>Estatus</label>
            <select class="select" name="status">
                <option value="active" <?= $item['status'] === 'active' ? 'selected' : '' ?>>Activo</option>
                <option value="inactive" <?= $item['status'] === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>

        <div class="actions-row">
            <button class="button" type="submit">Actualizar platillo</button>
            <a class="button-secondary" href="/admin/menu-items/index.php?menu_id=<?= (int)$menuId ?>">Cancelar</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>