<?php
require_once "clases/User.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    $resultado = $user->login($_POST['email'], $_POST['password']);

    if ($resultado) {
        $_SESSION['usuario'] = $resultado;  // Guardás los datos del usuario en sesión
        // Redireccionar a la página principal
        header("Location: principal.php");
        exit;
    } else {
        $mensaje = "Correo o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
</head>
<body>

    <h2>Iniciar Sesión</h2>

    <?php if (!empty($mensaje)): ?>
        <p><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="email">Correo:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Ingresar">
    </form>
</body>
</html>
