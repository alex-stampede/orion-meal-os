<?php
require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Clientes';

$stmt = $pdo->query("SELECT * FROM users WHERE role = 'customer' ORDER BY id DESC");
$customers = $stmt->fetchAll();

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
<div class="page-top">
    <div>
        <div class="badge">Clientes</div>
        <h1>Clientes</h1>
        <p>Administra los clientes registrados y asígnales planes.</p>
    </div>
    <a class="button" href="/admin/customers/create.php">Nuevo cliente</a>
</div>

    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($customers as $c): ?>
            <tr>
                <td><?= $c['first_name'] . ' ' . $c['last_name'] ?></td>
                <td><?= $c['email'] ?></td>
                <td>
                    <a class="button-secondary" href="/admin/subscriptions/create.php?user_id=<?= $c['id'] ?>">Asignar plan</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
