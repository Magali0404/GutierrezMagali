-- ============================================
-- SISTEMA GYM HABIBI - BASE DE DATOS COMPLETA
-- ============================================

DROP DATABASE IF EXISTS gym_habibi;
CREATE DATABASE gym_habibi;
USE gym_habibi;

-- ============================================
-- TABLA: clientes (mejorada)
-- ============================================
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    dni VARCHAR(20) NOT NULL UNIQUE,
    fecha_nacimiento DATE,
    domicilio VARCHAR(100),
    telefono VARCHAR(20),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    ultimo_pago DATE NULL,
    INDEX idx_dni (dni),
    INDEX idx_estado (estado),
    INDEX idx_ultimo_pago (ultimo_pago)
);

-- ============================================
-- TABLA: historial_pagos (mejorada)
-- ============================================
CREATE TABLE historial_pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    fecha_pago DATE NOT NULL,
    monto DECIMAL(10,2) NOT NULL DEFAULT 15000.00,
    mes_pagado TINYINT NOT NULL, -- 1-12 (enero-diciembre)
    anio_pagado YEAR NOT NULL,
    metodo_pago ENUM('efectivo', 'transferencia', 'tarjeta') DEFAULT 'efectivo',
    observaciones TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cliente_mes_anio (cliente_id, mes_pagado, anio_pagado),
    INDEX idx_fecha_pago (fecha_pago),
    INDEX idx_mes_anio (mes_pagado, anio_pagado)
);

-- ============================================
-- TABLA: clientes_inactivos (automática)
-- ============================================
CREATE TABLE clientes_inactivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    fecha_inactivacion DATE NOT NULL,
    motivo ENUM('sin_pago_2_meses', 'sin_pago_3_meses', 'baja_voluntaria') DEFAULT 'sin_pago_2_meses',
    dias_sin_pago INT NOT NULL,
    observaciones TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    INDEX idx_fecha_inactivacion (fecha_inactivacion),
    INDEX idx_motivo (motivo)
);

-- ============================================
-- TRIGGER: Actualizar ultimo_pago automáticamente
-- ============================================
DELIMITER $$
CREATE TRIGGER actualizar_ultimo_pago 
AFTER INSERT ON historial_pagos
FOR EACH ROW
BEGIN
    UPDATE clientes 
    SET ultimo_pago = NEW.fecha_pago,
        estado = 'activo'
    WHERE id = NEW.cliente_id;
END$$
DELIMITER ;

-- ============================================
-- PROCEDIMIENTO: Marcar clientes inactivos automáticamente
-- ============================================
DELIMITER $$
CREATE PROCEDURE marcar_clientes_inactivos()
BEGIN
    -- Clientes sin pago hace más de 60 días
    INSERT INTO clientes_inactivos (cliente_id, fecha_inactivacion, motivo, dias_sin_pago, observaciones)
    SELECT 
        c.id,
        CURDATE(),
        'sin_pago_2_meses',
        DATEDIFF(CURDATE(), COALESCE(c.ultimo_pago, c.fecha_registro)),
        CONCAT('Sin pago desde: ', COALESCE(c.ultimo_pago, c.fecha_registro))
    FROM clientes c
    WHERE c.estado = 'activo'
    AND (
        (c.ultimo_pago IS NOT NULL AND DATEDIFF(CURDATE(), c.ultimo_pago) > 60)
        OR 
        (c.ultimo_pago IS NULL AND DATEDIFF(CURDATE(), c.fecha_registro) > 60)
    )
    AND c.id NOT IN (SELECT cliente_id FROM clientes_inactivos)
    ON DUPLICATE KEY UPDATE 
        dias_sin_pago = DATEDIFF(CURDATE(), COALESCE(c.ultimo_pago, c.fecha_registro));
    
    -- Actualizar estado en tabla clientes
    UPDATE clientes c
    INNER JOIN clientes_inactivos ci ON c.id = ci.cliente_id
    SET c.estado = 'inactivo'
    WHERE c.estado = 'activo';
END$$
DELIMITER ;

-- ============================================
-- EVENT: Ejecutar automáticamente cada día
-- ============================================
SET GLOBAL event_scheduler = ON;

CREATE EVENT IF NOT EXISTS revisar_inactivos_diario
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
CALL marcar_clientes_inactivos();

-- ============================================
-- DATOS DE PRUEBA
-- ============================================

-- Insertar clientes de prueba
INSERT INTO clientes (nombre, apellido, dni, fecha_nacimiento, domicilio, telefono, ultimo_pago) VALUES
('Carlos', 'García', '32154879', '1985-03-15', 'Av. Corrientes 1234', '1123456789', '2024-12-15'),
('Ana', 'Martínez', '35879542', '1990-07-22', 'San Martín 567', '1198765432', '2025-01-10'),
('Luis', 'Rodríguez', '30456123', '1982-11-08', 'Belgrano 890', '1155667788', '2024-11-20'),
('María', 'Fernández', '38789456', '1995-02-14', 'Mitre 345', '1133221144', '2025-01-25'),
('Javier', 'López', '29888777', '1980-09-30', 'Rivadavia 678', '1166554433', '2024-10-05'),
('Sofía', 'Pérez', '40123789', '1998-06-12', 'Sarmiento 123', '1122998877', '2025-01-20'),
('Miguel', 'Gómez', '34567890', '1987-12-03', 'Moreno 456', '1144775566', '2024-09-15'),
('Lucía', 'Díaz', '36987123', '1993-04-18', 'Alsina 789', '1188664422', '2025-01-05'),
('Pedro', 'Sánchez', '31234567', '1984-08-25', 'Pueyrredón 012', '1177339911', '2024-08-30'),
('Elena', 'Torres', '39876543', '1996-01-09', 'Lavalle 345', '1133882277', '2025-01-15');

-- Insertar historial de pagos
INSERT INTO historial_pagos (cliente_id, fecha_pago, monto, mes_pagado, anio_pagado, metodo_pago) VALUES
(1, '2024-12-15', 15000.00, 12, 2024, 'efectivo'),
(2, '2025-01-10', 15000.00, 1, 2025, 'transferencia'),
(3, '2024-11-20', 15000.00, 11, 2024, 'efectivo'),
(4, '2025-01-25', 15000.00, 1, 2025, 'tarjeta'),
(5, '2024-10-05', 15000.00, 10, 2024, 'efectivo'),
(6, '2025-01-20', 15000.00, 1, 2025, 'transferencia'),
(7, '2024-09-15', 15000.00, 9, 2024, 'efectivo'),
(8, '2025-01-05', 15000.00, 1, 2025, 'efectivo'),
(9, '2024-08-30', 15000.00, 8, 2024, 'efectivo'),
(10, '2025-01-15', 15000.00, 1, 2025, 'transferencia');

-- Ejecutar procedimiento para marcar inactivos
CALL marcar_clientes_inactivos();

-- ============================================
-- CONSULTAS ÚTILES PARA VERIFICAR
-- ============================================

-- Ver todos los clientes con su estado
-- SELECT c.*, DATEDIFF(CURDATE(), COALESCE(c.ultimo_pago, c.fecha_registro)) as dias_sin_pago 
-- FROM clientes c ORDER BY c.estado, dias_sin_pago DESC;

-- Ver clientes inactivos
-- SELECT c.nombre, c.apellido, ci.* FROM clientes_inactivos ci
-- INNER JOIN clientes c ON ci.cliente_id = c.id;

-- Ver historial completo
-- SELECT c.nombre, c.apellido, hp.* FROM historial_pagos hp
-- INNER JOIN clientes c ON hp.cliente_id = c.id
-- ORDER BY hp.fecha_pago DESC;