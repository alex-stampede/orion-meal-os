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
        <p>Administra los clientes del sistema</p>
    </div>

    <a class="button" href="/admin/customers/create.php">Nuevo cliente</a>
</div>

<div class="table-wrap">

<table class="table">
<thead>
<tr>
<th>ID</th>
<th>Nombre</th>
<th>Email</th>
<th>Estatus</th>
<th>Acciones</th>
</tr>
</thead>

<tbody>

<?php if (!$customers): ?>
<tr><td colspan="5">Sin clientes</td></tr>
<?php else: ?>

<?php foreach ($customers as $c): ?>

<tr>
<td><?= $c['id'] ?></td>

<td>
<?= htmlspecialchars(trim(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? ''))) ?>
</td>

<td><?= htmlspecialchars($c['email']) ?></td>

<td>
<span class="status-pill"><?= $c['status'] ?></span>
</td>

<td>
<div class="actions-row">

<a class="button-secondary"
href="/admin/customers/view.php?id=<?= $c['id'] ?>">
Ver
</a>

<a class="button-secondary"
href="/admin/customers/edit.php?id=<?= $c['id'] ?>">
Editar
</a>

<a class="button-danger"
href="/admin/customers/delete.php?id=<?= $c['id'] ?>"
onclick="return confirm('¿Eliminar cliente?')">
Eliminar
</a>

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