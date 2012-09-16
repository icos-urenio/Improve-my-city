#version 2.4.1 introduces timestamp table to be used by native android application
 
CREATE TABLE IF NOT EXISTS `#__improvemycity_timestamp` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `triggered` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT COLLATE=utf8_general_ci;

