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
    header("Location: ../salas.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_sala = $_POST['id_sala'] ?? null;
$fecha = $_POST['fecha'] ?? null;
$hora_inicio = $_POST['hora_inicio'] ?? null;
$hora_fin = $_POST['hora_fin'] ?? null;

if (!$id_sala || !$fecha || !$hora_inicio || !$hora_fin) {
    die("Datos incompletos para reservar la sala.");
}

try {
    // Verificar que no exista reserva para esa sala y horario (solapamiento)
    $sql_check = "SELECT s.id_sala, r.estado FROM reserva r INNER JOIN sala s ON r.id_sala = s.id_sala WHERE s.id_sala = :id_sala AND s.fecha_disponible = :fecha AND s.hora_inicio = :hora_inicio AND s.hora_fin = :hora_fin";
    $stmt = $conn->prepare($sql_check);
    $stmt->execute([
        ':id_sala' => $id_sala,
        ':fecha' => $fecha,
        ':hora_inicio' => $hora_inicio,
        ':hora_fin' => $hora_fin,
    ]);
    $cnt = $stmt->fetchAll();

    $sql = "";
    if (count($cnt) > 0) {
        if ($cnt[0]['estado'] === 'no disponible') {
            die("Error: La sala ya está reservada en ese horario.");
        } else {
            // Editar reserva
            $sql = "UPDATE reserva SET id_usuario = :id_usuario, estado = 'no disponible'
                    WHERE id_sala = :id_sala";
        }
    } else {
        // Insertar reserva
        $sql = "INSERT INTO reserva (id_usuario, id_sala, estado) 
                VALUES (:id_usuario, :id_sala, 'no disponible')";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':id_sala' => $id_sala,
    ]);
    header("Location: ../reservas.php?msg=Reserva realizada con éxito");
    exit;
} catch (PDOException $e) {
    die("Error al procesar la reserva: " . $e->getMessage());
}


