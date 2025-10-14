<?php
session_start();
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
</head>
<body>

<h2>Iniciar sesión</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="../acciones/procesar_login.php" method="POST">
    <label for="email">Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label for="contraseña">Contraseña:</label><br>
    <input type="password" name="contraseña" required><br><br>

    <input type="submit" value="Ingresar">
</form>

</body>
</html>
