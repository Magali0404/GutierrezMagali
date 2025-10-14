<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require_once '../clases/Conexion.php';

// Crear conexión
$conexion = new Conexion();
$conn = $conexion->obtenerConexion();

// Obtener todas las salas junto con estado (disponible o no)
try {
    $sql = "
        SELECT s.id_sala, s.numero_sala, s.capacidad, s.elementos, s.fecha_disponible, s.hora_inicio, s.hora_fin,
            CASE
                WHEN EXISTS (
                    SELECT 1 FROM reserva r
                    WHERE r.id_sala = s.id_sala
                    AND r.fecha = s.fecha_disponible
                    AND (r.hora_inicio < s.hora_fin AND r.hora_fin > s.hora_inicio)
                ) THEN 'No disponible'
                ELSE 'Disponible'
            END AS estado
        FROM sala s
        ORDER BY s.fecha_disponible, s.hora_inicio
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $salas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener las salas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Salas disponibles</title>
</head>
<body>

<h2>Salas disponibles</h2>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Número de sala</th>
            <th>Capacidad</th>
            <th>Elementos</th>
            <th>Fecha</th>
            <th>Hora inicio</th>
            <th>Hora fin</th>
            <th>Estado</th>
            <th>Reservar</th>
        </tr>
    </thead>
    <tbody>
    <?php if (count($salas) > 0): ?>
        <?php foreach ($salas as $sala): ?>
            <tr>
                <td><?= htmlspecialchars($sala['numero_sala']) ?></td>
                <td><?= htmlspecialchars($sala['capacidad']) ?></td>
                <td><?= htmlspecialchars($sala['elementos']) ?></td>
                <td><?= htmlspecialchars($sala['fecha_disponible']) ?></td>
                <td><?= htmlspecialchars($sala['hora_inicio']) ?></td>
                <td><?= htmlspecialchars($sala['hora_fin']) ?></td>
                <td><?= $sala['estado'] ?></td>
                <td>
                    <?php if ($sala['estado'] === 'Disponible'): ?>
                        <form action="../acciones/procesar_reserva.php" method="POST" style="margin:0;">
                            <input type="hidden" name="id_sala" value="<?= $sala['id_sala'] ?>">
                            <input type="hidden" name="fecha" value="<?= $sala['fecha_disponible'] ?>">
                            <input type="hidden" name="hora_inicio" value="<?= $sala['hora_inicio'] ?>">
                            <input type="hidden" name="hora_fin" value="<?= $sala['hora_fin'] ?>">
                            <input type="submit" value="Reservar">
                        </form>
                    <?php else: ?>
                        No disponible
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="8">No hay salas registradas.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<br>
<a href="dashboard.php">Volver al menú</a>

</body>
</html>
