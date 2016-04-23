set foreign_key_checks = 0;

DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `code` varchar(2) NOT NULL,
  `name` varchar(50) NOT NULL,
  `locale` varchar(50) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `code` (`code`)
);

DROP TABLE IF EXISTS `location_country`;
CREATE TABLE `location_country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `code` varchar(4) NOT NULL,
  `name` varchar(50) NOT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `list_order` (`list_order`,`name`),
  KEY `code` (`code`)
);

DROP TABLE IF EXISTS `location_region`;
CREATE TABLE `location_region` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `code` varchar(4) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `upper_name` varchar(50) DEFAULT NULL,
  `country` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `name` (`name`),
  KEY `code` (`code`),
  KEY `country` (`country`)
);

DROP TABLE IF EXISTS `location_state`;
CREATE TABLE `location_state` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `code` varchar(4) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `upper_name` varchar(50) DEFAULT NULL,
  `region` varchar(4) DEFAULT NULL,
  `country` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `country` (`country`,`region`),
  KEY `code` (`code`),
  KEY `name` (`name`)
);

DROP TABLE IF EXISTS `location_zipcode`;
CREATE TABLE `location_zipcode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `country` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `country` (`country`,`code`),
  KEY `name` (`name`)
);

DROP TABLE IF EXISTS `website`;
CREATE TABLE `website` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `system_name` varchar(50) NOT NULL,
  `db_name` varchar(50) NOT NULL,
  `domain_name` varchar(50) NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `disk_space` int(10) unsigned DEFAULT NULL,
  `package` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);

DROP TABLE IF EXISTS `website_address`;
CREATE TABLE `website_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `address1` varchar(50) DEFAULT NULL,
  `address2` varchar(50) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `postal_box` varchar(50) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `vat_number` varchar(50) DEFAULT NULL,
  `website_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);

DROP TABLE IF EXISTS `website_invoice`;
CREATE TABLE `website_invoice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `release_date` date NOT NULL,
  `website_subscriptionId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);

DROP TABLE IF EXISTS `website_option`;
CREATE TABLE `website_option` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` varchar(20) DEFAULT NULL,
  `website_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);

DROP TABLE IF EXISTS `website_subscription`;
CREATE TABLE `website_subscription` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `opening_date` datetime NOT NULL,
  `fee` int(10) unsigned DEFAULT NULL,
  `duration` int(10) unsigned DEFAULT NULL,
  `auto_renewal` tinyint(1) NOT NULL,
  `termination_date` datetime DEFAULT NULL,
  `website_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);

