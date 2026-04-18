<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Editar menú';
$error = '';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM weekly_menus WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$menu = $stmt->fetch();

if (!$menu) {
    header('Location: /admin/menus/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $weekStart = $_POST['week_start'] ?? '';
    $weekEnd = $_POST['week_end'] ?? '';
    $status = $_POST['status'] ?? 'draft';

    if ($title === '' || $weekStart === '' || $weekEnd === '') {
        $error = 'Completa correctamente todos los campos obligatorios.';
    } else {
        $update = $pdo->prepare("
            UPDATE weekly_menus
            SET title = ?, week_start = ?, week_end = ?, status = ?
            WHERE id = ?
        ");
        $update->execute([$title, $weekStart, $weekEnd, $status, $id]);

        header('Location: /admin/menus/index.php?updated=1');
        exit;
    }
}

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <div class="page-top">
        <div>
            <div class="badge">Menús</div>
            <h1>Editar menú</h1>
            <p>Actualiza la semana seleccionada.</p>
        </div>
        <a class="button-secondary" href="/admin/menus/index.php">Volver</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="message-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-grid">
        <div class="form-group full">
            <label>Título</label>
            <input class="input" type="text" name="title" value="<?= htmlspecialchars($menu['title']) ?>" required>
        </div>

        <div class="form-group">
            <label>Semana inicio</label>
            <input class="input" type="date" name="week_start" value="<?= htmlspecialchars($menu['week_start']) ?>" required>
        </div>

        <div class="form-group">
            <label>Semana fin</label>
            <input class="input" type="date" name="week_end" value="<?= htmlspecialchars($menu['week_end']) ?>" required>
        </div>

        <div class="form-group">
            <label>Estatus</label>
            <select class="select" name="status">
                <option value="draft" <?= $menu['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= $menu['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="archived" <?= $menu['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
            </select>
        </div>

        <div class="actions-row">
            <button class="button" type="submit">Actualizar menú</button>
            <a class="button-secondary" href="/admin/menus/index.php">Cancelar</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>