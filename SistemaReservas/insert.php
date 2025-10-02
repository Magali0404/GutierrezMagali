<?php
require_once "clases/User.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();

    try {
        $user->add(
            $_POST['nombre'],
            $_POST['apellido'],
            $_POST['dni'],
            $_POST['email'],
            $_POST['telefono'],
            $_POST['password']
        );
        echo "Registro exitoso. <a href='login.php'>Iniciar sesión</a>";
    } catch (PDOException $e) {
        echo "Error al registrar: " . $e->getMessage();
    }
}
