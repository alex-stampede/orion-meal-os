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
    <style>
        :root {
            --bg: #061510;
            --card: rgba(255,255,255,0.06);
            --stroke: rgba(255,255,255,0.1);
            --text: #edf4f1;
            --muted: #a5b7b0;
            --accent: #0f6b57;
            --danger: #c84b4b;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: Inter, Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(15, 107, 87, 0.25), transparent 30%),
                linear-gradient(180deg, #04110d 0%, #081a15 100%);
            color: var(--text);
        }
        .card {
            width: 100%;
            max-width: 420px;
            background: var(--card);
            border: 1px solid var(--stroke);
            border-radius: 24px;
            padding: 28px;
            backdrop-filter: blur(14px);
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
        }
        .badge {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(15, 107, 87, 0.18);
            border: 1px solid rgba(125,255,207,.12);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 16px;
        }
        h1 {
            margin: 0 0 10px;
            font-size: 2rem;
        }
        p {
            margin: 0 0 20px;
            color: var(--muted);
            line-height: 1.6;
        }
        label {
            display: block;
            margin: 0 0 8px;
            font-size: .95rem;
        }
        input {
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 16px;
            border-radius: 14px;
            border: 1px solid var(--stroke);
            background: rgba(255,255,255,0.04);
            color: var(--text);
            outline: none;
        }
        input::placeholder {
            color: #90a29b;
        }
        button {
            width: 100%;
            padding: 14px 16px;
            border: 0;
            border-radius: 14px;
            background: var(--accent);
            color: white;
            font-weight: 700;
            cursor: pointer;
        }
        .error {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(200,75,75,0.12);
            border: 1px solid rgba(200,75,75,0.3);
            color: #ffd7d7;
        }
    </style>
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
