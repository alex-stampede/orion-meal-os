<?php
require __DIR__ . '/../admin/partials/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name === '' || $email === '' || $password === '') {
        $error = "Completa todos los campos";
    } else {

        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("
            INSERT INTO users (role, first_name, email, password_hash)
            VALUES ('customer', ?, ?, ?)
        ");

        $stmt->execute([$name, $email, $hash]);

        header('Location: /app/login.php');
        exit;
    }
}
?>

<h2>Registro</h2>

<?php if ($error): ?>
<p><?= $error ?></p>
<?php endif; ?>

<form method="POST">
<input name="name" placeholder="Nombre">
<input name="email" placeholder="Email">
<input name="password" placeholder="Password">
<button>Registrarme</button>
</form>