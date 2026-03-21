<?php

declare(strict_types=1);

session_start();

$dbConfig = require __DIR__ . '/config/database.php';

function db(array $dbConfig): PDO
{
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $dbConfig['host'],
        $dbConfig['name'],
        $dbConfig['charset']
    );

    return new PDO(
        $dsn,
        $dbConfig['user'],
        $dbConfig['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
}

if (isset($_SESSION['user_id'])) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $pdo = db($dbConfig);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));

            header('Location: /admin/dashboard.php');
            exit;
        }

        $error = 'Credenciales incorrectas';
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
    <title>Login | Orion Meal OS</title>

        <link rel="stylesheet" href="/assets/css/theme.css">
<link rel="stylesheet" href="/assets/css/base.css">
<link rel="stylesheet" href="/assets/css/components.css">

</head>
<body>
    <form class="card" method="POST" action="/login.php">
        <div class="badge">Admin Access</div>
        <h1>Iniciar sesión</h1>
        <p>Accede al panel interno de Orion Meal OS.</p>

        <?php if ($error !== ''): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <label for="email">Correo</label>
        <input id="email" type="email" name="email" placeholder="admin@app.minvitaciondigital.com.mx" required>

        <label for="password">Contraseña</label>
        <input id="password" type="password" name="password" placeholder="••••••••" required>

        <button type="submit">Entrar</button>
    </form>
</body>
</html>
