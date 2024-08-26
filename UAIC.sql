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


-- Volcando estructura de base de datos para uaic
CREATE DATABASE IF NOT EXISTS `uaic` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `uaic`;

-- Volcando estructura para tabla uaic.entidad_encabezado
CREATE TABLE IF NOT EXISTS `entidad_encabezado` (
  `Formulario` varchar(10) NOT NULL,
  `Numero` int(5) DEFAULT 0,
  `Año` int(4) DEFAULT 0,
  `Fecha` date DEFAULT NULL,
  `Hora` time DEFAULT NULL,
  `Fuente` varchar(5) DEFAULT NULL,
  `ReporteAsociado` varchar(50) DEFAULT NULL,
  `Tipologia` varchar(250) DEFAULT 'Sin asignar',
  `ModalidadComisiva` varchar(250) DEFAULT NULL,
  `TipoEstupefaciente` varchar(250) DEFAULT NULL,
  `Relevancia` varchar(10) DEFAULT NULL,
  `PosiblesUsurpaciones` tinyint(1) NOT NULL DEFAULT 0,
  `ConnivenciaPolicial` tinyint(1) NOT NULL DEFAULT 0,
  `UsoAF` tinyint(1) NOT NULL DEFAULT 0,
  `ParticipacionDeMenores` tinyint(1) NOT NULL DEFAULT 0,
  `ParticipacionOrgCrim` tinyint(1) NOT NULL DEFAULT 0,
  `OrganizacionCriminal` varchar(100) DEFAULT NULL,
  `Relato` text DEFAULT NULL,
  `Valoracion` text DEFAULT NULL,
  `UsuarioCreador` int(5) NOT NULL DEFAULT 1,
  `FechaDeCreacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`Formulario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla uaic.entidad_lugares
CREATE TABLE IF NOT EXISTS `entidad_lugares` (
  `ID_Lugar` varchar(15) NOT NULL,
  `FK_Encabezado` varchar(10) DEFAULT NULL,
  `NumeroDeOrden` int(5) NOT NULL,
  `L_Rol` varchar(100) NOT NULL DEFAULT 'Lugar mencionado',
  `L_Tipo` varchar(50) NOT NULL DEFAULT '',
  `L_SubTipo` varchar(50) DEFAULT '',
  `L_Calle` varchar(100) DEFAULT NULL,
  `L_AlturaCatastral` varchar(50) DEFAULT NULL,
  `L_CalleDetalle` varchar(50) DEFAULT NULL,
  `L_Interseccion1` varchar(50) DEFAULT NULL,
  `L_Interseccion2` varchar(50) DEFAULT NULL,
  `L_Barrio` varchar(50) DEFAULT NULL,
  `L_Localidad` varchar(50) DEFAULT NULL,
  `L_Provincia` varchar(50) NOT NULL DEFAULT 'Santa Fe',
  `L_Pais` varchar(50) NOT NULL DEFAULT 'ARGENTINA',
  `L_Coordenadas` varchar(50) DEFAULT NULL,
  `L_UsuarioCreador` int(5) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID_Lugar`) USING BTREE,
  KEY `FK_Encabezado` (`FK_Encabezado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla uaic.entidad_personas
CREATE TABLE IF NOT EXISTS `entidad_personas` (
  `ID_Persona` varchar(15) NOT NULL,
  `FK_Encabezado` varchar(10) NOT NULL DEFAULT '',
  `NumeroDeOrden` int(5) NOT NULL,
  `P_Rol` varchar(100) NOT NULL DEFAULT '1',
  `P_Apellido` varchar(50) NOT NULL,
  `P_Nombre` varchar(50) NOT NULL,
  `P_Alias` varchar(50) DEFAULT NULL,
  `P_UsuarioCreador` int(5) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID_Persona`) USING BTREE,
  KEY `FK_Encabezado` (`FK_Encabezado`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla uaic.entidad_vehiculos
CREATE TABLE IF NOT EXISTS `entidad_vehiculos` (
  `ID_Vehiculo` varchar(15) NOT NULL,
  `FK_Encabezado` varchar(10) NOT NULL DEFAULT '',
  `NumeroDeOrden` int(5) NOT NULL,
  `V_Rol` varchar(100) NOT NULL DEFAULT '',
  `V_Tipo` varchar(50) NOT NULL DEFAULT '',
  `V_Color` varchar(25) DEFAULT NULL,
  `V_Marca` varchar(25) DEFAULT NULL,
  `V_Modelo` varchar(25) DEFAULT NULL,
  `V_Dominio` varchar(25) DEFAULT NULL,
  `V_UsuarioCreador` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID_Vehiculo`),
  KEY `FK_Encabezado` (`FK_Encabezado`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
