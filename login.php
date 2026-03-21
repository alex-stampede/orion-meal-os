<?php

session_start();

require __DIR__ . '/config/database.php';

function db() {
    global $dbConfig;

    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['name']};charset=utf8mb4";

    return new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        header("Location: /admin/dashboard.php");
        exit;
    } else {
        $error = "Credenciales incorrectas";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Orion</title>
</head>
<body style="font-family: Arial; background:#0b1f19; color:white; display:flex; justify-content:center; align-items:center; height:100vh;">

<form method="POST" style="background:#102822; padding:30px; border-radius:10px; width:300px;">
    <h2>Login</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <input type="email" name="email" placeholder="Email" required style="width:100%; margin-bottom:10px;"><br>
    <input type="password" name="password" placeholder="Password" required style="width:100%; margin-bottom:10px;"><br>

    <button type="submit" style="width:100%;">Entrar</button>
</form>

</body>
</html>