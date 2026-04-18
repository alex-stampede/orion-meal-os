<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Nuevo platillo';
$error = '';

$menuId = (int)($_GET['menu_id'] ?? 0);

$menuStmt = $pdo->prepare("SELECT * FROM weekly_menus WHERE id = ? LIMIT 1");
$menuStmt->execute([$menuId]);
$menu = $menuStmt->fetch();

if (!$menu) {
    header('Location: /admin/menus/index.php');
    exit;
}

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
        $stmt = $pdo->prepare("
            INSERT INTO menu_items (
                weekly_menu_id, day_of_week, category, name, description, ingredients,
                calories, protein_g, carbs_g, fats_g, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $menuId, $dayOfWeek, $category, $name, $description ?: null, $ingredients ?: null,
            $calories, $protein, $carbs, $fats, $status
        ]);

        header('Location: /admin/menu-items/index.php?menu_id=' . $menuId . '&created=1');
        exit;
    }
}

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <div class="page-top">
        <div>
            <div class="badge">Platillos</div>
            <h1>Nuevo platillo</h1>
            <p><?= htmlspecialchars($menu['title']) ?></p>
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
                <option value="monday">Monday</option>
                <option value="tuesday">Tuesday</option>
                <option value="wednesday">Wednesday</option>
                <option value="thursday">Thursday</option>
                <option value="friday">Friday</option>
                <option value="saturday">Saturday</option>
                <option value="sunday">Sunday</option>
            </select>
        </div>

        <div class="form-group">
            <label>Categoría</label>
            <select class="select" name="category">
                <option value="breakfast">Breakfast</option>
                <option value="lunch" selected>Lunch</option>
                <option value="dinner">Dinner</option>
                <option value="snack">Snack</option>
            </select>
        </div>

        <div class="form-group full">
            <label>Nombre</label>
            <input class="input" type="text" name="name" required>
        </div>

        <div class="form-group full">
            <label>Descripción</label>
            <textarea class="textarea" name="description"></textarea>
        </div>

        <div class="form-group full">
            <label>Ingredientes</label>
            <textarea class="textarea" name="ingredients"></textarea>
        </div>

        <div class="form-group">
            <label>Calorías</label>
            <input class="input" type="number" name="calories" min="0">
        </div>

        <div class="form-group">
            <label>Proteína (g)</label>
            <input class="input" type="number" step="0.01" name="protein_g" min="0">
        </div>

        <div class="form-group">
            <label>Carbs (g)</label>
            <input class="input" type="number" step="0.01" name="carbs_g" min="0">
        </div>

        <div class="form-group">
            <label>Grasas (g)</label>
            <input class="input" type="number" step="0.01" name="fats_g" min="0">
        </div>

        <div class="form-group">
            <label>Estatus</label>
            <select class="select" name="status">
                <option value="active">Activo</option>
                <option value="inactive">Inactivo</option>
            </select>
        </div>

        <div class="actions-row">
            <button class="button" type="submit">Guardar platillo</button>
            <a class="button-secondary" href="/admin/menu-items/index.php?menu_id=<?= (int)$menuId ?>">Cancelar</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>