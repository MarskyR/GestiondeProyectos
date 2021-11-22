-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-05-2021 a las 01:02:34
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
-- Estructura de tabla para la tabla `detalle_dj_gastosmovilidad`
--

CREATE TABLE `detalle_dj_gastosmovilidad` (
  `codDetalleMovilidad` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `lugar` varchar(200) NOT NULL,
  `detalle` varchar(200) NOT NULL,
  `importe` float NOT NULL,
  `codDJGastosMovilidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_dj_gastosvarios`
--

CREATE TABLE `detalle_dj_gastosvarios` (
  `codDetalleVarios` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `concepto` varchar(200) NOT NULL,
  `importe` float NOT NULL,
  `codDJGastosVarios` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_dj_gastosviaticos`
--

CREATE TABLE `detalle_dj_gastosviaticos` (
  `codDetalleViaticos` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `lugar` varchar(200) NOT NULL,
  `montoDesayuno` float NOT NULL,
  `montoAlmuerzo` float NOT NULL,
  `montoCena` float NOT NULL,
  `totalDia` float NOT NULL,
  `codDJGastosViaticos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dj_gastosmovilidad`
--

CREATE TABLE `dj_gastosmovilidad` (
  `fechaHoraCreacion` datetime NOT NULL,
  `domicilio` varchar(200) NOT NULL,
  `importeTotal` float NOT NULL,
  `codMoneda` int(11) NOT NULL,
  `codEmpleado` int(11) NOT NULL,
  `codDJGastosMovilidad` int(11) NOT NULL,
  `codigoCedepas` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dj_gastosvarios`
--

CREATE TABLE `dj_gastosvarios` (
  `codDJGastosVarios` int(11) NOT NULL,
  `fechaHoraCreacion` datetime NOT NULL,
  `domicilio` varchar(200) NOT NULL,
  `importeTotal` float NOT NULL,
  `codMoneda` int(11) NOT NULL,
  `codEmpleado` int(11) NOT NULL,
  `codigoCedepas` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dj_gastosviaticos`
--

CREATE TABLE `dj_gastosviaticos` (
  `codDJGastosViaticos` int(11) NOT NULL,
  `fechaHoraCreacion` datetime NOT NULL,
  `domicilio` varchar(200) NOT NULL,
  `importeTotal` float NOT NULL,
  `codMoneda` int(11) NOT NULL,
  `codEmpleado` int(11) NOT NULL,
  `codigoCedepas` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_dj_gastosmovilidad`
--
ALTER TABLE `detalle_dj_gastosmovilidad`
  ADD PRIMARY KEY (`codDetalleMovilidad`);

--
-- Indices de la tabla `detalle_dj_gastosvarios`
--
ALTER TABLE `detalle_dj_gastosvarios`
  ADD PRIMARY KEY (`codDetalleVarios`);

--
-- Indices de la tabla `detalle_dj_gastosviaticos`
--
ALTER TABLE `detalle_dj_gastosviaticos`
  ADD PRIMARY KEY (`codDetalleViaticos`);

--
-- Indices de la tabla `dj_gastosmovilidad`
--
ALTER TABLE `dj_gastosmovilidad`
  ADD PRIMARY KEY (`codDJGastosMovilidad`);

--
-- Indices de la tabla `dj_gastosvarios`
--
ALTER TABLE `dj_gastosvarios`
  ADD PRIMARY KEY (`codDJGastosVarios`);

--
-- Indices de la tabla `dj_gastosviaticos`
--
ALTER TABLE `dj_gastosviaticos`
  ADD PRIMARY KEY (`codDJGastosViaticos`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_dj_gastosmovilidad`
--
ALTER TABLE `detalle_dj_gastosmovilidad`
  MODIFY `codDetalleMovilidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_dj_gastosvarios`
--
ALTER TABLE `detalle_dj_gastosvarios`
  MODIFY `codDetalleVarios` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_dj_gastosviaticos`
--
ALTER TABLE `detalle_dj_gastosviaticos`
  MODIFY `codDetalleViaticos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `dj_gastosmovilidad`
--
ALTER TABLE `dj_gastosmovilidad`
  MODIFY `codDJGastosMovilidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `dj_gastosvarios`
--
ALTER TABLE `dj_gastosvarios`
  MODIFY `codDJGastosVarios` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `dj_gastosviaticos`
--
ALTER TABLE `dj_gastosviaticos`
  MODIFY `codDJGastosViaticos` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
