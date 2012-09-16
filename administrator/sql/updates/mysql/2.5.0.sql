#2.5.0
DROP TABLE IF EXISTS `#__improvemycity_timestamp`; 
CREATE TABLE `#__improvemycity_timestamp` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `triggered` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT COLLATE=utf8_general_ci;
INSERT INTO `#__improvemycity_timestamp` (`triggered`) VALUES (MD5(RAND()));