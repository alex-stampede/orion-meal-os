<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Menús';

$stmt = $pdo->query("SELECT * FROM weekly_menus ORDER BY week_start DESC, id DESC");
$menus = $stmt->fetchAll();

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <div class="page-top">
        <div>
            <div class="badge">Gestión</div>
            <h1>Menús semanales</h1>
            <p>Administra las semanas activas del menú.</p>
        </div>
        <a class="button" href="/admin/menus/create.php">Nuevo menú</a>
    </div>

    <?php if (isset($_GET['created'])): ?>
        <div class="message-success">Menú creado correctamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <div class="message-success">Menú actualizado correctamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="message-success">Menú eliminado correctamente.</div>
    <?php endif; ?>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Semana inicio</th>
                    <th>Semana fin</th>
                    <th>Estatus</th>
                    <th>Platillos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$menus): ?>
                <tr>
                    <td colspan="7">Aún no hay menús registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($menus as $menu): ?>
                    <?php
                    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM menu_items WHERE weekly_menu_id = ?");
                    $countStmt->execute([(int)$menu['id']]);
                    $itemsCount = (int)$countStmt->fetchColumn();
                    ?>
                    <tr>
                        <td><?= (int)$menu['id'] ?></td>
                        <td><?= htmlspecialchars($menu['title']) ?></td>
                        <td><?= htmlspecialchars($menu['week_start']) ?></td>
                        <td><?= htmlspecialchars($menu['week_end']) ?></td>
                        <td>
    <span class="status-pill">
        <?= htmlspecialchars(strtoupper((string)$menu['status'])) ?>
    </span>
</td>
                        <td><?= $itemsCount ?></td>
                        <td>
                            <div class="actions-row">
                                <a class="button-secondary" href="/admin/menu-items/index.php?menu_id=<?= (int)$menu['id'] ?>">Ver platillos</a>
                                <a class="button-secondary" href="/admin/menus/edit.php?id=<?= (int)$menu['id'] ?>">Editar</a>
                                <a class="button-danger" href="/admin/menus/delete.php?id=<?= (int)$menu['id'] ?>" onclick="return confirm('¿Eliminar este menú y sus platillos?');">Eliminar</a>
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