<?php
/**
 * GYM HABIBI - CONFIGURACIÓN DE BASE DE DATOS ACTUALIZADA
 * Incluye sincronización automática con archivos JSON
 */

// ============================================
// CONFIGURACIÓN DE CONEXIÓN
// ============================================
$host = "localhost";
$usuario = "root";
$password = "";
$base_de_datos = "gym_habibi"; // Nombre actualizado

$conn = new mysqli($host, $usuario, $password, $base_de_datos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// ============================================
// FUNCIÓN: Sincronizar datos con JSON
// ============================================
function sincronizarJSON($conn) {
    // Rutas de archivos JSON
    $rutaClientes = __DIR__ . "/../../public/data/clientes.json";
    $rutaInactivos = __DIR__ . "/../../public/data/clientes_inactivos.json";
    $rutaPagos = __DIR__ . "/../../public/data/historial_pagos.json";
    
    // Crear directorio si no existe
    $dirData = dirname($rutaClientes);
    if (!file_exists($dirData)) {
        mkdir($dirData, 0777, true);
    }

    try {
        // SINCRONIZAR CLIENTES ACTIVOS
        $sqlClientes = "
            SELECT c.*, 
                   DATEDIFF(CURDATE(), COALESCE(c.ultimo_pago, c.fecha_registro)) as dias_sin_pago
            FROM clientes c 
            WHERE c.estado = 'activo'
            ORDER BY c.nombre, c.apellido
        ";
        $resultClientes = $conn->query($sqlClientes);
        $clientes = [];
        
        if ($resultClientes && $resultClientes->num_rows > 0) {
            while ($row = $resultClientes->fetch_assoc()) {
                $clientes[] = [
                    'id' => $row['id'],
                    'nombre' => $row['nombre'],
                    'apellido' => $row['apellido'],
                    'dni' => $row['dni'],
                    'telefono' => $row['telefono'],
                    'domicilio' => $row['domicilio'],
                    'fechaNac' => $row['fecha_nacimiento'],
                    'fechaRegistro' => $row['fecha_registro'],
                    'ultimoPago' => $row['ultimo_pago'],
                    'diasSinPago' => $row['dias_sin_pago'],
                    'estado' => $row['estado']
                ];
            }
        }
        file_put_contents($rutaClientes, json_encode($clientes, JSON_PRETTY_PRINT));

        // SINCRONIZAR CLIENTES INACTIVOS
        $sqlInactivos = "
            SELECT c.id, c.nombre, c.apellido, c.dni, c.telefono, c.domicilio,
                   c.fecha_nacimiento, c.ultimo_pago, ci.fecha_inactivacion, 
                   ci.motivo, ci.dias_sin_pago, ci.observaciones
            FROM clientes c 
            INNER JOIN clientes_inactivos ci ON c.id = ci.cliente_id
            WHERE c.estado = 'inactivo'
            ORDER BY ci.fecha_inactivacion DESC
        ";
        $resultInactivos = $conn->query($sqlInactivos);
        $inactivos = [];
        
        if ($resultInactivos && $resultInactivos->num_rows > 0) {
            while ($row = $resultInactivos->fetch_assoc()) {
                $inactivos[] = [
                    'id' => $row['id'],
                    'nombre' => $row['nombre'],
                    'apellido' => $row['apellido'],
                    'dni' => $row['dni'],
                    'telefono' => $row['telefono'],
                    'domicilio' => $row['domicilio'],
                    'fechaNac' => $row['fecha_nacimiento'],
                    'ultimoPago' => $row['ultimo_pago'],
                    'fechaInactivacion' => $row['fecha_inactivacion'],
                    'motivo' => $row['motivo'],
                    'diasSinPago' => $row['dias_sin_pago'],
                    'observaciones' => $row['observaciones']
                ];
            }
        }
        file_put_contents($rutaInactivos, json_encode($inactivos, JSON_PRETTY_PRINT));

        // SINCRONIZAR HISTORIAL DE PAGOS
        $sqlPagos = "
            SELECT c.id as cliente_id, c.nombre, c.apellido, c.dni,
                   hp.fecha_pago, hp.monto, hp.mes_pagado, hp.anio_pagado,
                   hp.metodo_pago, hp.observaciones,
                   CASE 
                       WHEN hp.fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1
                       ELSE 0
                   END as pago_reciente
            FROM clientes c
            LEFT JOIN historial_pagos hp ON c.id = hp.cliente_id
            ORDER BY c.nombre, c.apellido, hp.fecha_pago DESC
        ";
        $resultPagos = $conn->query($sqlPagos);
        $pagos = [];
        
        if ($resultPagos && $resultPagos->num_rows > 0) {
            while ($row = $resultPagos->fetch_assoc()) {
                $clienteId = $row['cliente_id'];
                
                // Si el cliente no existe en el array, agregarlo
                if (!isset($pagos[$clienteId])) {
                    $pagos[$clienteId] = [
                        'cliente_id' => $clienteId,
                        'nombre' => $row['nombre'],
                        'apellido' => $row['apellido'],
                        'dni' => $row['dni'],
                        'fecha_pago' => $row['fecha_pago'],
                        'pago_reciente' => $row['pago_reciente'],
                        'historial_pagos' => []
                    ];
                }
                
                // Agregar pago al historial si existe
                if ($row['fecha_pago']) {
                    $pagos[$clienteId]['historial_pagos'][] = [
                        'fecha' => $row['fecha_pago'],
                        'monto' => $row['monto'],
                        'mes' => $row['mes_pagado'],
                        'anio' => $row['anio_pagado'],
                        'metodo' => $row['metodo_pago'],
                        'observaciones' => $row['observaciones']
                    ];
                }
            }
        }
        
        // Convertir a array indexado
        $pagosArray = array_values($pagos);
        file_put_contents($rutaPagos, json_encode($pagosArray, JSON_PRETTY_PRINT));

        return true;

    } catch (Exception $e) {
        error_log("Error sincronizando JSON: " . $e->getMessage());
        return false;
    }
}

// ============================================
// PROCESAR FORMULARIO DE REGISTRO
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    
    // Ejecutar procedimiento para actualizar inactivos antes de procesar
    $conn->query("CALL marcar_clientes_inactivos()");
    
    // Capturar datos del formulario
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $dni = trim($_POST['dni']);
    $fecha_nacimiento = $_POST['fechaNac'] ?? null;
    $telefono = trim($_POST['telefono']);
    $domicilio = trim($_POST['domicilio']);

    // Validaciones básicas
    if (empty($nombre) || empty($apellido) || empty($dni)) {
        die("Error: Nombre, apellido y DNI son obligatorios.");
    }

    // Verificar si el DNI ya existe
    $sqlVerificar = "SELECT id FROM clientes WHERE dni = ?";
    $stmtVerificar = $conn->prepare($sqlVerificar);
    $stmtVerificar->bind_param("s", $dni);
    $stmtVerificar->execute();
    $resultVerificar = $stmtVerificar->get_result();

    if ($resultVerificar->num_rows > 0) {
        die("Error: Ya existe un cliente con el DNI $dni");
    }

    // Insertar nuevo cliente
    $sql = "INSERT INTO clientes (nombre, apellido, dni, fecha_nacimiento, domicilio, telefono) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $nombre, $apellido, $dni, $fecha_nacimiento, $domicilio, $telefono);

    if ($stmt->execute()) {
        $clienteId = $conn->insert_id;
        
        // Sincronizar JSON después de insertar
        if (sincronizarJSON($conn)) {
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px;'>";
            echo "<h3>✅ Cliente registrado exitosamente</h3>";
            echo "<p><strong>ID:</strong> $clienteId</p>";
            echo "<p><strong>Nombre:</strong> $nombre $apellido</p>";
            echo "<p><strong>DNI:</strong> $dni</p>";
            echo "<p>Los archivos JSON han sido actualizados automáticamente.</p>";
            echo "<a href='/gym/public/clientes.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px;'>Ver Lista de Clientes</a>";
            echo "</div>";
        } else {
            echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px;'>";
            echo "⚠️ Cliente guardado en base de datos, pero hubo un problema sincronizando JSON.";
            echo "</div>";
        }
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px;'>";
        echo "❌ Error al guardar cliente: " . $stmt->error;
        echo "</div>";
    }

    $stmt->close();
}

// ============================================
// FUNCIÓN: Obtener estadísticas del gym
// ============================================
function obtenerEstadisticas($conn) {
    $stats = [];
    
    // Total clientes activos
    $result = $conn->query("SELECT COUNT(*) as total FROM clientes WHERE estado = 'activo'");
    $stats['activos'] = $result->fetch_assoc()['total'];
    
    // Total clientes inactivos
    $result = $conn->query("SELECT COUNT(*) as total FROM clientes WHERE estado = 'inactivo'");
    $stats['inactivos'] = $result->fetch_assoc()['total'];
    
    // Pagos este mes
    $result = $conn->query("SELECT COUNT(*) as total FROM historial_pagos WHERE MONTH(fecha_pago) = MONTH(CURDATE()) AND YEAR(fecha_pago) = YEAR(CURDATE())");
    $stats['pagos_mes'] = $result->fetch_assoc()['total'];
    
    // Ingresos este mes
    $result = $conn->query("SELECT COALESCE(SUM(monto), 0) as total FROM historial_pagos WHERE MONTH(fecha_pago) = MONTH(CURDATE()) AND YEAR(fecha_pago) = YEAR(CURDATE())");
    $stats['ingresos_mes'] = $result->fetch_assoc()['total'];
    
    return $stats;
}

// ============================================
// AUTO-SINCRONIZACIÓN AL CARGAR EL ARCHIVO
// ============================================
if (!isset($_POST['no_auto_sync'])) {
    // Actualizar clientes inactivos
    $conn->query("CALL marcar_clientes_inactivos()");
    
    // Sincronizar JSON
    sincronizarJSON($conn);
}

?>