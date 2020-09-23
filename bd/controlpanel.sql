-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 23, 2020 at 07:15 PM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `control`
--

-- --------------------------------------------------------

--
-- Table structure for table `bebidas_envases`
--

	
DROP DATABASE IF EXISTS controlpanel;
CREATE DATABASE controlpanel;
use controlpanel;

CREATE TABLE `bebidas_envases` (
  `ID_Envases` int(11) NOT NULL,
  `Envase` varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `bebidas_envases`
--

INSERT INTO `bebidas_envases` (`ID_Envases`, `Envase`) VALUES
(1, 'Botella de plástico'),
(2, 'Lata'),
(3, 'Cartón');

-- --------------------------------------------------------

--
-- Table structure for table `bebidas_sabores`
--

CREATE TABLE `bebidas_sabores` (
  `ID_Sabor` int(11) NOT NULL,
  `Sabor` varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `bebidas_sabores`
--

INSERT INTO `bebidas_sabores` (`ID_Sabor`, `Sabor`) VALUES
(1, 'Naranja'),
(2, 'Manzana'),
(3, 'Lima limón'),
(4, 'Pomelo'),
(5, 'Multifruta');

-- --------------------------------------------------------

--
-- Table structure for table `bebida_final`
--

CREATE TABLE `bebida_final` (
  `ID_Bebida` int(11) NOT NULL,
  `Marca` int(11) NOT NULL,
  `ID_Categoria` int(11) NOT NULL,
  `ID_Size` double NOT NULL,
  `Precio` double NOT NULL,
  `Sabor` int(11) DEFAULT NULL,
  `Envase` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `bebida_final`
--

INSERT INTO `bebida_final` (`ID_Bebida`, `Marca`, `ID_Categoria`, `ID_Size`, `Precio`, `Sabor`, `Envase`) VALUES
(1, 1, 1, 1, 30, 1, 1),
(2, 2, 1, 1, 45, 2, 1),
(3, 6, 2, 2, 60, 3, 1),
(5, 4, 2, 4, 40, 3, 2),
(6, 3, 1, 5, 20, 1, 3),
(7, 1, 1, 1, 30, 4, 1),
(8, 2, 1, 1, 45, 1, 1),
(9, 2, 1, 1, 45, 4, 1),
(10, 7, 2, 2, 60, 1, 1),
(11, 5, 2, 2, 60, NULL, 1),
(12, 8, 1, 3, 70, 2, 3),
(13, 9, 2, 4, 40, NULL, 2),
(14, 10, 2, 4, 40, 4, 2),
(15, 3, 1, 5, 20, 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `bebida_marca`
--

CREATE TABLE `bebida_marca` (
  `ID_Marca` int(11) NOT NULL,
  `Marca` varchar(20) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `bebida_marca`
--

INSERT INTO `bebida_marca` (`ID_Marca`, `Marca`) VALUES
(1, 'Placer'),
(2, 'Levite'),
(3, 'Pindapoy'),
(4, '7up'),
(5, 'Coca-cola'),
(6, 'Sprite'),
(7, 'Fanta'),
(8, 'Baggio'),
(9, 'Pepsi'),
(10, 'Paso de los toros');

-- --------------------------------------------------------

--
-- Table structure for table `bebida_tamaño`
--

CREATE TABLE `bebida_tamaño` (
  `ID_Size` int(11) NOT NULL,
  `Mililitros` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `bebida_tamaño`
--

INSERT INTO `bebida_tamaño` (`ID_Size`, `Mililitros`) VALUES
(1, 500),
(2, 600),
(3, 1000),
(4, 354),
(5, 200);

-- --------------------------------------------------------

--
-- Table structure for table `caja`
--

CREATE TABLE `caja` (
  `ID_Caja` int(11) NOT NULL,
  `ID_Sucursal` int(11) NOT NULL,
  `ID_Encargado` int(11) NOT NULL,
  `Total` double NOT NULL,
  `FH_Apertura` timestamp NOT NULL DEFAULT current_timestamp(),
  `FH_Cierre` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `caja`
--

INSERT INTO `caja` (`ID_Caja`, `ID_Sucursal`, `ID_Encargado`, `Total`, `FH_Apertura`, `FH_Cierre`) VALUES
(1, 1, 1, 2145, '2019-12-05 18:56:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categorias_productos`
--

CREATE TABLE `categorias_productos` (
  `ID_Categoria` int(11) NOT NULL,
  `ID_Producto` int(11) NOT NULL,
  `Categoria` varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `categorias_productos`
--

INSERT INTO `categorias_productos` (`ID_Categoria`, `ID_Producto`, `Categoria`) VALUES
(1, 1, 'Jugo'),
(2, 1, 'Gaseosa'),
(3, 2, 'Cono Chico'),
(4, 2, 'Cono grande'),
(5, 2, 'Cono gigante'),
(6, 2, 'Bandeja'),
(7, 3, 'Salsas'),
(8, 3, 'Salsas especiales'),
(9, 3, 'Salsas de la casa'),
(10, 2, 'Normal');

-- --------------------------------------------------------

--
-- Table structure for table `comida`
--

CREATE TABLE `comida` (
  `ID_Comida` int(11) NOT NULL,
  `Nombre` varchar(20) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `comida`
--

INSERT INTO `comida` (`ID_Comida`, `Nombre`) VALUES
(1, 'Papas fritas'),
(2, 'Panchos');

-- --------------------------------------------------------

--
-- Table structure for table `comida_final`
--

CREATE TABLE `comida_final` (
  `ID_ComidaFinal` int(11) NOT NULL,
  `ID_Comida` int(11) NOT NULL,
  `ID_Categoria` int(11) NOT NULL,
  `Precio` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `comida_final`
--

INSERT INTO `comida_final` (`ID_ComidaFinal`, `ID_Comida`, `ID_Categoria`, `Precio`) VALUES
(1, 1, 3, 60),
(2, 1, 4, 100),
(3, 1, 5, 120),
(4, 1, 6, 160),
(5, 2, 10, 30);

-- --------------------------------------------------------

--
-- Table structure for table `comida_usa`
--

CREATE TABLE `comida_usa` (
  `ID_Comida` int(11) NOT NULL,
  `ID_Mercaderia` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `comida_usa`
--

INSERT INTO `comida_usa` (`ID_Comida`, `ID_Mercaderia`, `Cantidad`) VALUES
(1, 21, 1),
(2, 20, 1),
(3, 19, 1),
(4, 23, 1);

-- --------------------------------------------------------

--
-- Table structure for table `empleados`
--

CREATE TABLE `empleados` (
  `ID_Usuario` int(11) NOT NULL COMMENT 'ID DEL USUARIO ',
  `Nombres` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'NOMBRE DEL USUARIO',
  `Apellidos` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'APELLIDO DEL USUARIO',
  `DNI` int(9) NOT NULL COMMENT 'DNI DEL USUARIO',
  `Direccion` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'DIRECCION DEL USUARIO',
  `Phone` int(11) NOT NULL,
  `Tipo` varchar(10) COLLATE utf32_spanish_ci NOT NULL COMMENT 'TIPO DEL USUARIO(encargado,empleado o adminsucu))',
  `Email` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'EMAIL DEL USUARIO',
  `Fecha_de_ingreso` date NOT NULL COMMENT 'FECHA EN LA QUE INGRESO EL USUARIO'
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci COMMENT='TABLA CON LOS DATOS DEL USUARIO';

--
-- Dumping data for table `empleados`
--

INSERT INTO `empleados` (`ID_Usuario`, `Nombres`, `Apellidos`, `DNI`, `Direccion`, `Phone`, `Tipo`, `Email`, `Fecha_de_ingreso`) VALUES
(1, 'Leonel', 'Moyano', 42824297, 'Calle Falsa 123', 12345678, '2', 'leonelmoyano1809@gmail..com', '2020-09-23');

-- --------------------------------------------------------

--
-- Table structure for table `gastos`
--

CREATE TABLE `gastos` (
  `ID_Gasto` int(11) NOT NULL,
  `ID_Sucursal` int(11) NOT NULL,
  `ID_Encargado` int(11) NOT NULL,
  `ID_Producto` int(11) NOT NULL,
  `Precio` double NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `FH_Gasto` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gastos_productos`
--

CREATE TABLE `gastos_productos` (
  `ID_Producto` int(11) NOT NULL,
  `Producto` varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `gastos_productos`
--

INSERT INTO `gastos_productos` (`ID_Producto`, `Producto`) VALUES
(1, 'Esponja'),
(2, 'Talones de números');

-- --------------------------------------------------------

--
-- Table structure for table `pedidos`
--

CREATE TABLE `pedidos` (
  `ID_PedidoAI` int(11) NOT NULL,
  `ID_Sucursal` int(11) NOT NULL,
  `ID_Pedido` int(11) NOT NULL,
  `PrecioFinal` double DEFAULT NULL,
  `FH_Inicio` timestamp NOT NULL DEFAULT current_timestamp(),
  `FH_Listo` timestamp NULL DEFAULT NULL,
  `FH_Entregado` timestamp NULL DEFAULT NULL,
  `ID_Encargado` int(11) NOT NULL,
  `Regalo` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Cancelado` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pedidos_productos`
--

CREATE TABLE `pedidos_productos` (
  `ID_PedidoAI` int(11) NOT NULL,
  `Producto` int(11) NOT NULL,
  `ID_Producto` int(11) NOT NULL,
  `ID_SubPedido` int(11) DEFAULT NULL COMMENT 'EN EL CASO DE QUE HAYA PAPAS FRITAS CON ALGUNA SALSA VAN A COMPARTIR EL NÚMERO',
  `Cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `precio_salsas`
--

CREATE TABLE `precio_salsas` (
  `ID_Comida` int(11) NOT NULL,
  `ID_ComidaCategoria` int(11) NOT NULL DEFAULT 0,
  `ID_SalsaCategoria` int(11) NOT NULL,
  `Precio` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `precio_salsas`
--

INSERT INTO `precio_salsas` (`ID_Comida`, `ID_ComidaCategoria`, `ID_SalsaCategoria`, `Precio`) VALUES
(1, 3, 8, 20),
(1, 3, 9, 20),
(1, 4, 8, 25),
(1, 4, 9, 25),
(1, 5, 8, 25),
(1, 5, 9, 25),
(1, 6, 8, 40),
(1, 6, 9, 40),
(2, 10, 8, 10),
(2, 10, 9, 10);

-- --------------------------------------------------------

--
-- Table structure for table `promos_precios`
--

CREATE TABLE `promos_precios` (
  `ID_Promo` int(11) NOT NULL,
  `Precio` float NOT NULL,
  `Nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `promos_precios`
--

INSERT INTO `promos_precios` (`ID_Promo`, `Precio`, `Nombre`) VALUES
(1, 80, 'Escolar');

-- --------------------------------------------------------

--
-- Table structure for table `promos_productos`
--

CREATE TABLE `promos_productos` (
  `ID_Promo` int(11) NOT NULL,
  `ID_Producto` int(11) NOT NULL,
  `ID_Tipo` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `promos_productos`
--

INSERT INTO `promos_productos` (`ID_Promo`, `ID_Producto`, `ID_Tipo`, `Cantidad`) VALUES
(1, 1, 2, 1),
(1, 13, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `salsa`
--

CREATE TABLE `salsa` (
  `ID_Salsa` int(11) NOT NULL,
  `Nombre` varchar(20) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `ID_Categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `salsa`
--

INSERT INTO `salsa` (`ID_Salsa`, `Nombre`, `ID_Categoria`) VALUES
(1, 'Mayonesa', 7),
(2, 'Mostaza', 7),
(3, 'Ketchup', 7),
(4, 'Salsa golf', 7),
(5, 'Barbacoa', 7),
(6, 'Cheddar', 8),
(7, 'Mayocream', 8),
(9, 'Mexicana', 8),
(11, 'Braba', 9);

-- --------------------------------------------------------

--
-- Table structure for table `salsa_usa`
--

CREATE TABLE `salsa_usa` (
  `ID_Salsa` int(11) NOT NULL,
  `Producto` varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `salsa_usa`
--

INSERT INTO `salsa_usa` (`ID_Salsa`, `Producto`) VALUES
(7, 'Cebolla de verdeo'),
(7, 'Crema'),
(7, 'Mayonesa'),
(9, 'Ketchup'),
(9, 'Salsa tabasco'),
(11, 'Ajo'),
(11, 'Cebolla'),
(11, 'Chile jalapeño'),
(11, 'Pimienta'),
(11, 'Tomate');

-- --------------------------------------------------------

--
-- Table structure for table `tipo_productos`
--

CREATE TABLE `tipo_productos` (
  `ID_Tipo` int(11) NOT NULL,
  `Tipo` varchar(20) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `tipo_productos`
--

INSERT INTO `tipo_productos` (`ID_Tipo`, `Tipo`) VALUES
(1, 'Bebida'),
(2, 'Comida'),
(3, 'Salsa'),
(4, 'Promo');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bebidas_envases`
--
ALTER TABLE `bebidas_envases`
  ADD PRIMARY KEY (`ID_Envases`,`Envase`);

--
-- Indexes for table `bebidas_sabores`
--
ALTER TABLE `bebidas_sabores`
  ADD PRIMARY KEY (`ID_Sabor`,`Sabor`);

--
-- Indexes for table `bebida_final`
--
ALTER TABLE `bebida_final`
  ADD PRIMARY KEY (`ID_Bebida`);

--
-- Indexes for table `bebida_marca`
--
ALTER TABLE `bebida_marca`
  ADD PRIMARY KEY (`ID_Marca`);

--
-- Indexes for table `bebida_tamaño`
--
ALTER TABLE `bebida_tamaño`
  ADD PRIMARY KEY (`ID_Size`);

--
-- Indexes for table `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`ID_Caja`,`ID_Encargado`,`FH_Apertura`);

--
-- Indexes for table `categorias_productos`
--
ALTER TABLE `categorias_productos`
  ADD PRIMARY KEY (`ID_Categoria`);

--
-- Indexes for table `comida`
--
ALTER TABLE `comida`
  ADD PRIMARY KEY (`ID_Comida`);

--
-- Indexes for table `comida_final`
--
ALTER TABLE `comida_final`
  ADD PRIMARY KEY (`ID_ComidaFinal`,`ID_Comida`,`ID_Categoria`);

--
-- Indexes for table `comida_usa`
--
ALTER TABLE `comida_usa`
  ADD PRIMARY KEY (`ID_Comida`,`ID_Mercaderia`);

--
-- Indexes for table `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`ID_Usuario`,`DNI`,`Email`);

--
-- Indexes for table `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`ID_Gasto`);

--
-- Indexes for table `gastos_productos`
--
ALTER TABLE `gastos_productos`
  ADD PRIMARY KEY (`ID_Producto`,`Producto`);

--
-- Indexes for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`ID_PedidoAI`);

--
-- Indexes for table `pedidos_productos`
--
ALTER TABLE `pedidos_productos`
  ADD PRIMARY KEY (`ID_PedidoAI`,`Producto`,`ID_Producto`,`Cantidad`);

--
-- Indexes for table `precio_salsas`
--
ALTER TABLE `precio_salsas`
  ADD PRIMARY KEY (`ID_Comida`,`ID_ComidaCategoria`,`ID_SalsaCategoria`);

--
-- Indexes for table `promos_precios`
--
ALTER TABLE `promos_precios`
  ADD PRIMARY KEY (`ID_Promo`);

--
-- Indexes for table `promos_productos`
--
ALTER TABLE `promos_productos`
  ADD PRIMARY KEY (`ID_Promo`,`ID_Producto`);

--
-- Indexes for table `salsa`
--
ALTER TABLE `salsa`
  ADD PRIMARY KEY (`ID_Salsa`);

--
-- Indexes for table `salsa_usa`
--
ALTER TABLE `salsa_usa`
  ADD PRIMARY KEY (`ID_Salsa`,`Producto`);

--
-- Indexes for table `tipo_productos`
--
ALTER TABLE `tipo_productos`
  ADD PRIMARY KEY (`ID_Tipo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bebidas_envases`
--
ALTER TABLE `bebidas_envases`
  MODIFY `ID_Envases` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bebidas_sabores`
--
ALTER TABLE `bebidas_sabores`
  MODIFY `ID_Sabor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bebida_final`
--
ALTER TABLE `bebida_final`
  MODIFY `ID_Bebida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `bebida_marca`
--
ALTER TABLE `bebida_marca`
  MODIFY `ID_Marca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bebida_tamaño`
--
ALTER TABLE `bebida_tamaño`
  MODIFY `ID_Size` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `caja`
--
ALTER TABLE `caja`
  MODIFY `ID_Caja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `categorias_productos`
--
ALTER TABLE `categorias_productos`
  MODIFY `ID_Categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `comida`
--
ALTER TABLE `comida`
  MODIFY `ID_Comida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `comida_final`
--
ALTER TABLE `comida_final`
  MODIFY `ID_ComidaFinal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gastos`
--
ALTER TABLE `gastos`
  MODIFY `ID_Gasto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gastos_productos`
--
ALTER TABLE `gastos_productos`
  MODIFY `ID_Producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `ID_PedidoAI` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promos_precios`
--
ALTER TABLE `promos_precios`
  MODIFY `ID_Promo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `salsa`
--
ALTER TABLE `salsa`
  MODIFY `ID_Salsa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tipo_productos`
--
ALTER TABLE `tipo_productos`
  MODIFY `ID_Tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
