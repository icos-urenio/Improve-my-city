#2.5.1
DROP TABLE IF EXISTS `#__improvemycity_keys`; 
CREATE TABLE `#__improvemycity_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skey` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

INSERT INTO `#__improvemycity_keys` (`skey`) VALUES ('1234567890123456');