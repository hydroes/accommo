# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 33.33.33.10 (MySQL 5.1.62-0ubuntu0.10.04.1)
# Database: accommo_today
# Generation Time: 2012-08-12 12:06:15 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table accommodation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `accommodation`;

CREATE TABLE `accommodation` (
  `accommodation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accommodation_name` varchar(255) NOT NULL,
  `accommodation_description` text NOT NULL,
  `accommodation_location` text NOT NULL,
  `accommodation_address` text NOT NULL,
  `accommodation_sleeps` smallint(10) unsigned NOT NULL DEFAULT '0',
  `accommodation_price_min` decimal(10,0) unsigned NOT NULL,
  `accommodation_price_max` decimal(10,0) unsigned NOT NULL,
  `accommodation_lat` double(10,6) NOT NULL,
  `accommodation_lng` double(10,6) NOT NULL,
  `accommodation_zoom` smallint(2) unsigned NOT NULL,
  `accommodation_create_date` int(10) unsigned NOT NULL,
  `accommodation_user_id` int(10) unsigned NOT NULL,
  `accommodation_type` enum('Backpacker','Bed and Breakfast','Boutique Hotel','Camping and Caravanning','Country House','Guest House,Hotel','Houseboat','Lodge','Mobile Camp','Private Home','Resort','Safari Lodge','Self-catering','Tented Camp','none') NOT NULL DEFAULT 'none',
  `accommodation_check_in` time DEFAULT NULL,
  `accommodation_check_out` time DEFAULT NULL,
  PRIMARY KEY (`accommodation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table accommodation_features
# ------------------------------------------------------------

DROP TABLE IF EXISTS `accommodation_features`;

CREATE TABLE `accommodation_features` (
  `feature_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `feature_name` varchar(255) NOT NULL,
  `feature_official` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`feature_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

LOCK TABLES `accommodation_features` WRITE;
/*!40000 ALTER TABLE `accommodation_features` DISABLE KEYS */;

INSERT INTO `accommodation_features` (`feature_id`, `feature_name`, `feature_official`)
VALUES
	(1,'cute puppies','no'),
	(2,'dish washer','no'),
	(3,'jacuuzzi','yes');

/*!40000 ALTER TABLE `accommodation_features` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table accommodation_features_mappings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `accommodation_features_mappings`;

CREATE TABLE `accommodation_features_mappings` (
  `afm_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `afm_accommodation_id` int(10) unsigned NOT NULL,
  `afm_feature_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`afm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table accommodation_images
# ------------------------------------------------------------

DROP TABLE IF EXISTS `accommodation_images`;

CREATE TABLE `accommodation_images` (
  `ai_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ai_accommodation_id` int(10) unsigned NOT NULL,
  `ai_name` varchar(255) NOT NULL,
  `ai_original_name` varchar(255) NOT NULL,
  `ai_width` smallint(4) unsigned NOT NULL,
  `ai_height` smallint(4) unsigned NOT NULL,
  `ai_thumb_name` varchar(255) NOT NULL,
  `ai_thumb_width` smallint(4) unsigned NOT NULL,
  `ai_thumb_height` smallint(4) unsigned NOT NULL,
  PRIMARY KEY (`ai_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table accommodation_location_mappings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `accommodation_location_mappings`;

CREATE TABLE `accommodation_location_mappings` (
  `alm_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alm_accommodation_id` int(10) unsigned NOT NULL,
  `alm_location_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`alm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table accommodation_rates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `accommodation_rates`;

CREATE TABLE `accommodation_rates` (
  `ar_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ar_accommodation_id` int(10) unsigned NOT NULL,
  `ar_room_type_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table accommodation_room_feature_mappings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `accommodation_room_feature_mappings`;

CREATE TABLE `accommodation_room_feature_mappings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `arfm_room_type_id` int(11) unsigned DEFAULT NULL,
  `arfm_room_feature_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table accommodation_room_features
# ------------------------------------------------------------

DROP TABLE IF EXISTS `accommodation_room_features`;

CREATE TABLE `accommodation_room_features` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `arf_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table accommodation_room_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `accommodation_room_types`;

CREATE TABLE `accommodation_room_types` (
  `art_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `art_accommodation_id` int(10) unsigned NOT NULL,
  `art_name` varchar(255) NOT NULL DEFAULT '',
  `art_description` text NOT NULL,
  `art_rooms` smallint(5) unsigned NOT NULL DEFAULT '0',
  `art_sleeps` smallint(10) unsigned NOT NULL DEFAULT '0',
  `art_per_single` decimal(10,0) unsigned NOT NULL DEFAULT '0',
  `art_pp_sharing` decimal(10,0) unsigned NOT NULL DEFAULT '0',
  `art_per_room` decimal(10,0) unsigned NOT NULL DEFAULT '0',
  `art_live` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`art_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table locations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `locations`;

CREATE TABLE `locations` (
  `location_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_name` varchar(255) NOT NULL,
  `location_short_name` varchar(255) NOT NULL,
  `location_parent` mediumtext,
  `location_coord_lat` float(10,6) NOT NULL,
  `location_coord_lng` float(10,6) NOT NULL,
  `location_zoom` smallint(2) unsigned NOT NULL DEFAULT '8',
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_signup_date` int(10) unsigned NOT NULL,
  `user_last_login_date` int(10) unsigned NOT NULL,
  `user_email` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
