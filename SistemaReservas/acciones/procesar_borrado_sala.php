<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../clases/Conexion.php';

$conexion = new Conexion();
$conn = $conexion->obtenerConexion();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_sala'])) {
    header("Location: ../salas.php");
    exit;
}

$id_sala = $_POST['id_sala'];

$sql_reserva = "DELETE FROM reserva WHERE id_sala = ?";
$stmt_reserva = $conn->prepare($sql_reserva);
$stmt_reserva->execute([$id_sala]);

$sql = "DELETE FROM sala WHERE id_sala = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_sala]);

header("Location: ../salas.php");
exit;
?>
