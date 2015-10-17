<?php

/**
 * @version     $Id: email.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Tables
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.tableasset');

/**
 * Abstract Table class
 *
 * @package     Joomla.Platform
 * @subpackage  Table
 * @link        http://docs.joomla.org/JTable
 * @since       11.1
 * @tutorial	Joomla.Platform/jtable.cls
 */
class JSNUniformTableEmail extends JTable
{

	/**
	 * Object constructor to set table and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the table and key fields
	 * for a particular database table.
	 * 
	 * @param   JDatabase  &$db  JDatabase connector object.
	 *
	 * @since   11.1
	 */
	function __construct(&$db)
	{
		parent::__construct('#__jsn_uniform_emails', 'email_id', $db);
	}

}
