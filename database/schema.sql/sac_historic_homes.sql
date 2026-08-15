-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Aug 14, 2026 at 03:44 PM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sac_historic_homes`
--

-- --------------------------------------------------------

--
-- Table structure for table `architectural_styles`
--

DROP TABLE IF EXISTS `architectural_styles`;
CREATE TABLE IF NOT EXISTS `architectural_styles` (
  `style_id` int NOT NULL AUTO_INCREMENT,
  `style_name` varchar(100) NOT NULL,
  `era` varchar(100) NOT NULL,
  `approx_start_year` int DEFAULT NULL,
  `approx_end_year` int DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`style_id`),
  UNIQUE KEY `style_name` (`style_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `city_landmarks`
--

DROP TABLE IF EXISTS `city_landmarks`;
CREATE TABLE IF NOT EXISTS `city_landmarks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `objectid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apn` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_address` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordinance` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shape__area` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shape__length` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `objectid` (`objectid`),
  KEY `idx_city_landmarks_apn` (`apn`),
  KEY `idx_city_landmarks_address` (`street_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `etl_log`
--

DROP TABLE IF EXISTS `etl_log`;
CREATE TABLE IF NOT EXISTS `etl_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `run_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `records_staged` int DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `landmark_research`
--

DROP TABLE IF EXISTS `landmark_research`;
CREATE TABLE IF NOT EXISTS `landmark_research` (
  `research_id` int NOT NULL AUTO_INCREMENT,
  `landmark_id` int NOT NULL,
  `year_built` int DEFAULT NULL,
  `architect` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`research_id`),
  UNIQUE KEY `landmark_id` (`landmark_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `landmark_styles`
--

DROP TABLE IF EXISTS `landmark_styles`;
CREATE TABLE IF NOT EXISTS `landmark_styles` (
  `landmark_id` int NOT NULL,
  `style_id` int NOT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`landmark_id`,`style_id`),
  KEY `fk_style_lookup` (`style_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staging_historical_landmarks`
--

DROP TABLE IF EXISTS `staging_historical_landmarks`;
CREATE TABLE IF NOT EXISTS `staging_historical_landmarks` (
  `objectid` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apn` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assessment` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordinance` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shape__area` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shape__length` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
