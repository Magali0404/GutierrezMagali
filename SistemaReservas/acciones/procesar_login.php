<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../clases/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';

    $usuario = new Usuario();
    $userData = $usuario->login($email, $contraseña);

    if ($userData) {
        $_SESSION['id_usuario'] = $userData['id_usuario'];
        $_SESSION['nombre'] = $userData['nombre'];
        $_SESSION['apellido'] = $userData['apellido'];
        $_SESSION['id_rol'] = $userData['id_rol'];

        header('Location: ../vistas/dashboard.php');
        exit;
    } else {
        $_SESSION['error'] = 'Email o contraseña incorrectos';
        header('Location: ../vistas/login.php');
        exit;
    }
} else {
    header('Location: ../vistas/login.php');
    exit;
}
