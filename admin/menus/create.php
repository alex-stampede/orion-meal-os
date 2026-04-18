<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Nuevo menú';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $weekStart = $_POST['week_start'] ?? '';
    $weekEnd = $_POST['week_end'] ?? '';
    $status = $_POST['status'] ?? 'draft';

    if ($title === '' || $weekStart === '' || $weekEnd === '') {
        $error = 'Completa correctamente todos los campos obligatorios.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO weekly_menus (title, week_start, week_end, status)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$title, $weekStart, $weekEnd, $status]);

        header('Location: /admin/menus/index.php?created=1');
        exit;
    }
}

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <div class="page-top">
        <div>
            <div class="badge">Menús</div>
            <h1>Nuevo menú semanal</h1>
            <p>Crea una nueva semana de menú.</p>
        </div>
        <a class="button-secondary" href="/admin/menus/index.php">Volver</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="message-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-grid">
        <div class="form-group full">
            <label>Título</label>
            <input class="input" type="text" name="title" required>
        </div>

        <div class="form-group">
            <label>Semana inicio</label>
            <input class="input" type="date" name="week_start" required>
        </div>

        <div class="form-group">
            <label>Semana fin</label>
            <input class="input" type="date" name="week_end" required>
        </div>

        <div class="form-group">
            <label>Estatus</label>
            <select class="select" name="status">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </select>
        </div>

        <div class="actions-row">
            <button class="button" type="submit">Guardar menú</button>
            <a class="button-secondary" href="/admin/menus/index.php">Cancelar</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>