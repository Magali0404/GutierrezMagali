<?php
require_once 'Conexion.php';

class Sala {
    private $conn;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->obtenerConexion();
    }

    // Agregar una sala
    public function agregar($numero_sala, $capacidad, $elementos) {
        $sql = "INSERT INTO sala (numero_sala, capacidad, elementos) 
                VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$numero_sala, $capacidad, $elementos]);
    }

    // Listar todas las salas
    public function listar() {
        $sql = "SELECT * FROM sala ORDER BY numero_sala";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener sala por ID
    public function obtenerPorId($id_sala) {
        $sql = "SELECT * FROM sala WHERE id_sala = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_sala]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Editar sala
    public function editar($id_sala, $numero_sala, $capacidad, $elementos) {
        $sql = "UPDATE sala SET 
                    numero_sala = ?, 
                    capacidad = ?, 
                    elementos = ?
                WHERE id_sala = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$numero_sala, $capacidad, $elementos, $id_sala]);
    }

    // Eliminar sala
    public function eliminar($id_sala) {
        $sql = "DELETE FROM sala WHERE id_sala = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_sala]);
    }
}
?>
