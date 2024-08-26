-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.28-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.5.0.6677
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para sistema_usuarios
CREATE DATABASE IF NOT EXISTS `sistema_usuarios` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `sistema_usuarios`;

-- Volcando estructura para tabla sistema_usuarios.sistema_usuarios
CREATE TABLE IF NOT EXISTS `sistema_usuarios` (
  `ID` int(5) NOT NULL AUTO_INCREMENT,
  `Usuario` varchar(50) NOT NULL,
  `Contraseña` varchar(255) NOT NULL,
  `Rol` int(1) NOT NULL,
  `Grupo` varchar(25) NOT NULL DEFAULT 'SIACIP',
  `Apellido_Operador` varchar(50) NOT NULL,
  `Nombre_Operador` varchar(50) NOT NULL,
  `NI_Operador` int(10) NOT NULL,
  `Estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE KEY `Usuarios` (`Usuario`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- Volcando datos para la tabla sistema_usuarios.sistema_usuarios: ~4 rows (aproximadamente)
INSERT INTO `sistema_usuarios` (`ID`, `Usuario`, `Contraseña`, `Rol`, `Grupo`, `Apellido_Operador`, `Nombre_Operador`, `NI_Operador`, `Estado`) VALUES
	(1, 'SYSADMIN', '$2y$10$aDgsZ.s7f.Ap81AxWnBMReKajBviUuT.cBLqgOSQKitfJi/WTkakO', 1, 'ADMINISTRADOR', 'CAVALLI', 'Martín Raúl', 743526, 1),
	(2, 'NICOLAS', '$2y$10$sIKmoBSxhsX4J0xqwlnnv.66FC0Q5H.knz0R6H51AluXLWbVcljAe', 3, 'MICROCRIMEN', 'SIARCZYÑSKI', 'Nicolas', 786314, 1),
	(3, 'VRIQUELME', '$2y$10$XMjfAhCUUwMVI0RFUyiFTO8JGPyK9ooX3v7B/E8D3fmOJ5U4hsIom', 3, 'MICROCRIMEN', 'RUIZ', 'Jonatan', 786241, 1),
	(4, 'DCORDOVA', '$2y$10$Cci0QBozhcCeR6gOABB89.oeeNWXsHhIWwhd2ook98zD4DkkXpNfW', 3, 'MICROCRIMEN', 'MERET', 'Micaela', 785962, 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
