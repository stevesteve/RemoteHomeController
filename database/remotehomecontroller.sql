-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 23, 2015 at 05:26 PM
-- Server version: 5.5.41
-- PHP Version: 5.4.39-0+deb7u2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `remotehomecontroller`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=93 ;

-- --------------------------------------------------------

--
-- Table structure for table `favorite`
--

CREATE TABLE IF NOT EXISTS `favorite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `category` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=66 ;

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE IF NOT EXISTS `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=43 ;

-- --------------------------------------------------------

--
-- Table structure for table `permission_user`
--

CREATE TABLE IF NOT EXISTS `permission_user` (
  `permission` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`permission`,`user`),
  KEY `user` (`user`),
  KEY `permission` (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `switch`
--

CREATE TABLE IF NOT EXISTS `switch` (
  `id` int(11) NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=52 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `lastname`, `firstname`, `is_admin`) VALUES
(1, 'admin', '$2y$10$KR4lXIeNrIKq6VEIdIhXtOHDrg94OsrOBPmwqCyFIAFxMIkszjIQq', '', '', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `category_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `favorite`
--
ALTER TABLE `favorite`
  ADD CONSTRAINT `favorite_ibfk_2` FOREIGN KEY (`category`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `permission_user`
--
ALTER TABLE `permission_user`
  ADD CONSTRAINT `permission_user_ibfk_3` FOREIGN KEY (`permission`) REFERENCES `permission` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permission_user_ibfk_4` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
