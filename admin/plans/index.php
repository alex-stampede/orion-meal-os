<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Planes';

$stmt = $pdo->query("SELECT * FROM meal_plans ORDER BY id DESC");
$plans = $stmt->fetchAll();

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <div class="page-top">
        <div>
            <div class="badge">Gestión</div>
            <h1>Planes</h1>
            <p>Administra los planes disponibles del sistema.</p>
        </div>
        <a class="button" href="/admin/plans/create.php">Nuevo plan</a>
    </div>

    <?php if (isset($_GET['created'])): ?>
        <div class="message-success">Plan creado correctamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <div class="message-success">Plan actualizado correctamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="message-success">Plan eliminado correctamente.</div>
    <?php endif; ?>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Comidas/semana</th>
                    <th>Duración</th>
                    <th>Precio</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$plans): ?>
                <tr>
                    <td colspan="7">Aún no hay planes registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($plans as $plan): ?>
                    <tr>
                        <td><?= (int)$plan['id'] ?></td>
                        <td><?= htmlspecialchars($plan['name']) ?></td>
                        <td><?= (int)$plan['meals_per_week'] ?></td>
                        <td><?= (int)$plan['duration_weeks'] ?> semana(s)</td>
                        <td>$<?= number_format((float)$plan['price'], 2) ?></td>
                        <td>
                            <span class="status-pill"><?= htmlspecialchars($plan['status']) ?></span>
                        </td>
                        <td>
                            <div class="actions-row">
                                <a class="button-secondary" href="/admin/plans/edit.php?id=<?= (int)$plan['id'] ?>">Editar</a>
                                <a class="button-danger" href="/admin/plans/delete.php?id=<?= (int)$plan['id'] ?>" onclick="return confirm('¿Eliminar este plan?');">Eliminar</a>
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