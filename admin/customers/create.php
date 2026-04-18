<?php

declare(strict_types=1);

require __DIR__ . '/../partials/auth.php';

$pageTitle = 'Nuevo cliente';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($firstName === '' || $email === '' || $password === '') {
        $error = 'Completa correctamente los campos obligatorios.';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("
            INSERT INTO users (role, first_name, last_name, email, password_hash, status)
            VALUES ('customer', ?, ?, ?, ?, 'active')
        ");

        $stmt->execute([
            $firstName,
            $lastName ?: null,
            $email,
            $hash
        ]);

        header('Location: /admin/customers/index.php');
        exit;
    }
}

require __DIR__ . '/../partials/header.php';
?>

<section class="card page-card">
    <div class="page-top">
        <div>
            <div class="badge">Clientes</div>
            <h1>Nuevo cliente</h1>
            <p>Crea un nuevo cliente para asignarle planes y comidas.</p>
        </div>
        <a class="button-secondary" href="/admin/customers/index.php">Volver</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="message-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-grid">
        <div class="form-group">
            <label>Nombre</label>
            <input class="input" type="text" name="first_name" required>
        </div>

        <div class="form-group">
            <label>Apellido</label>
            <input class="input" type="text" name="last_name">
        </div>

        <div class="form-group full">
            <label>Correo</label>
            <input class="input" type="email" name="email" required>
        </div>

        <div class="form-group full">
            <label>Contraseña</label>
            <input class="input" type="password" name="password" required>
        </div>

        <div class="actions-row">
            <button class="button" type="submit">Crear cliente</button>
            <a class="button-secondary" href="/admin/customers/index.php">Cancelar</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>