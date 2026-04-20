<?php

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';

if (isset($_SESSION['user_id'], $_SESSION['role'])) {
    if ($_SESSION['role'] === 'customer') {
        header('Location: /app/dashboard.php');
        exit;
    }

    header('Location: /admin/dashboard.php');
    exit;
}

$pageTitle = 'Crear cuenta';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Completa todos los campos.';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = 'Ese correo ya está registrado.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("
                INSERT INTO users (role, first_name, email, password_hash, status)
                VALUES ('customer', ?, ?, ?, 'active')
            ");
            $stmt->execute([$name, $email, $hash]);

            header('Location: /login.php');
            exit;
        }
    }
}

require __DIR__ . '/partials/header.php';
?>

<section class="card page-card" style="max-width: 640px; margin: 40px auto;">
    <div class="page-top">
        <div>
            <div class="badge">Cliente</div>
            <h1>Crear cuenta</h1>
            <p class="helper-text">Regístrate para elegir tu plan y seleccionar tus comidas.</p>
        </div>
    </div>

    <?php if ($error !== ''): ?>
        <div class="message-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-grid">
        <div class="form-group full">
            <label>Nombre</label>
            <input class="input" type="text" name="name" required>
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
            <button class="button" type="submit">Crear cuenta</button>
            <a class="button-secondary" href="/login.php">Ya tengo cuenta</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
