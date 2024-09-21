CREATE TABLE `catfish_template` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tname` varchar(50) NOT NULL DEFAULT '',
  `screenshot` varchar(500) NOT NULL DEFAULT '',
  `tcontent` text,
  PRIMARY KEY (`id`),
  KEY `tname` (`tname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
