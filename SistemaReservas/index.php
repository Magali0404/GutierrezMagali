<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
</head>
<body>

    <h2>Formulario de Registro</h2>

    <form method="post" action="insert.php"> 
      
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="apellido">Apellido:</label><br>
        <input type="text" id="apellido" name="apellido" required><br><br>

        <label for="dni">DNI:</label><br>
        <input type="text" id="dni" name="dni" required><br><br>

        <label for="email">Correo:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="telefono">Teléfono:</label><br>
        <input type="tel" id="telefono" name="telefono" required><br><br>

        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Registrarse">
    </form>

    <p>¿Ya tenés cuenta? <a href="login.php">Iniciar sesión</a></p>

</body>
</html>
