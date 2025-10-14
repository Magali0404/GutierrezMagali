<?php
require_once 'Conexion.php';

class Reserva {
    private $conn;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->obtenerConexion();
    }

    // Crear una reserva si no hay conflicto en fecha y hora
    public function reservar($id_usuario, $id_sala, $fecha, $hora_inicio, $hora_fin) {
        // Verificar conflictos
        $sql_conflicto = "SELECT COUNT(*) FROM reserva 
                          WHERE id_sala = ? AND fecha = ? 
                          AND (
                            (hora_inicio < ? AND hora_fin > ?) OR
                            (hora_inicio < ? AND hora_fin > ?) OR
                            (hora_inicio >= ? AND hora_fin <= ?)
                          ) AND estado = 'no disponible'";
                          
        $stmt = $this->conn->prepare($sql_conflicto);
        $stmt->execute([$id_sala, $fecha, $hora_fin, $hora_fin, $hora_inicio, $hora_inicio, $hora_inicio, $hora_fin]);
        $conflicto = $stmt->fetchColumn();

        if ($conflicto > 0) {
            return false; // Conflicto con otra reserva
        }

        // Insertar reserva con estado 'no disponible'
        $sql = "INSERT INTO reserva (id_usuario, id_sala, fecha, hora_inicio, hora_fin, estado) 
                VALUES (?, ?, ?, ?, ?, 'no disponible')";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_usuario, $id_sala, $fecha, $hora_inicio, $hora_fin]);
    }

    // Cancelar reserva (la pone como disponible)
    public function cancelar($id_reserva, $id_usuario) {
        $sql = "UPDATE reserva SET estado = 'disponible' WHERE id_reserva = ? AND id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_reserva, $id_usuario]);
    }

    // Obtener reservas de un usuario
    public function listarPorUsuario($id_usuario) {
        $sql = "SELECT r.*, s.numero_sala FROM reserva r 
                INNER JOIN sala s ON r.id_sala = s.id_sala 
                WHERE r.id_usuario = ? 
                ORDER BY r.fecha DESC, r.hora_inicio DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todas las reservas (para admin)
    public function listarTodas() {
        $sql = "SELECT r.*, s.numero_sala, u.nombre, u.apellido FROM reserva r 
                INNER JOIN sala s ON r.id_sala = s.id_sala 
                INNER JOIN usuarios u ON r.id_usuario = u.id_usuario 
                ORDER BY r.fecha DESC, r.hora_inicio DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener estado de una sala en una fecha y hora
    public function verificarDisponibilidad($id_sala, $fecha, $hora_inicio, $hora_fin) {
        $sql = "SELECT COUNT(*) FROM reserva 
                WHERE id_sala = ? AND fecha = ? 
                AND (
                    (hora_inicio < ? AND hora_fin > ?) OR
                    (hora_inicio < ? AND hora_fin > ?) OR
                    (hora_inicio >= ? AND hora_fin <= ?)
                ) AND estado = 'no disponible'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_sala, $fecha, $hora_fin, $hora_fin, $hora_inicio, $hora_inicio, $hora_inicio, $hora_fin]);
        return $stmt->fetchColumn() == 0; // true si está disponible
    }
}
?>
