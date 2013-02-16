
CREATE TABLE IF NOT EXISTS `#__improvemycity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `catid` int(11) NOT NULL DEFAULT '0',
  `latitude` varchar(20) NOT NULL,
  `longitude` varchar(20) NOT NULL,
  `description` text,
  `photo` text,
  `address` text,
  `votes` int(11) NOT NULL DEFAULT '1',
  `currentstatus` int(11) NOT NULL DEFAULT '1',
  `reported` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `acknowledged` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `closed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `userid` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '1',
  `language` char(7) NOT NULL,
  `hits` int(10) NOT NULL,
  `inserted_by` INT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__improvemycity_progress` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `improvemycityid` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `description` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `progressdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__improvemycity_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `improvemycityid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `votingdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `state` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__improvemycity_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `improvemycityid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__improvemycity_timestamp` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `triggered` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT COLLATE=utf8_general_ci;

INSERT INTO `#__improvemycity_timestamp` (`triggered`) VALUES (MD5(RAND()));
INSERT INTO `#__improvemycity_timestamp` (`triggered`) VALUES (MD5(RAND()));

CREATE TABLE IF NOT EXISTS `#__improvemycity_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skey` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

INSERT INTO `#__improvemycity_keys` (`skey`) VALUES ('1234567890123456');