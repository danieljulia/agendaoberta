-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 23-11-2012 a las 09:04:53
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
-- Estructura de tabla para la tabla `ao_admin`
--

CREATE TABLE IF NOT EXISTS `ao_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(40) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `login_check` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ao_city`
--

CREATE TABLE IF NOT EXISTS `ao_city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `slug` varchar(128) NOT NULL,
  `lat` float(10,6) DEFAULT NULL,
  `lng` float(10,6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=367 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ao_event`
--

CREATE TABLE IF NOT EXISTS `ao_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `summary` varchar(255) NOT NULL,
  `url` varchar(256) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `processed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reflush` tinyint(1) NOT NULL DEFAULT '0',
  `category_classified` tinyint(1) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `starttime` time DEFAULT NULL,
  `endtime` time DEFAULT NULL,
  `schedule` varchar(64) NOT NULL,
  `duration` int(10) unsigned DEFAULT NULL,
  `geo_lat` float(10,6) DEFAULT NULL,
  `geo_lng` float(10,6) DEFAULT NULL,
  `geo_pre` tinyint(4) NOT NULL,
  `photo` varchar(128) NOT NULL,
  `photo_local` varchar(60) DEFAULT NULL,
  `location` varchar(128) DEFAULT NULL,
  `address` varchar(128) DEFAULT NULL,
  `source_id` int(11) NOT NULL,
  `city_id` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  `lang` varchar(2) NOT NULL DEFAULT 'ca',
  `score` tinyint(4) NOT NULL DEFAULT '1',
  `uid` varchar(200) NOT NULL,
  `num_favorites` int(10) unsigned NOT NULL DEFAULT '0',
  `num_flagged` int(10) unsigned NOT NULL DEFAULT '0',
  `promoted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `default_category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`),
  KEY `source_id` (`source_id`),
  KEY `location_id` (`location_id`),
  KEY `city_id` (`city_id`),
  KEY `default_category_id` (`default_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2240 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ao_event_2_category`
--

CREATE TABLE IF NOT EXISTS `ao_event_2_category` (
  `event_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`event_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ao_event_2_tag`
--

CREATE TABLE IF NOT EXISTS `ao_event_2_tag` (
  `event_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`event_id`,`tag_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ao_favorite`
--

CREATE TABLE IF NOT EXISTS `ao_favorite` (
  `event_id` int(11) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`event_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ao_friend`
--

CREATE TABLE IF NOT EXISTS `ao_friend` (
  `user_id` int(10) unsigned NOT NULL,
  `friend_id` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`user_id`,`friend_id`),
  KEY `friend_id` (`friend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ao_location`
--

CREATE TABLE IF NOT EXISTS `ao_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `address` varchar(128) NOT NULL,
  `location_type` varchar(16) NOT NULL,
  `pre` tinyint(4) NOT NULL,
  `formatted_address` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ao_search`
--

CREATE TABLE IF NOT EXISTS `ao_search` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(60) DEFAULT NULL,
  `q` varchar(60) DEFAULT NULL,
  `pref_time` enum('24h','48h','week','weekend','month') DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `location` varchar(60) DEFAULT NULL,
  `nearby` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `category_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `hash` varchar(32) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ao_source`
--

CREATE TABLE IF NOT EXISTS `ao_source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feed` varchar(256) NOT NULL,
  `feed_type` enum('ical','rss','xml','page','gcal') DEFAULT NULL,
  `xpath` varchar(512) NOT NULL,
  `scrape` tinyint(1) NOT NULL,
  `parser` varchar(12) NOT NULL,
  `name` varchar(128) NOT NULL,
  `slug` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `city_id` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  `lang` char(2) CHARACTER SET ascii NOT NULL DEFAULT 'ca',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `started` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` int(11) NOT NULL,
  `error` tinyint(4) NOT NULL,
  `next` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `new` int(11) NOT NULL,
  `city2events` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `city_id` (`city_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=200 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ao_source_2_category`
--

CREATE TABLE IF NOT EXISTS `ao_source_2_category` (
  `source_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`source_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ao_stats`
--

CREATE TABLE IF NOT EXISTS `ao_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(8) NOT NULL,
  `value` int(11) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=684 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ao_tag`
--

CREATE TABLE IF NOT EXISTS `ao_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

