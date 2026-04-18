<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Editar cliente';
$error = '';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = ? AND role = 'customer'
    LIMIT 1
");
$stmt->execute([$id]);
$customer = $stmt->fetch();

if (!$customer) {
    header('Location: /admin/customers/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $password = $_POST['password'] ?? '';

    if ($firstName === '' || $email === '') {
        $error = 'Completa correctamente los campos obligatorios.';
    } else {
        $check = $pdo->prepare("
            SELECT id
            FROM users
            WHERE email = ? AND id != ?
            LIMIT 1
        ");
        $check->execute([$email, $id]);

        if ($check->fetch()) {
            $error = 'Ese correo ya está registrado por otro usuario.';
        } else {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_BCRYPT);

                $update = $pdo->prepare("
                    UPDATE users
                    SET first_name = ?, last_name = ?, email = ?, status = ?, password_hash = ?
                    WHERE id = ? AND role = 'customer'
                ");
                $update->execute([$firstName, $lastName ?: null, $email, $status, $hash, $id]);
            } else {
                $update = $pdo->prepare("
                    UPDATE users
                    SET first_name = ?, last_name = ?, email = ?, status = ?
                    WHERE id = ? AND role = 'customer'
                ");
                $update->execute([$firstName, $lastName ?: null, $email, $status, $id]);
            }

            header('Location: /admin/customers/index.php?updated=1');
            exit;
        }
    }
}

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <div class="page-top">
        <div>
            <div class="badge">Clientes</div>
            <h1>Editar cliente</h1>
            <p>Actualiza la información del cliente.</p>
        </div>
        <a class="button-secondary" href="/admin/customers/index.php">Volver</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="message-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-grid">
        <div class="form-group">
            <label>Nombre</label>
            <input class="input" type="text" name="first_name" value="<?= htmlspecialchars((string)$customer['first_name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Apellido</label>
            <input class="input" type="text" name="last_name" value="<?= htmlspecialchars((string)$customer['last_name']) ?>">
        </div>

        <div class="form-group full">
            <label>Correo</label>
            <input class="input" type="email" name="email" value="<?= htmlspecialchars((string)$customer['email']) ?>" required>
        </div>

        <div class="form-group">
            <label>Estatus</label>
            <select class="select" name="status">
                <option value="active" <?= $customer['status'] === 'active' ? 'selected' : '' ?>>Activo</option>
                <option value="inactive" <?= $customer['status'] === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>

        <div class="form-group">
            <label>Nueva contraseña</label>
            <input class="input" type="password" name="password" placeholder="Déjalo vacío si no cambiará">
        </div>

        <div class="actions-row">
            <button class="button" type="submit">Guardar cambios</button>
            <a class="button-secondary" href="/admin/customers/index.php">Cancelar</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>