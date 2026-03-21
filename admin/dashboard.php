<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body style="font-family: Arial; background:#061510; color:white; padding:40px;">

<h1>Dashboard Orion Meal OS</h1>

<p>Bienvenido, ya estás dentro del sistema 🚀</p>

<a href="/logout.php">Cerrar sesión</a>

</body>
</html>