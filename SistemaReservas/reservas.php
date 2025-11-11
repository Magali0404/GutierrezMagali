<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require_once 'clases/Conexion.php';

$conexion = new Conexion();
$conn = $conexion->obtenerConexion();

$id_usuario = $_SESSION['id_usuario'];

try {
    // Obtener las reservas del usuario junto con datos de la sala
    $sql = "
        SELECT r.id_reserva, s.numero_sala, s.capacidad, s.elementos, s.fecha_disponible, s.hora_inicio, s.hora_fin
        FROM reserva r INNER JOIN sala s ON r.id_sala = s.id_sala
        WHERE r.id_usuario = :id_usuario AND r.estado = 'no disponible'
        ORDER BY s.fecha_disponible DESC, s.hora_inicio DESC
    "; // Borrar r.id_usuario = :id_usuario AND para el admin + columna de usuario

    $stmt = $conn->prepare($sql);
    $stmt->execute(['id_usuario' => $id_usuario]);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    http_response_code(500);
    die("Error al obtener las reservas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial</title>
</head>
<body>

<h2>Reservas</h2>

<?php if (isset($_GET['msg'])): ?>
    <p style="color:green;"><?= htmlspecialchars($_GET['msg']) ?></p>
<?php endif; ?>

<?php if (count($reservas) > 0): ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Número de sala</th>
                <th>Capacidad</th>
                <th>Elementos</th>
                <th>Fecha</th>
                <th>Hora inicio</th>
                <th>Hora fin</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservas as $reserva): ?>
                <tr>
                    <td><?= htmlspecialchars($reserva['numero_sala']) ?></td>
                    <td><?= htmlspecialchars($reserva['capacidad']) ?></td>
                    <td><?= htmlspecialchars($reserva['elementos']) ?></td>
                    <td><?= htmlspecialchars($reserva['fecha_disponible']) ?></td>
                    <td><?= htmlspecialchars($reserva['hora_inicio']) ?></td>
                    <td><?= htmlspecialchars($reserva['hora_fin']) ?></td>
                    <td>
                        <form action="acciones/procesar_cancelacion.php" method="POST" style="margin:0;">
                            <input type="hidden" name="id_reserva" value="<?= $reserva['id_reserva'] ?>">
                            <input type="hidden" name="id_usuario" value="<?php
                                if ($_SESSION['id_rol'] === 1) {
                                    echo $reserva['id_usuario'];
                                } else {
                                    echo $_SESSION['id_usuario'];
                                }
                            ?>">
                            <input type="submit" value="Cancelar reserva" onclick="return confirm('¿Seguro que deseas cancelar esta reserva?');">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No tienes reservas activas.</p>
<?php endif; ?>

<br>
<a href="dashboard.php">Volver al menú</a>

</body>
</html>
