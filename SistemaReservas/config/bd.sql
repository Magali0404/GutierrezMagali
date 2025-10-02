-- Tabla de Roles
CREATE TABLE Rol (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL
);

-- Tabla de Usuarios
CREATE TABLE Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    DNI CHAR(8) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(15),
    contraseña VARCHAR(255) NOT NULL,
    id_rol INT NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES Rol(id_rol)
);

-- Tabla de Salas
CREATE TABLE Sala (
    id_sala INT AUTO_INCREMENT PRIMARY KEY,
    numero_sala INT NOT NULL,
    capacidad INT NOT NULL,
    elementos VARCHAR(255)
);

-- Tabla de Reservas
CREATE TABLE Reserva (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_sala INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    estado BOOLEAN DEFAULT 1,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario),
    FOREIGN KEY (id_sala) REFERENCES Sala(id_sala)
);

--Para la tabla rol
INSERT INTO Rol (id_rol, nombre_rol) VALUES
(1, 'Administrador'),
(2, 'Usuario');

--creamos un admin
INSERT INTO Usuarios (nombre, apellido, DNI, email, telefono, contraseña, id_rol)
VALUES (
    'Giuliana',
    'Romero',
    '45334221',
    'admin@admin.com',
    '3446220128',
    'admin123',
    1
);