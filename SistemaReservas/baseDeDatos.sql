-- 1. Crear la base de datos
CREATE DATABASE IF NOT EXISTS reserva_salas;
USE reserva_salas;

-- 2. Crear tabla Rol
CREATE TABLE Rol (
    id_rol INT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL
);

-- 3. Crear tabla Usuarios
CREATE TABLE Usuarios (
    id_usuario INT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    DNI VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    contraseña VARCHAR(100) NOT NULL,
    id_rol INT,
    FOREIGN KEY (id_rol) REFERENCES Rol(id_rol)
);

-- 4. Crear tabla Sala
CREATE TABLE Sala (
    id_sala INT PRIMARY KEY,
    numero_sala VARCHAR(10) NOT NULL,
    capacidad INT NOT NULL,
    elementos TEXT,
    fecha_disponible DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL
);

-- 5. Crear tabla Reserva
CREATE TABLE Reserva (
    id_reserva INT PRIMARY KEY,
    id_usuario INT,
    id_sala INT,
    estado VARCHAR(20) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario),
    FOREIGN KEY (id_sala) REFERENCES Sala(id_sala)
);