<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
</head>
<body>
    <h2>Registro de Usuario</h2>

    <?php
    if (isset($_SESSION['error'])) {
        echo '<p>' . $_SESSION['error'] . '</p>';
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['mensaje'])) {
        echo '<p>' . $_SESSION['mensaje'] . '</p>';
        unset($_SESSION['mensaje']);
    }
    ?>

    <form action="acciones/procesar_registro.php" method="POST">
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="apellido">Apellido:</label><br>
        <input type="text" id="apellido" name="apellido" required><br><br>

        <label for="dni">DNI:</label><br>
        <input type="text" id="dni" name="dni" required pattern="\d+"><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="telefono">Teléfono:</label><br>
        <input type="text" id="telefono" name="telefono" required><br><br>

        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="password_confirm">Confirmar Contraseña:</label><br>
        <input type="password" id="password_confirm" name="password_confirm" required><br><br>

        <input type="submit" value="Registrar">
    </form>

    <p>¿Ya tenés cuenta? <a href="login.php">Iniciar sesión</a></p>
</body>
</html>
