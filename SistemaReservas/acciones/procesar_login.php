<?php
session_start();

require_once '../clases/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}
$email = $_POST['email'] ?? '';
$contraseña = $_POST['contraseña'] ?? '';

$usuario = new Usuario();
$userData = $usuario->login($email, $contraseña);

if ($userData) {
    $_SESSION['id_usuario'] = $userData['id_usuario'];
    $_SESSION['nombre'] = $userData['nombre'];
    $_SESSION['apellido'] = $userData['apellido'];
    $_SESSION['id_rol'] = $userData['id_rol'];

    header('Location: ../dashboard.php');
    exit;
} else {
    $_SESSION['error'] = 'Email o contraseña incorrectos';
    header('Location: ../login.php');
    exit;
}
?>