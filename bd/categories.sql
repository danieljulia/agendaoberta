-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 23-11-2012 a las 09:05:26
-- Versión del servidor: 5.1.36
-- Versión de PHP: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `ao_ok`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ao_category`
--

CREATE TABLE IF NOT EXISTS `ao_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `slug` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Volcar la base de datos para la tabla `ao_category`
--

INSERT INTO `ao_category` (`id`, `name`, `slug`) VALUES
(1, 'Infantil i juvenil', 'infantil-i-juvenil'),
(2, 'Teatre i dansa', 'teatre-i-dansa'),
(3, 'Cinema i video', 'cinema-i-video'),
(4, 'Música', 'musica'),
(5, 'Exposicions', 'exposicions'),
(6, 'Fires', 'fires'),
(7, 'Cultura tradicional', 'cultura-tradicional'),
(8, 'Festes populars', 'festes-populars'),
(9, 'Rutes i itineraris', 'rutes-i-itineraris'),
(10, 'Patrimoni històric', 'patrimoni-historic'),
(11, 'Esports', 'esports'),
(12, 'Cursos i tallers', 'cursos-i-tallers'),
(13, 'Xerrades i conferències', 'xerrades-i-conferencies'),
(14, 'Ciència', 'ciencia'),
(15, 'Literatura', 'literatura'),
(16, 'Gastronomia', 'gastronomia'),
(20, 'Altres', 'altres');
