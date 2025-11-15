<?php
session_start();

if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../login.php");
    exit;
}

require_once '../clases/Conexion.php';

$conexion = new Conexion();
$conn = $conexion->obtenerConexion();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../dashboard.php");
    exit;
}

$numero_sala = $_POST['numero_sala'];
$capacidad = $_POST['capacidad'];
$elementos = $_POST['elementos'];
$fecha_disponible = $_POST['fecha_disponible'];
$hora_inicio = $_POST['hora_inicio'];
$hora_fin = $_POST['hora_fin'];

if (strtotime($hora_inicio) >= strtotime($hora_fin)) {
    echo "La hora de inicio debe ser menor que la hora de fin.";
    exit;
}

try {
    $consulta = "SELECT COUNT(*) FROM sala 
                    WHERE numero_sala = :numero_sala 
                    AND fecha_disponible = :fecha_disponible 
                    AND hora_inicio = :hora_inicio 
                    AND hora_fin = :hora_fin";
    $stmt = $conn->prepare($consulta);
    $stmt->execute([
        ':numero_sala' => $numero_sala,
        ':fecha_disponible' => $fecha_disponible,
        ':hora_inicio' => $hora_inicio,
        ':hora_fin' => $hora_fin
    ]);

    if ($stmt->fetchColumn() > 0) {
        echo "Ya existe una sala con el mismo número y horario en esa fecha.";
        exit;
    }

    $sql = "INSERT INTO sala (numero_sala, capacidad, elementos, fecha_disponible, hora_inicio, hora_fin)
            VALUES (:numero_sala, :capacidad, :elementos, :fecha_disponible, :hora_inicio, :hora_fin)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':numero_sala' => $numero_sala,
        ':capacidad' => $capacidad,
        ':elementos' => $elementos,
        ':fecha_disponible' => $fecha_disponible,
        ':hora_inicio' => $hora_inicio,
        ':hora_fin' => $hora_fin
    ]);

    header("Location: ../salas.php?msg=Sala creada correctamente");

} catch (PDOException $e) {
    echo "Error al crear la sala: " . $e->getMessage();
}
?>
