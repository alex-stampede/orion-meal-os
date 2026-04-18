<?php

declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';

if (isset($_SESSION['user_id'], $_SESSION['role'])) {
    if ($_SESSION['role'] === 'customer') {
        header('Location: /app/dashboard.php');
        exit;
    }

    header('Location: /admin/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));

            if ($user['role'] === 'customer') {
                header('Location: /app/dashboard.php');
                exit;
            }

            header('Location: /admin/dashboard.php');
            exit;
        }

        $error = 'Credenciales incorrectas.';
    } catch (Throwable $e) {
        $error = 'Ocurrió un error al iniciar sesión.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Orion Meal OS</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <main class="shell">
        <section class="card page-card" style="max-width: 640px; margin: 40px auto;">
            <div class="page-top">
                <div>
                    <div class="badge">Acceso</div>
                    <h1>Iniciar sesión</h1>
                    <p>Accede a tu cuenta para administrar o seleccionar tus comidas.</p>
                </div>
            </div>

            <?php if ($error !== ''): ?>
                <div class="message-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="form-grid">
                <div class="form-group full">
                    <label>Correo</label>
                    <input class="input" type="email" name="email" required>
                </div>

                <div class="form-group full">
                    <label>Contraseña</label>
                    <input class="input" type="password" name="password" required>
                </div>

                <div class="actions-row">
                    <button class="button" type="submit">Entrar</button>
                    <a class="button-secondary" href="/app/register.php">Crear cuenta</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
