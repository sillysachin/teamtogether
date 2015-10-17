CREATE TABLE IF NOT EXISTS `#__jsn_poweradmin_menu_assets` (
  `menuId` int(16) NOT NULL,
  `assets` text NULL,
  `type` enum('css','js') NOT NULL DEFAULT 'css'
) CHARACTER SET `utf8`;