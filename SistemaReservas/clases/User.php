<?php
require_once __DIR__ . '/../config/Database.php'; 

class User {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    // Método para registrar un usuario
    public function add($nombre, $apellido, $dni, $email, $telefono, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO Usuarios (nombre, apellido, DNI, email, telefono, contraseña, id_rol)
                VALUES (:nombre, :apellido, :dni, :email, :telefono, :password, 2)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':dni' => $dni,
            ':email' => $email,
            ':telefono' => $telefono,
            ':password' => $hash
        ]);
    }

    // Método para iniciar sesión
    public function login($email, $password) {
        $sql = "SELECT * FROM Usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica que exista y que la contraseña coincida
        if ($usuario && password_verify($password, $usuario['contraseña'])) {
            return $usuario;  // Devuelve los datos del usuario
        }

        return false;  // Error de login
    }
}
