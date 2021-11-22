-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-04-2021 a las 06:25:16
-- Versión del servidor: 5.7.32-log
-- Versión de PHP: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `maracsof_cedepas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banco`
--

CREATE TABLE `banco` (
  `codBanco` int(11) NOT NULL,
  `nombreBanco` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `banco`
--

INSERT INTO `banco` (`codBanco`, `nombreBanco`) VALUES
(1, 'BCP'),
(2, 'Interbank'),
(3, 'BBVA'),
(4, 'Banco de la Nacion'),
(5, 'Pichincha');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cdp`
--

CREATE TABLE `cdp` (
  `codTipoCDP` int(11) NOT NULL,
  `nombreCDP` varchar(200) NOT NULL,
  `codigoSUNAT` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `cdp`
--

INSERT INTO `cdp` (`codTipoCDP`, `nombreCDP`, `codigoSUNAT`) VALUES
(1, 'Fact.', 1),
(2, 'Rec. Hon.', 2),
(3, 'Bol. Venta', 3),
(4, 'Liq. Compra', 4),
(5, 'Boleto Aéreo', 5),
(6, 'Rec. Alquiler', 10),
(7, 'Ticket', 12),
(8, 'Rec. Serv. Pub', 14),
(9, 'Boleto Trans Publico', 15),
(10, 'Boleto Inteprovincial', 16),
(11, 'DJ Mov', 0),
(12, 'DJ Viat', 0),
(13, 'DJ Varios', 0),
(14, 'Otros', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_rendicion_gastos`
--

CREATE TABLE `detalle_rendicion_gastos` (
  `codDetalleRendicion` int(11) NOT NULL,
  `codRendicionGastos` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `nroComprobante` varchar(200) NOT NULL,
  `concepto` varchar(500) NOT NULL,
  `importe` float NOT NULL,
  `codigoPresupuestal` varchar(200) NOT NULL,
  `codTipoCDP` int(11) NOT NULL,
  `terminacionArchivo` varchar(10) DEFAULT NULL,
  `nroEnRendicion` int(11) NOT NULL,
  `contabilizado` tinyint(4) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_reposicion_gastos`
--

CREATE TABLE `detalle_reposicion_gastos` (
  `codDetalleReposicion` int(11) NOT NULL,
  `codReposicionGastos` int(11) NOT NULL,
  `fechaComprobante` date NOT NULL,
  `nroComprobante` varchar(50) NOT NULL,
  `concepto` varchar(200) NOT NULL,
  `importe` float NOT NULL,
  `codigoPresupuestal` varchar(50) NOT NULL,
  `nroEnReposicion` int(11) NOT NULL,
  `codTipoCDP` int(11) NOT NULL,
  `contabilizado` tinyint(4) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_solicitud_fondos`
--

CREATE TABLE `detalle_solicitud_fondos` (
  `codDetalleSolicitud` int(11) NOT NULL,
  `codSolicitud` int(11) NOT NULL,
  `nroItem` int(11) NOT NULL,
  `concepto` varchar(200) NOT NULL,
  `importe` float NOT NULL,
  `codigoPresupuestal` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `codEmpleado` int(11) NOT NULL,
  `codUsuario` int(11) NOT NULL,
  `codigoCedepas` varchar(50) NOT NULL,
  `nombres` varchar(300) NOT NULL,
  `apellidos` varchar(300) NOT NULL,
  `correo` varchar(60) NOT NULL,
  `dni` char(8) NOT NULL,
  `codPuesto` int(11) NOT NULL,
  `activo` int(11) NOT NULL,
  `fechaRegistro` date NOT NULL,
  `fechaDeBaja` date DEFAULT NULL,
  `codSede` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`codEmpleado`, `codUsuario`, `codigoCedepas`, `nombres`, `apellidos`, `correo`, `dni`, `codPuesto`, `activo`, `fechaRegistro`, `fechaDeBaja`, `codSede`) VALUES
(0, 0, 'E0000', 'admin', 'admin', 'admin@maracsoft.com', '71208489', 1, 1, '2021-03-09', '2021-03-19', 1),
(1, 1, 'E0428', 'FAUSTO GILMER', 'ALARCON ROJAS', 'falarcon@cedepas.org.pe', '40556946', 4, 1, '2021-03-29', '2060-03-29', 0),
(2, 2, 'E0727', 'PAULA', 'ALIAGA RODRIGUEZ', 'paliaga@cedepas.org.pe', '46636006', 4, 1, '2021-03-29', '2060-03-29', 0),
(3, 3, 'E0668', 'GIANLUIGUI BRYAN', 'ALVARADO VELIZ', 'galvarado@cedepas.org.pe', '47541289', 4, 1, '2021-03-29', '2060-03-29', 0),
(4, 4, 'E0004', 'ANA CECILIA', 'ANGULO ALVA', 'aangulo@cedepas.org.pe', '26682689', 1, 1, '2021-03-29', '2060-03-29', 0),
(5, 5, 'E0306', 'JANET', 'APAESTEGUI BUSTAMANTE', 'japaestegui@cedepas.org.pe', '41943357', 4, 1, '2021-03-29', '2060-03-29', 0),
(6, 6, 'E0674', 'HUBERT RICHARD', 'APARCO HUAMAN', 'raparco@cedepas.org.pe', '43485279', 4, 1, '2021-03-29', '2060-03-29', 0),
(7, 7, 'E0435', 'JUDITH VERONICA', 'AVILA JORGE', 'javila@cedepas.org.pe', '42090409', 4, 1, '2021-03-29', '2060-03-29', 0),
(8, 8, 'E0726', 'MERY JAHAIRA', 'BENITES OBESO', 'mbenites@cedepas.org.pe', '44847934', 1, 1, '2021-03-29', '2060-03-29', 0),
(9, 9, 'E0149', 'MARYCRUZ ROCÍO', 'BRIONES ORDOÑEZ', 'mbriones@cedepas.org.pe', '26682687', 3, 1, '2021-03-29', '2060-03-29', 0),
(10, 10, 'E0103', 'MELVA VIRGINIA', 'CABRERA TEJADA', 'mcabrera@cedepas.org.pe', '17914644', 4, 1, '2021-03-29', '2060-03-29', 0),
(11, 11, 'E0729', 'HINDIRA KATERINE', 'CASTAÑEDA ALFARO', 'hcastaneda@cedepas.org.pe', '70355561', 2, 1, '2021-03-29', '2060-03-29', 0),
(12, 12, 'E0787', 'WILSON EDGAR', 'COTRINA MEGO', 'wcotrina@cedepas.org.pe', '70585629', 1, 1, '2021-03-29', '2060-03-29', 0),
(13, 13, 'E0267', 'ROXANA MELISSA', 'DONET PAREDES', 'mdonet@cedepas.org.pe', '44685699', 1, 1, '2021-03-29', '2060-03-29', 0),
(14, 14, 'E0075', 'SANTOS ROSARIO', 'ESCOBEDO SANCHEZ', 'sescobedo@cedepas.org.pe', '19327774', 1, 1, '2021-03-29', '2060-03-29', 0),
(15, 15, 'E0177', 'JACQUELINE', 'GARCIA ESPINOZA', 'jgarcia@cedepas.org.pe', '40360154', 3, 1, '2021-03-29', '2060-03-29', 0),
(16, 16, 'E0716', 'GABY SHARON', 'HUANCA MAMANI', 'gshuanca@cedepas.org.pe', '45740336', 4, 1, '2021-03-29', '2060-03-29', 0),
(17, 17, 'E0677', 'CARLOS RICARDO', 'LEON LUTGARDO', 'cleon@cedepas.org.pe', '15738099', 2, 1, '2021-03-29', '2060-03-29', 0),
(18, 18, 'E0269', 'JUAN CARLOS', 'LEON SAUCEDO', 'jleon@cedepas.org.pe', '19330869', 4, 1, '2021-03-29', '2060-03-29', 0),
(19, 19, 'E0679', 'CRISTELL FRANCCESCA', 'LINO ZANONI', 'clino@cedepas.org.pe', '74240802', 4, 1, '2021-03-29', '2060-03-29', 0),
(20, 20, 'E0718', 'EDWAR LUIS', 'LIZARRAGA ALVAREZ', 'elizarraga@cedepas.org.pe', '70386230', 4, 1, '2021-03-29', '2060-03-29', 0),
(21, 21, 'E0641', 'CYNTHIA ESPERANZA', 'LOPEZ PRADO', 'clopez@cedepas.org.pe', '42927000', 4, 1, '2021-03-29', '2060-03-29', 0),
(22, 22, 'E0286', 'ROSSMERY LUZ', 'MARTINEZ OBANDO', 'rmartinez@cedepas.org.pe', '42305800', 4, 1, '2021-03-29', '2060-03-29', 0),
(23, 23, 'E0454', 'CARMEN CECILIA', 'MOLLEAPASA PASTOR', 'cmolleapasa@cedepas.org.pe', '15766143', 3, 1, '2021-03-29', '2060-03-29', 0),
(24, 24, 'E0612', 'CAROLYN LILIANA', 'MORENO PEREZ', 'cmoreno@cedepas.org.pe', '45540460', 4, 1, '2021-03-29', '2060-03-29', 0),
(25, 25, 'E0703', 'KELY EUSEBIA', 'MULLER TITO', 'kmuller@cedepas.org.pe', '45372425', 4, 1, '2021-03-29', '2060-03-29', 0),
(26, 26, 'E0195', 'SEGUNDO EDGARDO', 'OBANDO PINTADO', 'sobando@cedepas.org.pe', '3120627', 1, 1, '2021-03-29', '2060-03-29', 0),
(27, 27, 'E0721', 'ELVIS', 'ORRILLO MAYTA', 'eorillo@cedepas.org.pe', '45576187', 4, 1, '2021-03-29', '2060-03-29', 0),
(28, 28, 'E0159', 'SANTOS ABELARDO', 'PEREDA LUIS', 'spereda@cedepas.org.pe', '17877014', 4, 1, '2021-03-29', '2060-03-29', 0),
(29, 29, 'E0397', 'KARLHOS MARCO', 'QUINDE RODRIGUEZ', 'kquinde@cedepas.org.pe', '2897932', 1, 1, '2021-03-29', '2060-03-29', 0),
(30, 30, 'E0510', 'MILAGROS', 'QUIROZ TORREJON', 'mquiroz@cedepas.org.pe', '44155217', 1, 1, '2021-03-29', '2060-03-29', 0),
(31, 31, 'E0084', 'RONY AQUILES', 'RODRIGUEZ ROMERO', 'rrodriguez@cedepas.org.pe', '18175358', 4, 1, '2021-03-29', '2060-03-29', 0),
(32, 32, 'E0181', 'DANIEL', 'RODRIGUEZ RUIZ', 'drodriguez@cedepas.org.pe', '40068481', 4, 1, '2021-03-29', '2060-03-29', 0),
(33, 33, 'E0063', 'JANET JACQUELINE', 'ROJAS GONZALEZ', 'jrojas@cedepas.org.pe', '18126610', 2, 1, '2021-03-29', '2060-03-29', 0),
(34, 34, 'E0593', 'RICHARD JAVIER', 'ROSILLO ASTUDILLO', 'rrosillo@cedepas.org.pe', '43162714', 4, 1, '2021-03-29', '2060-03-29', 0),
(35, 35, 'E0390', 'TANIA JULISSA', 'RUIZ CORNEJO', 'truiz@cedepas.org.pe', '40392458', 2, 1, '2021-03-29', '2060-03-29', 0),
(36, 36, 'E0092', 'CINTHIA CAROLYN', 'SANCHEZ RAMIREZ', 'csanchez@cedepas.org.pe', '40242073', 3, 1, '2021-03-29', '2060-03-29', 0),
(37, 37, 'E0524', 'NELIDA RICARDINA', 'SERIN CRUZADO', 'nserin@cedepas.org.pe', '40994213', 3, 1, '2021-03-29', '2060-03-29', 0),
(38, 38, 'E0704', 'JUAN CARLOS', 'SILVA COTRINA', 'jsilva@cedepas.org.pe', '42122048', 4, 1, '2021-03-29', '2060-03-29', 0),
(39, 39, 'E0568', 'JUANA ROSA', 'URIOL VILLALOBOS', 'juriol@cedepas.org.pe', '44896824', 4, 1, '2021-03-29', '2060-03-29', 0),
(40, 40, 'E0763', 'CARLOS ANIBAL', 'VILCA CHAVEZ', 'cvilca@cedepas.org.pe', '46352412', 4, 1, '2021-03-29', '2060-03-29', 0),
(41, 41, 'E0765', 'JAVIER OSMAR', 'VILLENA RAMOS', 'jvillena@cedepas.org.pe', '43953715', 4, 1, '2021-03-29', '2060-03-29', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_rendicion_gastos`
--

CREATE TABLE `estado_rendicion_gastos` (
  `codEstadoRendicion` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `estado_rendicion_gastos`
--

INSERT INTO `estado_rendicion_gastos` (`codEstadoRendicion`, `nombre`) VALUES
(0, 'Momentaneo'),
(1, 'Creada'),
(2, 'Aprobada'),
(3, 'Contabilizada'),
(4, 'Observada'),
(5, 'Subsanada'),
(6, 'Rechazada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_reposicion_gastos`
--

CREATE TABLE `estado_reposicion_gastos` (
  `codEstadoReposicion` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `estado_reposicion_gastos`
--

INSERT INTO `estado_reposicion_gastos` (`codEstadoReposicion`, `nombre`) VALUES
(1, 'Creada'),
(2, 'Aprobada'),
(3, 'Abonada'),
(4, 'Contabilizada'),
(5, 'Observada'),
(6, 'Subsanada'),
(7, 'Rechazada'),
(8, 'Cancelada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_solicitud_fondos`
--

CREATE TABLE `estado_solicitud_fondos` (
  `codEstadoSolicitud` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estado_solicitud_fondos`
--

INSERT INTO `estado_solicitud_fondos` (`codEstadoSolicitud`, `nombre`) VALUES
(1, 'Creada'),
(2, 'Aprobada'),
(3, 'Abonada'),
(4, 'Contabilizada'),
(5, 'Observada'),
(6, 'Subsanada'),
(7, 'Rechazada'),
(8, 'Cancelada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `moneda`
--

CREATE TABLE `moneda` (
  `codMoneda` int(11) NOT NULL,
  `nombre` varchar(10) NOT NULL,
  `abreviatura` varchar(10) NOT NULL,
  `simbolo` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `moneda`
--

INSERT INTO `moneda` (`codMoneda`, `nombre`, `abreviatura`, `simbolo`) VALUES
(1, 'Soles', 'PEN', 'S/'),
(2, 'Dólares', 'USD', '$');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `numeracion`
--

CREATE TABLE `numeracion` (
  `codNumeracion` int(11) NOT NULL,
  `nombreDocumento` varchar(50) NOT NULL,
  `año` smallint(6) NOT NULL,
  `numeroLibreActual` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `numeracion`
--

INSERT INTO `numeracion` (`codNumeracion`, `nombreDocumento`, `año`, `numeroLibreActual`) VALUES
(1, 'Solicitud de Fondos', 2021, 1),
(2, 'Rendicion de Gastos', 2021, 1),
(3, 'Reposición de Gastos', 2021, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto`
--

CREATE TABLE `proyecto` (
  `codProyecto` int(11) NOT NULL,
  `codigoPresupuestal` varchar(5) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `codEmpleadoDirector` int(11) DEFAULT NULL,
  `activo` tinyint(4) NOT NULL,
  `codSedePrincipal` int(11) DEFAULT NULL,
  `nombreLargo` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `proyecto`
--

INSERT INTO `proyecto` (`codProyecto`, `codigoPresupuestal`, `nombre`, `codEmpleadoDirector`, `activo`, `codSedePrincipal`, `nombreLargo`) VALUES
(1, '5', 'CONST.MEJORES PRACTICAS - EITI', 0, 1, 1, 'x'),
(2, '6', 'INCREM.CAPAC.SOC CIVIL - FORD', 0, 1, 1, 'x'),
(3, '10', 'CONSULTORIAS III', 13, 1, 1, 'x'),
(4, '11', 'FONDOS ROTATORIOS LA LIBERTAD', 13, 1, 1, 'x'),
(5, '13', 'CONSULTORIAS NEXA', 12, 1, 1, 'x'),
(6, '14', 'CONSULTORIAS RAURA MINSUR', 12, 1, 1, 'x'),
(7, '16', 'BECAS-DESCO', 13, 1, 1, 'x'),
(8, '25', 'SHAHUINDO CUYES Y PALTAS', 4, 1, 1, 'x'),
(9, '26', 'MEJ.CADENA VALOR CUYES', 4, 1, 1, 'x'),
(10, '27', 'MEJ. SEGURIDAD ALIMENTARIA', 4, 1, 1, 'x'),
(11, '28', 'MEJ. SEGURIDAD ALIMENTARIA 3', 4, 1, 1, 'x'),
(12, '29', 'CONSULTORIAS VARIAS CAJAMARCA', 4, 1, 1, 'x'),
(13, '37', 'RIMISP', 26, 1, 1, 'x'),
(14, '43', 'CADENA DE BANANO ORGANICO', 26, 1, 1, 'x'),
(15, '44', 'CONTRAP. PROD.COMERCIO Y SERV.', 26, 1, 1, 'x'),
(16, '45', 'CONTRAP.LINEA AGROIN.INNOVADORA', 26, 1, 1, 'x'),
(17, '46', 'AUTOFINANCIAMIENTO PIURA', 26, 1, 1, 'x'),
(18, '48', 'NUEVA LINEA AGROIN.INNOVADORA', 29, 1, 1, 'x'),
(19, '54', 'PLAN ESTRATEGICO IV', 13, 1, 1, 'x'),
(20, '58', 'DEVOLUCION IGV LA LIBERTAD', 13, 1, 1, 'x'),
(21, '59', 'PROYECTO CON MANOS UNIDAS', 8, 1, 1, 'x'),
(22, '65', 'CITE III', 13, 1, 1, 'x'),
(23, '71', 'MEJ.CADENA PROD.QUINUA', 4, 1, 1, 'x'),
(24, '74', 'MEJ. SEGURIDAD ALIMENTARIA 2', 4, 1, 1, 'x'),
(25, '77', 'CONSULTORIAS LAREDO', 13, 1, 1, 'x'),
(26, '80', 'AUTOFINANCIAMIENTO CAJAMARCA', 4, 1, 1, 'x'),
(27, '81', 'AUTOFINANCIAMIENTO LA LIBERTAD', 13, 1, 1, 'x'),
(28, '83', 'CONTRAPARTIDA CITE III', 13, 1, 1, 'x'),
(29, '86', 'BRECHAS GENERO-FOREST', 13, 1, 1, 'x'),
(30, '87', 'DIPLOMADOS', 13, 1, 1, 'x'),
(31, '88', 'VIVERO SAN JOSE', 13, 1, 1, 'x'),
(32, '89', 'PY. FORESTAL SANTA CRUZ', 14, 1, 1, 'x'),
(33, '90', 'PROGRAMA MAD', 14, 1, 1, 'x'),
(34, '91', 'CONSOLIDACION COOPANORTE', 14, 1, 1, 'x'),
(35, '92', 'PROG.SERV.AGROP.MINERODUCTO', 14, 1, 1, 'x'),
(36, '93', 'CRIANZA DE ANIMALES MENORES JUPROG', 14, 1, 1, 'x'),
(37, '94', 'PY. PRODUCTIVO CHIQUIHUANCA', 14, 1, 1, 'x'),
(38, '95', 'PLAN CAP.LIDERES EN JUSTICIA FISCAL', 0, 1, 1, 'x'),
(39, '96', 'AUTOFINANCIAMIENTO GPC LIMA', 0, 1, 1, 'x'),
(40, '97', 'MEJ.FAMILIAS BOLOGNESI ANTAMINA', 14, 1, 1, 'x'),
(41, '98', 'PART.CIUD.RECONST.GOBERNANZA Y OTROS', 0, 1, 1, 'x');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto_contador`
--

CREATE TABLE `proyecto_contador` (
  `codProyectoContador` int(11) NOT NULL,
  `codEmpleadoContador` int(11) NOT NULL,
  `codProyecto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `proyecto_contador`
--

INSERT INTO `proyecto_contador` (`codProyectoContador`, `codEmpleadoContador`, `codProyecto`) VALUES
(1, 11, 1),
(2, 17, 1),
(3, 33, 1),
(4, 35, 1),
(5, 11, 2),
(6, 17, 2),
(7, 33, 2),
(8, 35, 2),
(9, 11, 3),
(10, 17, 3),
(11, 33, 3),
(12, 35, 3),
(13, 11, 4),
(14, 17, 4),
(15, 33, 4),
(16, 35, 4),
(17, 11, 5),
(18, 17, 5),
(19, 33, 5),
(20, 35, 5),
(21, 11, 6),
(22, 17, 6),
(23, 33, 6),
(24, 35, 6),
(25, 11, 7),
(26, 17, 7),
(27, 33, 7),
(28, 35, 7),
(29, 11, 8),
(30, 17, 8),
(31, 33, 8),
(32, 35, 8),
(33, 11, 9),
(34, 17, 9),
(35, 33, 9),
(36, 35, 9),
(37, 11, 10),
(38, 17, 10),
(39, 33, 10),
(40, 35, 10),
(41, 11, 11),
(42, 17, 11),
(43, 33, 11),
(44, 35, 11),
(45, 11, 12),
(46, 17, 12),
(47, 33, 12),
(48, 35, 12),
(49, 11, 13),
(50, 17, 13),
(51, 33, 13),
(52, 35, 13),
(53, 11, 14),
(54, 17, 14),
(55, 33, 14),
(56, 35, 14),
(57, 11, 15),
(58, 17, 15),
(59, 33, 15),
(60, 35, 15),
(61, 11, 16),
(62, 17, 16),
(63, 33, 16),
(64, 35, 16),
(65, 11, 17),
(66, 17, 17),
(67, 33, 17),
(68, 35, 17),
(69, 11, 18),
(70, 17, 18),
(71, 33, 18),
(72, 35, 18),
(73, 11, 19),
(74, 17, 19),
(75, 33, 19),
(76, 35, 19),
(77, 11, 20),
(78, 17, 20),
(79, 33, 20),
(80, 35, 20),
(81, 11, 21),
(82, 17, 21),
(83, 33, 21),
(84, 35, 21),
(85, 11, 22),
(86, 17, 22),
(87, 33, 22),
(88, 35, 22),
(89, 11, 23),
(90, 17, 23),
(91, 33, 23),
(92, 35, 23),
(93, 11, 24),
(94, 17, 24),
(95, 33, 24),
(96, 35, 24),
(97, 11, 25),
(98, 17, 25),
(99, 33, 25),
(100, 35, 25),
(101, 11, 26),
(102, 17, 26),
(103, 33, 26),
(104, 35, 26),
(105, 11, 27),
(106, 17, 27),
(107, 33, 27),
(108, 35, 27),
(109, 11, 28),
(110, 17, 28),
(111, 33, 28),
(112, 35, 28),
(113, 11, 29),
(114, 17, 29),
(115, 33, 29),
(116, 35, 29),
(117, 11, 30),
(118, 17, 30),
(119, 33, 30),
(120, 35, 30),
(121, 11, 31),
(122, 17, 31),
(123, 33, 31),
(124, 35, 31),
(125, 11, 32),
(126, 17, 32),
(127, 33, 32),
(128, 35, 32),
(129, 11, 33),
(130, 17, 33),
(131, 33, 33),
(132, 35, 33),
(133, 11, 34),
(134, 17, 34),
(135, 33, 34),
(136, 35, 34),
(137, 11, 35),
(138, 17, 35),
(139, 33, 35),
(140, 35, 35),
(141, 11, 36),
(142, 17, 36),
(143, 33, 36),
(144, 35, 36),
(145, 11, 37),
(146, 17, 37),
(147, 33, 37),
(148, 35, 37),
(149, 11, 38),
(150, 17, 38),
(151, 33, 38),
(152, 35, 38),
(153, 11, 39),
(154, 17, 39),
(155, 33, 39),
(156, 35, 39),
(157, 11, 40),
(158, 17, 40),
(159, 33, 40),
(160, 35, 40),
(161, 11, 41),
(162, 17, 41),
(163, 33, 41),
(164, 35, 41);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puesto`
--

CREATE TABLE `puesto` (
  `codPuesto` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `puesto`
--

INSERT INTO `puesto` (`codPuesto`, `nombre`, `estado`) VALUES
(0, 'Admin sistema', 0),
(1, 'Gerente', 0),
(2, 'Contador', 0),
(3, 'Administrador', 0),
(4, 'Empleado', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rendicion_gastos`
--

CREATE TABLE `rendicion_gastos` (
  `codRendicionGastos` int(11) NOT NULL,
  `codSolicitud` int(11) NOT NULL,
  `codMoneda` int(11) NOT NULL,
  `codigoCedepas` varchar(50) NOT NULL,
  `totalImporteRecibido` float DEFAULT NULL,
  `totalImporteRendido` float DEFAULT NULL,
  `saldoAFavorDeEmpleado` float DEFAULT NULL,
  `resumenDeActividad` varchar(200) NOT NULL,
  `codEstadoRendicion` int(11) NOT NULL,
  `fechaHoraRendicion` datetime DEFAULT NULL,
  `fechaHoraRevisado` datetime DEFAULT NULL,
  `observacion` varchar(500) DEFAULT NULL,
  `codEmpleadoSolicitante` int(11) NOT NULL,
  `codEmpleadoEvaluador` int(11) DEFAULT NULL,
  `codEmpleadoContador` int(11) DEFAULT NULL,
  `cantArchivos` int(11) DEFAULT NULL,
  `terminacionesArchivos` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reposicion_gastos`
--

CREATE TABLE `reposicion_gastos` (
  `codReposicionGastos` int(11) NOT NULL,
  `codEstadoReposicion` int(11) NOT NULL,
  `totalImporte` float DEFAULT NULL,
  `codProyecto` int(11) NOT NULL,
  `codMoneda` int(11) NOT NULL,
  `codigoCedepas` varchar(30) NOT NULL,
  `girarAOrdenDe` varchar(50) NOT NULL,
  `numeroCuentaBanco` varchar(100) NOT NULL,
  `codBanco` int(11) NOT NULL,
  `resumen` varchar(100) NOT NULL,
  `fechaHoraEmision` datetime NOT NULL,
  `codEmpleadoSolicitante` int(11) NOT NULL,
  `codEmpleadoEvaluador` int(11) DEFAULT NULL,
  `codEmpleadoAdmin` int(11) DEFAULT NULL,
  `codEmpleadoConta` int(11) DEFAULT NULL,
  `fechaHoraRevisionGerente` datetime DEFAULT NULL,
  `fechaHoraRevisionAdmin` datetime DEFAULT NULL,
  `fechaHoraRevisionConta` datetime DEFAULT NULL,
  `observacion` varchar(200) DEFAULT NULL,
  `cantArchivos` tinyint(4) DEFAULT NULL,
  `terminacionesArchivos` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sede`
--

CREATE TABLE `sede` (
  `codSede` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sede`
--

INSERT INTO `sede` (`codSede`, `nombre`) VALUES
(1, 'Trujillo'),
(2, 'Cajamarca'),
(4, 'Lima'),
(5, 'Santiago de Chuco');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_fondos`
--

CREATE TABLE `solicitud_fondos` (
  `codSolicitud` int(11) NOT NULL,
  `codProyecto` int(11) NOT NULL,
  `codigoCedepas` varchar(200) NOT NULL,
  `codEmpleadoSolicitante` int(11) NOT NULL,
  `fechaHoraEmision` datetime NOT NULL,
  `totalSolicitado` float DEFAULT NULL,
  `girarAOrdenDe` varchar(200) NOT NULL,
  `numeroCuentaBanco` varchar(200) NOT NULL,
  `codBanco` int(11) NOT NULL,
  `justificacion` varchar(500) DEFAULT NULL,
  `codEmpleadoEvaluador` int(11) DEFAULT NULL,
  `fechaHoraRevisado` datetime DEFAULT NULL,
  `codEstadoSolicitud` int(11) NOT NULL,
  `fechaHoraAbonado` datetime DEFAULT NULL,
  `observacion` varchar(300) DEFAULT NULL,
  `terminacionArchivo` varchar(10) DEFAULT NULL,
  `codEmpleadoAbonador` int(11) DEFAULT NULL,
  `estaRendida` int(11) DEFAULT '0',
  `codEmpleadoContador` int(11) DEFAULT NULL,
  `codMoneda` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `codUsuario` bigint(20) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `isAdmin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`codUsuario`, `usuario`, `password`, `isAdmin`) VALUES
(0, 'admin', '$2y$10$NT382fPkmou2YFXnAfN5V.DghGqNKhA5Ai/DycFWTIQ4dJKmlbXOu', 1),
(1, 'E0428', '$2y$10$YljPRsPhtw5WFBaTCASRT.Ir4oiJg.hnrr9HJMyTU7FkKL7s.0jPy', 0),
(2, 'E0727', '$2y$10$sOv11Z.pdA.VXT9F6xc9Vei8CDvTUyJzixcTCIoCpWNxQlu.bCpkK', 0),
(3, 'E0668', '$2y$10$/RWkviTNQUR74RtzV0/c/uU8b/VKUtpvID79T3CVIqUHLRWeXC3sa', 0),
(4, 'E0004', '$2y$10$6B2FMMXuOQr/yCDMkxALtuOuv7OQNx9waxIfoSNeLc5sWhu7Nh2Jm', 0),
(5, 'E0306', '$2y$10$0SEv392wFNwbh8pYLRRIruYxsYYqWTH8rA7057LTi4gRXP82Pps3G', 0),
(6, 'E0674', '$2y$10$mNsx4Ny6tEZPAF9x88Kbv.hq7UWz35IhrdrGplPaMYOXcT3WW2E3a', 0),
(7, 'E0435', '$2y$10$4K.niVIQpCVksBz6FYmWmeDR3PKWFrPw4//1OsyhkSmGsN7jXQsLG', 0),
(8, 'E0726', '$2y$10$uHX7suTvVYrJmnzGS3Wa6OBiMZOu9ljW6qsG8d5j0OGh/HmyR1Zhm', 0),
(9, 'E0149', '$2y$10$5FeSLEDosSWn0/RTRL9z.OU.MR/kiVPL1vgkznSxaviOQYhTirmg6', 0),
(10, 'E0103', '$2y$10$uc18NPQhQUOGiNJgFQQjVO1NiX5btQ4NF.b7CysAiLD.km04zOmwO', 0),
(11, 'E0729', '$2y$10$Qf.jyHOK3RBltzpEcdv7S.r/wkvAstoX732FfhagZmoTYpREUxhVi', 0),
(12, 'E0787', '$2y$10$iyOfwBFgn.JjWumsFXS2f.S1mLCQFxYgptQEmAsqhF6kJBw0IslAC', 0),
(13, 'E0267', '$2y$10$dX3mghlFuWa2odBaVzuAKOm4rmVq4jGqG7T21ASCsLnj3qZbmKCxS', 0),
(14, 'E0075', '$2y$10$dX8HbBBihyKcFWn3ewdNM.8afv.Ll3zysnkw3HVgrXYsbCIYiJ4fa', 0),
(15, 'E0177', '$2y$10$Yph54a54T6SpdD2Xees8TuRkaAXMnNxAACizg53.UmMIfc7ux2kr6', 0),
(16, 'E0716', '$2y$10$cNcVibkSDiUVy0ZV.TXgueai8dVV.DQnFiO6kfXjStUbg0D5CJHxq', 0),
(17, 'E0677', '$2y$10$NBB/fOD3wR2G.D3G3pWb1evFXDEyHY5LMVIAdGeJNDOXR14tTLgIC', 0),
(18, 'E0269', '$2y$10$sL5e/CIfFhAuTQS1aR83z.XFs7c1gGFToPYoKDpDouuRfMG7WtPvm', 0),
(19, 'E0679', '$2y$10$NCwDv0H4uL9zLGZIscZeO.Lv51Ub9j71zKa/9vpCf2/KSTxXv5Ms.', 0),
(20, 'E0718', '$2y$10$STVAzpu1XGTi28GrDFfxI.K22.y5CLXOOJc30kpyWIyVn1Jr2Lp3m', 0),
(21, 'E0641', '$2y$10$ljaiZxiP/oehs4.2HwjNmee0r04.ciNvJ6U1b2n9f1Fga8p.J2mf2', 0),
(22, 'E0286', '$2y$10$s1jzEZzLBq99YKLY3RtgB.K.RxPqLc5iaDBcA.eHC/f3zw9Tg7cLe', 0),
(23, 'E0454', '$2y$10$tY.2Q2kjnozcE0oX3r8bXuN35z4Anr/Io04gGKVbmYS5Ij376zeDO', 0),
(24, 'E0612', '$2y$10$zwhTljGfmgAHKoUOOtzLieFEeZtAIvxd97Ub8I4W9ekdut/uqfSV2', 0),
(25, 'E0703', '$2y$10$gmYvMJ2fLGeqS9fO1.f2U.Lo0A2kNynQq/orN5grK3wYDS7/aIirO', 0),
(26, 'E0195', '$2y$10$oKBtrWHq507UVIrrAIAaiOE/9jjRxuWiNTsrO/vpkIcn5Y19wnSUi', 0),
(27, 'E0721', '$2y$10$2Dwob02pPUCHAlchkzSaGOIaOv5p76.0.ELFhyQDotiZsTClNdB6m', 0),
(28, 'E0159', '$2y$10$TMqsur7Nc2CrVhsnxw2loOZVg4N0QE4qEzwOWKKebdeSkiiLWJkd6', 0),
(29, 'E0397', '$2y$10$eidazEzgCJEAsz1lC/RpNu8NE9wNtW4mWI76MVTflirCHylF42TBe', 0),
(30, 'E0510', '$2y$10$bXZpO0ZhEZNv/f4W2SOSWOGp72XCUOLXWdxktRIel7OscjCPBfOKq', 0),
(31, 'E0084', '$2y$10$b5q83QgZNtIPKMGy7fSTxeoCRLepBz0O.YE8eq/9GvTuE7lHdy5MW', 0),
(32, 'E0181', '$2y$10$ULX0VbX9LX.LPLTHdXmMOOXt48PkW/85ThPkvpAm6oTltU0JOEzOy', 0),
(33, 'E0063', '$2y$10$VtzXe24cViKh3SbGUPQbL.NG/st2qvFBwPGJGAbwbSJOfhHWKZOQW', 0),
(34, 'E0593', '$2y$10$lavJtTqVaoYCIYr80h8IJ.Ww6dCfvOvxb3edDLBgLSze9D626SjVO', 0),
(35, 'E0390', '$2y$10$N34EQDBFuVSvy1.17cugaOo00ZrCYA0FJeW52iNIwKWyl6sjNF2Zq', 0),
(36, 'E0092', '$2y$10$GL7c12QnyvCCQv.7Zr792O/inIWgTXQILMe.NgO1w3P9QbRLHmylq', 0),
(37, 'E0524', '$2y$10$xCxWWrMPKpw3lR2g7gk49eeP9ERpDD3YwritKzEzk/J7avKK.MHBO', 0),
(38, 'E0704', '$2y$10$kfRj/DV8FfcqF.PpS6Xnz./IzcrapZBRe.EHGcWdT5fnMneqdqMAu', 0),
(39, 'E0568', '$2y$10$va8ue1e/CGekWoQ6QrV/xOM5zbV6FYDD5fBhUJ9wGfzrbn9lgQYwy', 0),
(40, 'E0763', '$2y$10$lMi/qnv17ziI9Q2xVN0pKeiHZMkC/E4m2YqtQrhqf8oEWNOJa0L.S', 0),
(41, 'E0765', '$2y$10$4HA0tTZD/Y3/e26F7e6GreIlygUX/ULUZT2QpN3DoTcdhCmvPhk8a', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `banco`
--
ALTER TABLE `banco`
  ADD PRIMARY KEY (`codBanco`);

--
-- Indices de la tabla `cdp`
--
ALTER TABLE `cdp`
  ADD PRIMARY KEY (`codTipoCDP`);

--
-- Indices de la tabla `detalle_rendicion_gastos`
--
ALTER TABLE `detalle_rendicion_gastos`
  ADD PRIMARY KEY (`codDetalleRendicion`);

--
-- Indices de la tabla `detalle_reposicion_gastos`
--
ALTER TABLE `detalle_reposicion_gastos`
  ADD PRIMARY KEY (`codDetalleReposicion`);

--
-- Indices de la tabla `detalle_solicitud_fondos`
--
ALTER TABLE `detalle_solicitud_fondos`
  ADD PRIMARY KEY (`codDetalleSolicitud`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`codEmpleado`);

--
-- Indices de la tabla `estado_rendicion_gastos`
--
ALTER TABLE `estado_rendicion_gastos`
  ADD PRIMARY KEY (`codEstadoRendicion`);

--
-- Indices de la tabla `estado_reposicion_gastos`
--
ALTER TABLE `estado_reposicion_gastos`
  ADD PRIMARY KEY (`codEstadoReposicion`);

--
-- Indices de la tabla `estado_solicitud_fondos`
--
ALTER TABLE `estado_solicitud_fondos`
  ADD PRIMARY KEY (`codEstadoSolicitud`);

--
-- Indices de la tabla `moneda`
--
ALTER TABLE `moneda`
  ADD PRIMARY KEY (`codMoneda`);

--
-- Indices de la tabla `numeracion`
--
ALTER TABLE `numeracion`
  ADD PRIMARY KEY (`codNumeracion`);

--
-- Indices de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  ADD PRIMARY KEY (`codProyecto`);

--
-- Indices de la tabla `proyecto_contador`
--
ALTER TABLE `proyecto_contador`
  ADD PRIMARY KEY (`codProyectoContador`);

--
-- Indices de la tabla `puesto`
--
ALTER TABLE `puesto`
  ADD PRIMARY KEY (`codPuesto`);

--
-- Indices de la tabla `rendicion_gastos`
--
ALTER TABLE `rendicion_gastos`
  ADD PRIMARY KEY (`codRendicionGastos`);

--
-- Indices de la tabla `reposicion_gastos`
--
ALTER TABLE `reposicion_gastos`
  ADD PRIMARY KEY (`codReposicionGastos`);

--
-- Indices de la tabla `sede`
--
ALTER TABLE `sede`
  ADD PRIMARY KEY (`codSede`);

--
-- Indices de la tabla `solicitud_fondos`
--
ALTER TABLE `solicitud_fondos`
  ADD PRIMARY KEY (`codSolicitud`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`codUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `banco`
--
ALTER TABLE `banco`
  MODIFY `codBanco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cdp`
--
ALTER TABLE `cdp`
  MODIFY `codTipoCDP` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `detalle_rendicion_gastos`
--
ALTER TABLE `detalle_rendicion_gastos`
  MODIFY `codDetalleRendicion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_reposicion_gastos`
--
ALTER TABLE `detalle_reposicion_gastos`
  MODIFY `codDetalleReposicion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT de la tabla `detalle_solicitud_fondos`
--
ALTER TABLE `detalle_solicitud_fondos`
  MODIFY `codDetalleSolicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=354;

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `codEmpleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `estado_rendicion_gastos`
--
ALTER TABLE `estado_rendicion_gastos`
  MODIFY `codEstadoRendicion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `estado_reposicion_gastos`
--
ALTER TABLE `estado_reposicion_gastos`
  MODIFY `codEstadoReposicion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `estado_solicitud_fondos`
--
ALTER TABLE `estado_solicitud_fondos`
  MODIFY `codEstadoSolicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `moneda`
--
ALTER TABLE `moneda`
  MODIFY `codMoneda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `numeracion`
--
ALTER TABLE `numeracion`
  MODIFY `codNumeracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  MODIFY `codProyecto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `proyecto_contador`
--
ALTER TABLE `proyecto_contador`
  MODIFY `codProyectoContador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT de la tabla `puesto`
--
ALTER TABLE `puesto`
  MODIFY `codPuesto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `rendicion_gastos`
--
ALTER TABLE `rendicion_gastos`
  MODIFY `codRendicionGastos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reposicion_gastos`
--
ALTER TABLE `reposicion_gastos`
  MODIFY `codReposicionGastos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `sede`
--
ALTER TABLE `sede`
  MODIFY `codSede` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `solicitud_fondos`
--
ALTER TABLE `solicitud_fondos`
  MODIFY `codSolicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1785;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `codUsuario` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
