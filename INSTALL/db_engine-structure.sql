-- MySQL dump 10.11
--
-- Host: localhost    Database: db_engine
-- ------------------------------------------------------
-- Server version	5.0.44-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES latin1 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `version` int(10) unsigned NOT NULL,
  `code` varchar(2) NOT NULL,
  `name` varchar(50) NOT NULL,
  `locale` varchar(50) default NULL,
  `image` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Table structure for table `location_country`
--

DROP TABLE IF EXISTS `location_country`;
CREATE TABLE `location_country` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `version` int(10) unsigned NOT NULL,
  `code` varchar(4) NOT NULL,
  `name` varchar(50) NOT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `list_order` (`list_order`,`name`),
  KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=240 DEFAULT CHARSET=latin1;

--
-- Table structure for table `location_region`
--

DROP TABLE IF EXISTS `location_region`;
CREATE TABLE `location_region` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `version` int(10) unsigned NOT NULL,
  `code` varchar(4) default NULL,
  `name` varchar(50) default NULL,
  `upper_name` varchar(50) default NULL,
  `country` varchar(4) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `name` (`name`),
  KEY `code` (`code`),
  KEY `country` (`country`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

--
-- Table structure for table `location_state`
--

DROP TABLE IF EXISTS `location_state`;
CREATE TABLE `location_state` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `version` int(10) unsigned NOT NULL,
  `code` varchar(4) default NULL,
  `name` varchar(50) default NULL,
  `upper_name` varchar(50) default NULL,
  `region` varchar(4) default NULL,
  `country` varchar(4) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `country` (`country`,`region`),
  KEY `code` (`code`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;

--
-- Table structure for table `location_zipcode`
--

DROP TABLE IF EXISTS `location_zipcode`;
CREATE TABLE `location_zipcode` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `version` int(10) unsigned NOT NULL,
  `code` varchar(10) default NULL,
  `name` varchar(50) default NULL,
  `country` varchar(4) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `country` (`country`,`code`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=36683 DEFAULT CHARSET=latin1;

--
-- Table structure for table `website`
--

DROP TABLE IF EXISTS `website`;
CREATE TABLE `website` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `system_name` varchar(50) NOT NULL,
  `db_name` varchar(50) NOT NULL,
  `domain_name` varchar(50) NOT NULL,
  `firstname` varchar(255) default NULL,
  `lastname` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `disk_space` int(10) unsigned default NULL,
  `package` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;

--
-- Table structure for table `website_address`
--

DROP TABLE IF EXISTS `website_address`;
CREATE TABLE `website_address` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `version` int(10) unsigned NOT NULL,
  `address1` varchar(50) default NULL,
  `address2` varchar(50) default NULL,
  `zip_code` varchar(10) default NULL,
  `city` varchar(50) default NULL,
  `state` varchar(50) default NULL,
  `country` varchar(50) default NULL,
  `postal_box` varchar(50) default NULL,
  `telephone` varchar(20) default NULL,
  `mobile` varchar(20) default NULL,
  `fax` varchar(20) default NULL,
  `vat_number` varchar(50) default NULL,
  `website` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Table structure for table `website_invoice`
--

DROP TABLE IF EXISTS `website_invoice`;
CREATE TABLE `website_invoice` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `version` int(10) unsigned NOT NULL,
  `release_date` date NOT NULL,
  `website_subscriptionId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Table structure for table `website_option`
--

DROP TABLE IF EXISTS `website_option`;
CREATE TABLE `website_option` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` varchar(20) default NULL,
  `website_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=193 DEFAULT CHARSET=latin1;

--
-- Table structure for table `website_subscription`
--

DROP TABLE IF EXISTS `website_subscription`;
CREATE TABLE `website_subscription` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `version` int(10) unsigned NOT NULL,
  `opening_date` datetime NOT NULL,
  `fee` int(10) unsigned default NULL,
  `duration` int(10) unsigned default NULL,
  `auto_renewal` tinyint(1) NOT NULL,
  `termination_date` datetime default NULL,
  `website_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-02-04  4:21:54
