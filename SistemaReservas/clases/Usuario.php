<?php
require_once 'Conexion.php';

class Usuario {
    private $conn;
    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->obtenerConexion();
    }

    // Registrar usuario
    public function registrar($nombre, $apellido, $dni, $email, $telefono, $contraseña) {
        $sql_check = "SELECT COUNT(*) FROM usuarios WHERE email = ? OR dni = ?";
        $stmt_check = $this->conn->prepare($sql_check);
        $stmt_check->execute([$email, $dni]);
        $count = $stmt_check->fetchColumn();

        if ($count > 0) {
            return false;
        }

        $hash = password_hash($contraseña, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, apellido, dni, email, telefono, contraseña, id_rol)
                VALUES (?, ?, ?, ?, ?, ?, 2)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nombre, $apellido, $dni, $email, $telefono, $hash]);
    }

    // Iniciar sesión
    public function login($email, $contraseña) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
            return $usuario;
        }
        return false;
    }
}
?>
