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

-- Volcando estructura para tabla sic.entidad_armas
CREATE TABLE IF NOT EXISTS `entidad_armas` (
  `ID_Arma` varchar(25) NOT NULL,
  `FK_Encabezado` varchar(25) NOT NULL,
  `NumeroDeOrden` int(5) NOT NULL,
  `AF_EsDeFabricacionCasera` tinyint(1) NOT NULL DEFAULT 0,
  `AF_TipoAF` varchar(50) NOT NULL DEFAULT 'Pistola',
  `AF_Marca` varchar(25) DEFAULT NULL,
  `AF_Modelo` varchar(25) DEFAULT NULL,
  `AF_Calibre` varchar(25) DEFAULT NULL,
  `AF_PoseeNumeracionVisible` tinyint(1) NOT NULL,
  `AF_NumeroDeSerie` varchar(25) DEFAULT NULL,
  `AF_UsuarioCreador` int(5) NOT NULL,
  `AF_FechaDeCreacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID_Arma`),
  KEY `AF_UsuarioCreador` (`AF_UsuarioCreador`),
  KEY `FK_IP` (`FK_Encabezado`) USING BTREE,
  CONSTRAINT `AF_FK` FOREIGN KEY (`FK_Encabezado`) REFERENCES `entidad_encabezado` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `AF_UsuarioCreador` FOREIGN KEY (`AF_UsuarioCreador`) REFERENCES `sistema_usuarios` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla sic.entidad_datos_complementarios
CREATE TABLE IF NOT EXISTS `entidad_datos_complementarios` (
  `ID_DatoComplementario` varchar(25) NOT NULL,
  `FK_Persona` varchar(25) DEFAULT NULL,
  `FK_Lugar` varchar(25) DEFAULT NULL,
  `FK_Vehiculo` varchar(25) DEFAULT NULL,
  `FK_Arma` varchar(25) DEFAULT NULL,
  `NumeroDeOrden` int(5) NOT NULL,
  `DC_Tipo` varchar(100) NOT NULL DEFAULT 'Otro dato complementario',
  `DC_ImagenAdjunta` mediumtext DEFAULT NULL,
  `DC_Comentario` text DEFAULT NULL,
  `DC_UsuarioCreador` int(5) NOT NULL,
  `DC_FechaDeCreacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID_DatoComplementario`),
  KEY `FK_Persona` (`FK_Persona`),
  KEY `FK_Lugar` (`FK_Lugar`),
  KEY `FK_Vehiculo` (`FK_Vehiculo`) USING BTREE,
  KEY `FK_Arma` (`FK_Arma`) USING BTREE,
  KEY `DC_UsuarioCreador` (`DC_UsuarioCreador`) USING BTREE,
  CONSTRAINT `DC_FK_Armas` FOREIGN KEY (`FK_Arma`) REFERENCES `entidad_armas` (`ID_Arma`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `DC_FK_Lugares` FOREIGN KEY (`FK_Lugar`) REFERENCES `entidad_lugares` (`ID_Lugar`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `DC_FK_Personas` FOREIGN KEY (`FK_Persona`) REFERENCES `entidad_personas` (`ID_Persona`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `DC_FK_Vehiculos` FOREIGN KEY (`FK_Vehiculo`) REFERENCES `entidad_vehiculos` (`ID_Vehiculo`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `DC_UsuarioCreador` FOREIGN KEY (`DC_UsuarioCreador`) REFERENCES `sistema_usuarios` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla sic.entidad_encabezado
CREATE TABLE IF NOT EXISTS `entidad_encabezado` (
  `ID` varchar(25) NOT NULL,
  `Fecha` date NOT NULL,
  `Tipo` varchar(50) NOT NULL DEFAULT 'current_timestamp()',
  `Juzgado` varchar(50) NOT NULL DEFAULT 'current_timestamp()',
  `Dependencia` varchar(50) NOT NULL DEFAULT 'current_timestamp()',
  `Causa` varchar(50) NOT NULL DEFAULT 'current_timestamp()',
  `Relato` text NOT NULL,
  `UsuarioCreador` int(5) NOT NULL,
  `FechaDeCreacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`) USING BTREE,
  KEY `IP_UsuarioCreador` (`UsuarioCreador`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla sic.entidad_lugares
CREATE TABLE IF NOT EXISTS `entidad_lugares` (
  `ID_Lugar` varchar(25) NOT NULL,
  `FK_Encabezado` varchar(25) DEFAULT NULL,
  `FK_Persona` varchar(25) DEFAULT NULL,
  `NumeroDeOrden` int(5) NOT NULL,
  `L_Rol` varchar(100) NOT NULL DEFAULT '1',
  `L_TipoLugar` varchar(50) NOT NULL DEFAULT '',
  `L_NombreLugarEspecifico` varchar(50) DEFAULT NULL,
  `L_Calle` varchar(50) DEFAULT NULL,
  `L_AlturaCatastral` varchar(5) DEFAULT NULL,
  `L_CalleDetalle` varchar(50) DEFAULT NULL,
  `L_Interseccion1` varchar(50) DEFAULT NULL,
  `L_Interseccion2` varchar(50) DEFAULT NULL,
  `L_Barrio` varchar(50) DEFAULT NULL,
  `L_Localidad` varchar(50) NOT NULL DEFAULT 'Rosario',
  `L_Provincia` varchar(50) NOT NULL DEFAULT 'Santa Fe',
  `L_Pais` varchar(50) NOT NULL DEFAULT 'ARGENTINA',
  `L_Coordenadas` varchar(50) DEFAULT NULL,
  `L_UsuarioCreador` int(5) NOT NULL,
  `L_FechaDeCreacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID_Lugar`),
  KEY `FK_Encabezado` (`FK_Encabezado`),
  KEY `FK_Persona` (`FK_Persona`),
  KEY `L_UsuarioCreador` (`L_UsuarioCreador`) USING BTREE,
  CONSTRAINT `Lugar_FK_Encabezado` FOREIGN KEY (`FK_Encabezado`) REFERENCES `entidad_encabezado` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Lugar_FK_Persona` FOREIGN KEY (`FK_Persona`) REFERENCES `entidad_personas` (`ID_Persona`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Lugar_UsuarioCreador` FOREIGN KEY (`L_UsuarioCreador`) REFERENCES `sistema_usuarios` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla sic.entidad_personas
CREATE TABLE IF NOT EXISTS `entidad_personas` (
  `ID_Persona` varchar(25) NOT NULL,
  `FK_Encabezado` varchar(25) NOT NULL,
  `NumeroDeOrden` int(5) NOT NULL,
  `P_Rol` varchar(100) NOT NULL DEFAULT '1',
  `P_FotoPersona` text DEFAULT NULL,
  `P_Apellido` varchar(50) NOT NULL,
  `P_Nombre` varchar(50) NOT NULL,
  `P_Alias` varchar(50) DEFAULT NULL,
  `P_Genero` varchar(25) NOT NULL DEFAULT '1',
  `P_DNI` varchar(10) DEFAULT NULL,
  `P_Edad` varchar(5) DEFAULT NULL,
  `P_EstadoCivil` varchar(50) NOT NULL DEFAULT '1',
  `P_Pais` varchar(50) NOT NULL DEFAULT 'ARGENTINA',
  `P_UsuarioCreador` int(5) NOT NULL,
  `P_FechaDeCreacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID_Persona`),
  KEY `FK_Encabezado` (`FK_Encabezado`) USING BTREE,
  KEY `P_UsuarioCreador` (`P_UsuarioCreador`) USING BTREE,
  CONSTRAINT `P_FK_Encabezado` FOREIGN KEY (`FK_Encabezado`) REFERENCES `entidad_encabezado` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `P_UsuarioCreador` FOREIGN KEY (`P_UsuarioCreador`) REFERENCES `sistema_usuarios` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla sic.entidad_vehiculos
CREATE TABLE IF NOT EXISTS `entidad_vehiculos` (
  `ID_Vehiculo` varchar(25) NOT NULL,
  `FK_Encabezado` varchar(25) NOT NULL,
  `NumeroDeOrden` int(2) NOT NULL,
  `V_Rol` varchar(100) NOT NULL DEFAULT '',
  `V_TipoVehiculo` varchar(50) NOT NULL DEFAULT '',
  `V_Color` varchar(25) DEFAULT NULL,
  `V_Marca` varchar(25) DEFAULT NULL,
  `V_Modelo` varchar(25) DEFAULT NULL,
  `V_Año` varchar(4) DEFAULT NULL,
  `V_Dominio` varchar(20) DEFAULT NULL,
  `V_NumeroChasis` varchar(25) DEFAULT NULL,
  `V_NumeroMotor` varchar(25) DEFAULT NULL,
  `V_UsuarioCreador` int(5) NOT NULL,
  `V_FechaDeCreacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID_Vehiculo`),
  KEY `V_UsuarioCreador` (`V_UsuarioCreador`),
  KEY `V_FK_Encabezado` (`FK_Encabezado`) USING BTREE,
  CONSTRAINT `V_FK_Encabezado` FOREIGN KEY (`ID_Vehiculo`) REFERENCES `entidad_encabezado` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `V_UsuarioCreador` FOREIGN KEY (`V_UsuarioCreador`) REFERENCES `sistema_usuarios` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla sic.lista_roles_de_usuarios
CREATE TABLE IF NOT EXISTS `lista_roles_de_usuarios` (
  `ID` int(1) NOT NULL AUTO_INCREMENT,
  `Roles` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla sic.sistema_planilla_infractores
CREATE TABLE IF NOT EXISTS `sistema_planilla_infractores` (
  `ID` varchar(10) NOT NULL,
  `Numero` int(5) NOT NULL,
  `Año` int(4) NOT NULL,
  `Tipo` varchar(100) NOT NULL DEFAULT '',
  `Estado` tinyint(1) NOT NULL DEFAULT 1,
  `FechaDeCreacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `UsuarioCreador` int(5) NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  KEY `UsuarioCreador` (`UsuarioCreador`),
  KEY `TipoHecho` (`Tipo`) USING BTREE,
  CONSTRAINT `S_UsuarioCreador` FOREIGN KEY (`UsuarioCreador`) REFERENCES `sistema_usuarios` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla sic.sistema_usuarios
CREATE TABLE IF NOT EXISTS `sistema_usuarios` (
  `ID` int(5) NOT NULL AUTO_INCREMENT,
  `Usuario` varchar(50) NOT NULL,
  `Contraseña` varchar(255) NOT NULL,
  `Rol_del_usuario` int(1) NOT NULL,
  `Apellido_Operador` varchar(50) NOT NULL,
  `Nombre_Operador` varchar(50) NOT NULL,
  `NI_Operador` int(10) NOT NULL,
  `Estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Usuarios_Siacip` (`Usuario`),
  KEY `Rol_del_usuario` (`Rol_del_usuario`),
  CONSTRAINT `sistema_usuarios_ibfk_1` FOREIGN KEY (`Rol_del_usuario`) REFERENCES `lista_roles_de_usuarios` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
