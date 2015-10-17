<?php

/**
 * @version     $Id: emailsettings.php 19013 2012-11-28 04:48:47Z thailv $
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
 * JSNUniform model EmailSettings
 *
 * @package     Models
 * @subpackage  Configuration
 * @since       1.6
 */
class JSNUniformModelEmailSettings extends JModelAdmin
{

	protected $option = JSN_UNIFORM;

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   11.1
	 */
	public function getTable($type = 'Template', $prefix = 'JSNUniformTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return    mixed    A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_uniform.emailsettings', 'template', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see JModelForm::loadFormData()
	 *
	 * @return object
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_uniform.edit.template.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   11.1
	 */
	public function getItem($pk = null)
	{

		$item = (object) array('template_id' => '', 'form_id' => '', 'template_notify_to' => '', 'template_from' => '', 'template_from_name' => '', 'template_to' => '', 'template_subject' => '', 'template_message' => '', 'template_attach' => '');
		$get = $_GET;
		if (!empty($get['control']) && $get['control'] == "config")
		{
			$emailTemplate = $this->_getEmailSettingsConfig($get['action']);

			if (!empty($emailTemplate->value))
			{
				$emailTemplate = json_decode($emailTemplate->value);
				if ($emailTemplate)
				{
					$item->template_subject = $emailTemplate->template_subject;
					$item->template_message = $emailTemplate->template_message;
				}
			}
		}
		elseif (!empty($get['control']) && $get['control'] == "form")
		{
			if (!empty($get['form_id']))
			{
				$emailTemplate = $this->_getEmailTemplate($get['form_id'], $get['action']);
				if ($emailTemplate)
				{
					$item->template_id = $emailTemplate->template_id;
					$item->form_id = $emailTemplate->form_id;
					$item->template_notify_to = $emailTemplate->template_notify_to;
					$item->template_from = $emailTemplate->template_from;
					$item->template_reply_to = $emailTemplate->template_reply_to;
					$item->template_subject = $emailTemplate->template_subject;
					$item->template_message = $emailTemplate->template_message;
					$item->template_attach = $emailTemplate->template_attach;
				}
			}
			else
			{
				$session = JFactory::getSession();
				$notify = JFactory::getApplication()->input->getVar('action', '0');
				$emailTemplate = $session->get('emailsettings_notify_' . $notify);

				$item->template_notify_to = isset($emailTemplate->template_notify_to)?$emailTemplate->template_notify_to:'';
				$item->template_from = isset($emailTemplate->template_from)?$emailTemplate->template_from:'';
				$item->template_reply_to = isset($emailTemplate->template_reply_to)?$emailTemplate->template_reply_to:'';
				$item->template_subject = isset($emailTemplate->template_subject)?$emailTemplate->template_subject:'';
				$item->template_message = isset($emailTemplate->template_message)?$emailTemplate->template_message:'';
			}
		}
		return $item;
	}

	/**
	 * Override save method to save form fields to database
	 *
	 * @param   array  $data  Data template email notify
	 *
	 * @return boolean
	 */
	public function saveForm($data)
	{
		//$data['jform']['template_message'] = isset($data['jform']['template_message']) ? str_replace("&nbsp;", " ", $data['jform']['template_message']) : "";
		if (isset($data['jform']['template_message']))
		{
			$data['jform']['template_message'] = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true)?stripslashes($data['jform']['template_message']):$data['jform']['template_message'];
		}
		if (isset($data['jform']['template_subject']))
		{
			$data['jform']['template_subject'] = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true)?stripslashes($data['jform']['template_subject']):$data['jform']['template_subject'];
		}
		if (isset($data['jform']['template_from']))
		{
			$data['jform']['template_from'] = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true)?stripslashes($data['jform']['template_from']):$data['jform']['template_from'];
		}
		if (isset($data['jform']['template_reply_to']))
		{
			$data['jform']['template_reply_to'] = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true)?stripslashes($data['jform']['template_reply_to']):$data['jform']['template_reply_to'];
		}
		if (!empty($data['jform']['template_message']))
		{
			preg_match_all('/< *img[^>]*src *= *["\']?([^"\']*)/i', $data['jform']['template_message'], $matches);
			if (isset($matches[1]) && count($matches[1]))
			{
				for ($i = 0; $i < count($matches[1]); $i++)
				{
					if (!preg_match('/http\:\/\/|www\./', $matches[1][$i]))
					{
						$img = str_replace($matches[1][$i], JURI::root() . $matches[1][$i], $matches[0][$i]);
						$data['jform']['template_message'] = str_replace($matches[0][$i], $img, $data['jform']['template_message']);
					}
				}
			}
		}
		$data['jform']['template_attach'] = !empty($_POST['file_attach'])?json_encode($_POST['file_attach']):"";
		if (!empty($data['jform']['form_id']) && is_array($data['jform']))
		{
			$this->_db->setQuery("DELETE FROM #__jsn_uniform_templates WHERE form_id = ".(int)$data['jform']['form_id']." and template_notify_to = ".(int)$data['jform']['template_notify_to']." and template_id != ".(int)$data['jform']['template_id']);
			$this->_db->execute();
			parent::save($data['jform']);
		}
		else
		{
			$session = JFactory::getSession();
			$session->set('emailsettings_notify_' . $data['jform']['template_notify_to'], (Object) $data['jform']);
		}
		return true;
	}

	/**
	 * Override save method to save form fields to database
	 *
	 * @param   array  $data  Data template email notify
	 *
	 * @return boolean
	 */
	public function saveConfig($data)
	{
		$id = $data['jform']['template_notify_to']?"list_email":"email_submitter";
		if (isset($data['jform']['template_message']))
		{
			$data['jform']['template_message'] = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true)?stripslashes($data['jform']['template_message']):$data['jform']['template_message'];
		}
		if (isset($data['jform']['template_subject']))
		{
			$data['jform']['template_subject'] = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true)?stripslashes($data['jform']['template_subject']):$data['jform']['template_subject'];
		}
		if (isset($data['jform']['template_from']))
		{
			$data['jform']['template_from'] = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true)?stripslashes($data['jform']['template_from']):$data['jform']['template_from'];
		}
		if (isset($data['jform']['template_reply_to']))
		{
			$data['jform']['template_reply_to'] = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true)?stripslashes($data['jform']['template_reply_to']):$data['jform']['template_reply_to'];
		}
		if (!empty($data['jform']['template_message']))
		{
			preg_match_all('/< *img[^>]*src *= *["\']?([^"\']*)/i', $data['jform']['template_message'], $matches);
			if (isset($matches[1]) && count($matches[1]))
			{
				for ($i = 0; $i < count($matches[1]); $i++)
				{
					if (!preg_match('/http\:\/\/|www\./', $matches[1][$i]))
					{
						$img = str_replace($matches[1][$i], JURI::root() . $matches[1][$i], $matches[0][$i]);
						$data['jform']['template_message'] = str_replace($matches[0][$i], $img, $data['jform']['template_message']);
					}
				}
			}
		}

		$this->_db->setQuery("REPLACE INTO `#__jsn_uniform_config` (name, value) VALUES ('" . $id . "'," . $this->_db->Quote(json_encode($data['jform'])) . ")");
		if (!$this->_db->execute())
		{
			return false;
		}
		return true;
	}

	/**
	 * Retrieve form content for use in page design
	 *
	 * @param   int  $formId  From id
	 *
	 * @param   int  $action  Form action
	 *
	 * @return Object
	 */
	private function _getEmailTemplate($formId, $action)
	{
		if (!empty($formId) && is_numeric($formId))
		{
			$this->_db->setQuery($this->_db->getQuery(true)->select('*')->from('#__jsn_uniform_templates')->where('form_id=' . intval($formId) . ' AND template_notify_to = ' . intval($action)));
			return $this->_db->loadObject();

		}
	}

	/**
	 * Retrieve form content for use in page design
	 *
	 * @param   int  $action  Form action
	 *
	 * @return Object
	 */
	private function _getEmailSettingsConfig($action)
	{
		$id = $action?"list_email":"email_submitter";
		$this->_db->setQuery($this->_db->getQuery(true)->select('value')->from('#__jsn_uniform_config')->where('name = "' . $id . '"'));
		return $this->_db->loadObject();
	}

	/**
	 * Get data default configuration
	 *
	 * @return Objectlist
	 */
	public function getDataConfig()
	{
		$this->_db->setQuery($this->_db->getQuery(true)->select('*')->from("#__jsn_uniform_config"));
		return $this->_db->loadObjectList();
	}
}
