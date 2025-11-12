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

if (isset($_POST['numero_sala'])&& isset($_POST['capacidad'])&&isset($_POST['elementos'])&&isset($_POST['fecha_disponible'])&&isset($_POST['hora_inicio'])&&isset($_POST['hora_fin'])&&isset($_POST['id_sala'])){
    $numero_sala = $_POST['numero_sala'];
    $capacidad = $_POST['capacidad'];
    $elementos = $_POST['elementos'];
    $fecha_disponible = $_POST['fecha_disponible'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $id_sala = $_POST['id_sala'];

    // Validación simple de horario
    if (strtotime($hora_inicio) >= strtotime($hora_fin)) {
        echo "La hora de inicio debe ser menor que la hora de fin.";
        exit;
    }

    try {
        // Validar que no exista una sala con el mismo número, fecha y horario (opcional)
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

        $sql = "UPDATE sala SET numero_sala = :numero_sala, capacidad = :capacidad, elementos = :elementos, fecha_disponible = :fecha_disponible, hora_inicio = :hora_inicio, hora_fin = :hora_fin 
                WHERE id_sala = :id_sala
                ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':numero_sala' => $numero_sala,
            ':capacidad' => $capacidad,
            ':elementos' => $elementos,
            ':fecha_disponible' => $fecha_disponible,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin,
            ':id_sala' => $id_sala
        ]);

        header("Location: ../salas.php?msg=Sala modificada correctamente");
        exit;

    } catch (PDOException $e) {
        echo "Error al editar la sala: " . $e->getMessage();
        exit;
    }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar sala</title>
</head>
<body>
        <h3>Editar sala</h3>

        <form action="procesar_edicion_sala.php" method="POST">
            <p>Número de sala:</p>
            <input type="text" name="numero_sala" required><br><br>

            <p>Capacidad:</p>
            <input type="number" name="capacidad" required><br><br>

            <p>Elementos:</p>
            <input type="text" name="elementos"><br><br>

            <p>Fecha de disponibilidad:</p>
            <input type="date" name="fecha_disponible" required><br><br>

            <p>Hora de inicio:</p>
            <input type="time" name="hora_inicio" required><br><br>

            <p>Hora de fin:</p>
            <input type="time" name="hora_fin" required><br><br>
            
            <input type="hidden" name="id_sala" value="<?= $_POST['id_sala'] ?>">

            <input type="submit" value="Confirmar">
        </form>
    </body>
</html>