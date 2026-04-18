<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Platillos';

$menuId = (int)($_GET['menu_id'] ?? 0);

$menuStmt = $pdo->prepare("SELECT * FROM weekly_menus WHERE id = ? LIMIT 1");
$menuStmt->execute([$menuId]);
$menu = $menuStmt->fetch();

if (!$menu) {
    header('Location: /admin/menus/index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM menu_items WHERE weekly_menu_id = ? ORDER BY FIELD(day_of_week,'monday','tuesday','wednesday','thursday','friday','saturday','sunday'), category, id DESC");
$stmt->execute([$menuId]);
$items = $stmt->fetchAll();

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <div class="page-top">
        <div>
            <div class="badge">Platillos</div>
            <h1><?= htmlspecialchars($menu['title']) ?></h1>
            <p>Administra los platillos de esta semana.</p>
        </div>
        <div class="actions-row">
            <a class="button-secondary" href="/admin/menus/index.php">Volver a menús</a>
            <a class="button" href="/admin/menu-items/create.php?menu_id=<?= (int)$menuId ?>">Nuevo platillo</a>
        </div>
    </div>

    <?php if (isset($_GET['created'])): ?>
        <div class="message-success">Platillo creado correctamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <div class="message-success">Platillo actualizado correctamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="message-success">Platillo eliminado correctamente.</div>
    <?php endif; ?>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Día</th>
                    <th>Categoría</th>
                    <th>Nombre</th>
                    <th>Calorías</th>
                    <th>Proteína</th>
                    <th>Carbs</th>
                    <th>Grasas</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$items): ?>
                <tr>
                    <td colspan="9">Aún no hay platillos registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['day_of_week']) ?></td>
                        <td><?= htmlspecialchars($item['category']) ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars((string)($item['calories'] ?? '—')) ?></td>
                        <td><?= htmlspecialchars((string)($item['protein_g'] ?? '—')) ?></td>
                        <td><?= htmlspecialchars((string)($item['carbs_g'] ?? '—')) ?></td>
                        <td><?= htmlspecialchars((string)($item['fats_g'] ?? '—')) ?></td>
                        <td><span class="status-pill"><?= htmlspecialchars($item['status']) ?></span></td>
                        <td>
                            <div class="actions-row">
                                <a class="button-secondary" href="/admin/menu-items/edit.php?id=<?= (int)$item['id'] ?>&menu_id=<?= (int)$menuId ?>">Editar</a>
                                <a class="button-danger" href="/admin/menu-items/delete.php?id=<?= (int)$item['id'] ?>&menu_id=<?= (int)$menuId ?>" onclick="return confirm('¿Eliminar este platillo?');">Eliminar</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>