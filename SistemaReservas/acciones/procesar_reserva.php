<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../vistas/login.php");
    exit;
}

require_once '../clases/Conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_usuario = $_SESSION['id_usuario'];
    $id_sala = $_POST['id_sala'] ?? null;
    $fecha = $_POST['fecha'] ?? null;
    $hora_inicio = $_POST['hora_inicio'] ?? null;
    $hora_fin = $_POST['hora_fin'] ?? null;

    if (!$id_sala || !$fecha || !$hora_inicio || !$hora_fin) {
        die("Datos incompletos para reservar la sala.");
    }

    $conexion = new Conexion();
    $conn = $conexion->obtenerConexion();

    try {
        // Verificar que no exista reserva para esa sala y horario (solapamiento)
        $sql_check = "SELECT COUNT(*) FROM reserva WHERE id_sala = :id_sala AND fecha = :fecha 
                      AND (hora_inicio < :hora_fin AND hora_fin > :hora_inicio)";
        $stmt = $conn->prepare($sql_check);
        $stmt->execute([
            ':id_sala' => $id_sala,
            ':fecha' => $fecha,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin,
        ]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            die("Error: La sala ya está reservada en ese horario.");
        }

        // Insertar reserva
        $sql_insert = "INSERT INTO reserva (id_usuario, id_sala, fecha, hora_inicio, hora_fin, estado) 
                       VALUES (:id_usuario, :id_sala, :fecha, :hora_inicio, :hora_fin, 'no disponible')";
        $stmt = $conn->prepare($sql_insert);
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':id_sala' => $id_sala,
            ':fecha' => $fecha,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin,
        ]);

        header("Location: ../vistas/reservas.php?msg=Reserva realizada con éxito");
        exit;

    } catch (PDOException $e) {
        die("Error al procesar la reserva: " . $e->getMessage());
    }

} else {
    header("Location: ../vistas/salas.php");
    exit;
}
