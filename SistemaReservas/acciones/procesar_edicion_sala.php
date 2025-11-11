<?php
// Si no hay datos adicionales de la sala, mostrar un formulario para cambiarlos que redirija
// A la misma página (con los datos nuevos)
// Si hay datos adicionales (fecha_inicio, etc) se guardan y se redirige a ../salas.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../clases/Conexion.php';

$conexion = new Conexion();
$conn = $conexion->obtenerConexion();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../salas.php");
    exit;
}

//boton editar