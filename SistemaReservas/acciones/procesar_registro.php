<?php
session_start();
require_once '../clases/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password !== $password_confirm) {
        $_SESSION['error'] = 'Las contraseñas no coinciden.';
        header('Location: ../vistas/registro.php');
        exit;
    }

    $usuario = new Usuario();

    if (!$usuario->registrar($nombre, $apellido, $dni, $email, $telefono, $password)) {
        $_SESSION['error'] = 'El email o DNI ya están registrados.';
        header('Location: ../vistas/registro.php');
        exit;
    } else {
        $_SESSION['mensaje'] = 'Registro exitoso. Por favor inicia sesión.';
        header('Location: ../vistas/login.php');
        exit;
    }
} else {
    header('Location: ../vistas/registro.php');
    exit;
}
