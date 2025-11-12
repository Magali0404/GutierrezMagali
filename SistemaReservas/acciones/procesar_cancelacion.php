<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../clases/Conexion.php';

$conexion = new Conexion();
$conn = $conexion->obtenerConexion();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../reservas.php");
    exit;
}

$id_reserva = $_POST['id_reserva'] ?? null;
$id_usuario = $_POST['id_usuario'];

if (!$id_reserva) {
    die("No se especificó la reserva a cancelar.");
}

try {
    // Verificar que la reserva pertenezca al usuario
    $sql_check = "SELECT COUNT(*) FROM reserva WHERE id_reserva = :id_reserva AND id_usuario = :id_usuario";
    $stmt = $conn->prepare($sql_check);
    $stmt->execute([
        ':id_reserva' => $id_reserva,
        ':id_usuario' => $id_usuario,
    ]);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        die("No tienes permiso para cancelar esta reserva o no existe.");
    }
    // Cambia el estado a disponible
    $sql_delete = "UPDATE reserva SET estado = 'disponible' WHERE id_reserva = :id_reserva";
    $stmt = $conn->prepare($sql_delete);
    $stmt->execute([':id_reserva' => $id_reserva]);

    header("Location: ../reservas.php?msg=Reserva cancelada correctamente");
    exit;

} catch (PDOException $e) {
    die("Error al cancelar la reserva: " . $e->getMessage());
}

