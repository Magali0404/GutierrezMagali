-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Volcando estructura de base de datos para reserva_salas
CREATE DATABASE IF NOT EXISTS `reserva_salas` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `reserva_salas`;

-- Volcando estructura para tabla reserva_salas.reserva
CREATE TABLE IF NOT EXISTS `reserva` (
  `id_reserva` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_sala` int NOT NULL,
  `estado` enum('disponible','no disponible') NOT NULL DEFAULT 'no disponible',
  PRIMARY KEY (`id_reserva`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_sala` (`id_sala`),
  CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id_sala`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla reserva_salas.reserva: ~4 rows (aproximadamente)
INSERT INTO `reserva` (`id_reserva`, `id_usuario`, `id_sala`, `estado`) VALUES
	(7, 6, 1, 'no disponible'),
	(8, 7, 8, 'disponible'),
	(9, 7, 10, 'no disponible');

-- Volcando estructura para tabla reserva_salas.rol
CREATE TABLE IF NOT EXISTS `rol` (
  `id_rol` int NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla reserva_salas.rol: ~2 rows (aproximadamente)
INSERT INTO `rol` (`id_rol`, `nombre_rol`) VALUES
	(1, 'admin'),
	(2, 'usuario');

-- Volcando estructura para tabla reserva_salas.sala
CREATE TABLE IF NOT EXISTS `sala` (
  `id_sala` int NOT NULL AUTO_INCREMENT,
  `numero_sala` varchar(10) NOT NULL,
  `capacidad` int NOT NULL,
  `elementos` text,
  `fecha_disponible` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  PRIMARY KEY (`id_sala`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla reserva_salas.sala: ~3 rows (aproximadamente)
INSERT INTO `sala` (`id_sala`, `numero_sala`, `capacidad`, `elementos`, `fecha_disponible`, `hora_inicio`, `hora_fin`) VALUES
	(1, '1', 20, 'tele y cafetera', '2025-11-29', '20:00:00', '21:00:00'),
	(8, '3', 20, 'tele, cafetera, escenario', '2025-11-16', '20:00:00', '22:00:00'),
	(10, '4', 20, 'tele', '2025-11-20', '22:00:00', '23:00:00'),
	(11, '5', 20, 'Proyector', '2025-11-30', '12:00:00', '13:00:00');

-- Volcando estructura para tabla reserva_salas.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `id_rol` int NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `dni` (`dni`),
  UNIQUE KEY `email` (`email`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla reserva_salas.usuarios: ~5 rows (aproximadamente)
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `dni`, `email`, `telefono`, `contraseña`, `id_rol`) VALUES
	(3, 'Javier', 'Parra', '23013634', 'admin@email.com', '123456789', '$2y$10$UrXbHs2HZ8SEp6C2EBRQE.WH9eA/yh0WyHlqEHt60LL3flcJmXcVO', 1),
	(4, 'giuliana', 'romero', '45234678', 'giu@email.com', '3446123987', '$2y$10$alipXNYEYwaHfRXBfjJjj.yjttfoYIzaep8SdW.lvzlh58ufO6mze', 2),
	(5, 'juan', 'perez', '87654321', 'juan@email.com', '3446554433', '$2y$10$ZzWmOgzQC1Lez0xGu4N87.7mhkA4gRxUkOhO1bC4Lm0dBavU/rz3a', 2),
	(6, 'pepe', 'argento', '23445677', 'pepe@gmail.com', '3446223123', '$2y$10$MfcLKpvYrTo9S4q5W4cATes9qU0rswWcKpFeBUefhP6XJiHH95C6y', 2),
	(7, 'magali', 'gutierrez', '45689007', 'magali@gmail.com', '3446361071', '$2y$10$syqSO.hYBGn0Wa/awe9QoeQHEQtUUXgowe//ZocksBhxnpSsyZxXm', 2);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
