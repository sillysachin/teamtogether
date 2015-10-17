DROP TABLE IF EXISTS `#__jsonexport_tables`;

CREATE TABLE IF NOT EXISTS  `#__jsonexport_tables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tableName` varchar(300) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
