<?php
require __DIR__ . '/../partials/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("
        INSERT INTO users (role, first_name, last_name, email, password_hash)
        VALUES ('customer', ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $hash
    ]);

    header('Location: /admin/customers/index.php');
    exit;
}

require __DIR__ . '/../partials/header.php';
?>

<form method="POST" class="form-grid">
    <input class="input" name="first_name" placeholder="Nombre">
    <input class="input" name="last_name" placeholder="Apellido">
    <input class="input" name="email" placeholder="Email">
    <input class="input" name="password" placeholder="Password">

    <button class="button">Crear cliente</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>