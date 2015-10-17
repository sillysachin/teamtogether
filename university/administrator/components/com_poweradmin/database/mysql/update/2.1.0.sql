ALTER TABLE `#__jsn_poweradmin_menu_assets` ADD `legacy` tinyint(2) NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `#__jsn_poweradmin_favourite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `icon` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `url` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;