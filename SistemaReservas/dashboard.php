<?php
session_start();
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$nombre = $_SESSION['nombre'];
$apellido = $_SESSION['apellido'];
$rol = $_SESSION['id_rol'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>

    <h1>Bienvenido, <?= htmlspecialchars($nombre) . ' ' . htmlspecialchars($apellido) ?></h1>

    <?php if ($rol == 1): ?>
        <h2>Panel del Administrador</h2>

        <a href="salas.php">Ver salas</a>
        <br>
        <a href="reservas.php">Historial de reservas</a>

        <h3>Crear sala</h3>

        <form action="acciones/procesar_crear_sala.php" method="POST">
            <p>Número de sala:</p>
            <input type="text" name="numero_sala" required><br><br>

            <p>Capacidad:</p>
            <input type="number" name="capacidad" required><br><br>

            <p>Elementos:</p>
            <input type="text" name="elementos"><br><br>

            <p>Fecha de disponibilidad:</p>
            <input type="date" name="fecha_disponible" required><br><br>

            <p>Hora de inicio:</p>
            <input type="time" name="hora_inicio" required><br><br>

            <p>Hora de fin:</p>
            <input type="time" name="hora_fin" required><br><br>

            <input type="submit" value="Crear sala">
        </form>

    <?php else: ?>
        <h3>Panel del Usuario</h3>
        <p>Puedes ver las salas disponibles y hacer reservas.</p>
        <a href="salas.php">Ver salas</a><br>
        <a href="reservas.php">Historial de reservas</a>
    <?php endif; ?>

    <br><br>
    <a href="dashboard.php?logout=1">Cerrar sesión</a>

</body>
</html>
