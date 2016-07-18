-- phpMyAdmin SQL Dump
-- version 3.5.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 19, 2016 at 03:43 AM
-- Server version: 5.1.68-community
-- PHP Version: 5.3.24

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `greenpeace`
--

-- --------------------------------------------------------

--
-- Table structure for table `en_history_eabroadcastdata`
--

CREATE TABLE IF NOT EXISTS `en_history_eabroadcastdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `broadcastId` int(11) NOT NULL,
  `openCount` int(11) NOT NULL,
  `clickCount` int(11) NOT NULL,
  `compCount` int(11) NOT NULL,
  `hardBounceCount` int(11) NOT NULL,
  `softbounceCount` int(11) NOT NULL,
  `unsubscribeCount` int(11) NOT NULL,
  `feedbackCount` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9060 ;

-- --------------------------------------------------------

--
-- Table structure for table `en_history_eabroadcastinfo`
--

CREATE TABLE IF NOT EXISTS `en_history_eabroadcastinfo` (
  `broadcastId` int(11) NOT NULL,
  `Country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `broadcastName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `broadcastDate` date NOT NULL,
  `sendCount` int(11) NOT NULL,
  `project` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `campaign` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`broadcastId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
