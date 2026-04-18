<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Clientes';

$stmt = $pdo->query("
    SELECT *
    FROM users
    WHERE role = 'customer'
    ORDER BY id DESC
");
$customers = $stmt->fetchAll();

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <div class="page-top">
        <div>
            <div class="badge">Clientes</div>
            <h1>Clientes</h1>
            <p>Administra los clientes registrados y sus cuentas.</p>
        </div>
        <a class="button" href="/admin/customers/create.php">Nuevo cliente</a>
    </div>

    <?php if (isset($_GET['created'])): ?>
        <div class="message-success">Cliente creado correctamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <div class="message-success">Cliente actualizado correctamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="message-success">Cliente eliminado correctamente.</div>
    <?php endif; ?>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$customers): ?>
                <tr>
                    <td colspan="5">Aún no hay clientes registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($customers as $c): ?>
                    <tr>
                        <td><?= (int)$c['id'] ?></td>
                        <td><?= htmlspecialchars(trim(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? ''))) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><span class="status-pill"><?= htmlspecialchars($c['status']) ?></span></td>
                        <td>
                            <div class="actions-row">
                                <a class="button-secondary" href="/admin/subscriptions/create.php?user_id=<?= (int)$c['id'] ?>">Asignar plan</a>
                                <a class="button-secondary" href="/admin/customers/edit.php?id=<?= (int)$c['id'] ?>">Editar</a>
                                <a class="button-danger" href="/admin/customers/delete.php?id=<?= (int)$c['id'] ?>" onclick="return confirm('¿Eliminar este cliente? Esto también puede afectar sus suscripciones y selecciones.');">Eliminar</a>
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
