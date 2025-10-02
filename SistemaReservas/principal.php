<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    // Si no hay sesión, redirige a login
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página Principal</title>
</head>
<body>
    <h1>Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?>!</h1>

    <h2>Salas disponibles:</h2>
    

</body>
</html>
