CREATE TABLE `catfish_terminal` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT 0,
  `pid` bigint(20) DEFAULT 0,
  `jname` varchar(50) NOT NULL DEFAULT '',
  `jvalue` text,
  `times` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
