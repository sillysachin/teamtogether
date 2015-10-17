<?php

/**
 * @version     $Id: configuration.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Models
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
/**
 * JSNUniform model Configuration
 *
 * @package     Models
 * @subpackage  Configuration
 * @since       1.6
 */
class JSNUniformModelConfiguration extends JSNConfigModel
{

	/**
	 * Save the submitted configuration data to database.
	 *
	 * @param   array  $config  Parsed configuration declaration.
	 * @param   array  $data    The data to save.
	 *
	 * @return  void
	 */
	public function save($config, $data)
	{
		$post = $_POST;
		if (!empty($post['form_email_notification']))
		{
			$emailNotification = array();
			foreach ($post['form_email_notification'] as $email)
			{
				if (!empty($email))
				{
					$address = new stdClass;
					$address->email_address = $email;
					$emailNotification[] = $address;
				}
			}
			$data['email_notification'] = json_encode($emailNotification);
		}

		parent::save($config, $data);
	}

}
