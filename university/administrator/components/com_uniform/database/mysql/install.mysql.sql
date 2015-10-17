DROP TABLE IF EXISTS `#__jsn_uniform_forms`;
CREATE  TABLE IF NOT EXISTS `#__jsn_uniform_forms` (
  `form_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_title` varchar(255) NOT NULL,
  `form_description` text,
  `form_layout` varchar(50) NOT NULL,
  `form_theme` varchar(45) NOT NULL,
  `form_style` text,
  `form_notify_submitter` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `form_post_action` tinyint(1) unsigned NOT NULL COMMENT '1 = Redirect to URL; 2 = Redirect to Menu Item; 3 = Show Article; 4 = Show custom message',
  `form_post_action_data` text NOT NULL,
  `form_captcha` tinyint(1) unsigned NOT NULL,
  `form_state` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `form_access` int(11) NOT NULL,
  `form_created_by` int(10) unsigned NOT NULL,
  `form_created_at` datetime DEFAULT NULL,
  `form_modified_by` int(10) unsigned DEFAULT '0',
  `form_modified_at` datetime DEFAULT NULL,
  `form_checked_out` int(10) unsigned DEFAULT '0',
  `form_checked_out_time` datetime DEFAULT NULL,
  `form_submission_cout` int(11) NOT NULL,
  `form_last_submitted` datetime NOT NULL,
  `form_submitter` varchar(255) NOT NULL,
  `form_type` int(11) NOT NULL,
  `form_settings` LONGTEXT NOT NULL ,
  `form_edit_submission` int(11) NOT NULL,
  PRIMARY KEY (`form_id`))DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__jsn_uniform_fields`;
CREATE  TABLE IF NOT EXISTS `#__jsn_uniform_fields` (
  `field_id` INT NOT NULL AUTO_INCREMENT ,
  `form_id` INT NOT NULL ,
  `field_type` VARCHAR(45) NOT NULL ,
  `field_identifier` VARCHAR(255) NOT NULL ,
  `field_title` VARCHAR(255) NULL ,
  `field_instructions` TEXT NULL ,
  `field_position` VARCHAR(50) NOT NULL ,
  `field_ordering` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `field_settings` TEXT NULL ,
  PRIMARY KEY (`field_id`) )DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__jsn_uniform_templates`;
CREATE  TABLE IF NOT EXISTS `#__jsn_uniform_templates` (
  `template_id` INT NOT NULL AUTO_INCREMENT ,
  `form_id` INT NOT NULL ,
  `template_notify_to` TINYINT(1) NOT NULL COMMENT '0 = Send to submitter; 1 = Send to added emails' ,
  `template_from` VARCHAR(75) NOT NULL ,
  `template_reply_to` VARCHAR(75) NOT NULL ,
  `template_subject` VARCHAR(255) NOT NULL ,
  `template_message` LONGTEXT NOT NULL ,
  `template_attach` TEXT NOT NULL ,
  PRIMARY KEY (`template_id`) )DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__jsn_uniform_submissions`;
CREATE  TABLE IF NOT EXISTS `#__jsn_uniform_submissions` (
  `submission_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `form_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NULL ,
  `submission_ip` VARCHAR(40) NOT NULL ,
  `submission_country` VARCHAR(45) NOT NULL ,
  `submission_country_code` VARCHAR(4) NOT NULL ,
  `submission_browser` VARCHAR(45) NOT NULL ,
  `submission_browser_version` VARCHAR(20) NOT NULL ,
  `submission_browser_agent` VARCHAR(255) NOT NULL ,
  `submission_os` VARCHAR(45) NOT NULL ,
  `submission_created_by` INT UNSIGNED NOT NULL COMMENT '0 = Guest' ,
  `submission_created_at` DATETIME NOT NULL ,
  `submission_state` TINYINT(1) UNSIGNED NOT NULL COMMENT '-1 = Trashed; 0 = Unpublish; 1 = Published' ,
  PRIMARY KEY (`submission_id`) ) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__jsn_uniform_emails`;
CREATE  TABLE IF NOT EXISTS `#__jsn_uniform_emails` (
  `email_id` INT NOT NULL AUTO_INCREMENT ,
  `form_id` INT NOT NULL ,
  `user_id` INT UNSIGNED NULL ,
  `email_name` VARCHAR(70) NULL ,
  `email_address` VARCHAR(255) NOT NULL ,
  `email_state` TINYINT(1) NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`email_id`))DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__jsn_uniform_config`;
CREATE TABLE IF NOT EXISTS `#__jsn_uniform_config` (
	`name` varchar( 255 ) NOT NULL ,
	`value` text NOT NULL ,
	UNIQUE KEY `name` ( `name` )
)DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__jsn_uniform_messages`;
CREATE TABLE IF NOT EXISTS `#__jsn_uniform_messages` (
    `msg_id` int(11) NOT NULL AUTO_INCREMENT,
    `msg_screen` varchar(150) DEFAULT NULL,
    `published` tinyint(1) DEFAULT '1',
    `ordering` int(11) DEFAULT '0',
    PRIMARY KEY (`msg_id`),
    UNIQUE KEY `message` (`msg_screen`,`ordering`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__jsn_uniform_form_pages`;
CREATE TABLE IF NOT EXISTS `#__jsn_uniform_form_pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_title` varchar(255) NOT NULL,
  `form_id` int(11) NOT NULL,
  `page_content` LONGTEXT NOT NULL,
  `page_template` LONGTEXT NOT NULL,
  `page_container` LONGTEXT NOT NULL,
  PRIMARY KEY (`page_id`)
)DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__jsn_uniform_submission_data`;
CREATE TABLE IF NOT EXISTS `#__jsn_uniform_submission_data` (
  `submission_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `submission_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `field_type` varchar(45) NOT NULL,
  `submission_data_value` longtext NOT NULL,
  PRIMARY KEY (`submission_data_id`),
  KEY `submission_data_id` (`submission_data_id`),
  KEY `submission_id` (`submission_id`),
  KEY `form_id` (`form_id`),
  KEY `field_id` (`field_id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
