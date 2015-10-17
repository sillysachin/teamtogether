<?php

/**
 * @version     $Id: form.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Controller
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Form controllers of JControllerForm
 *
 * @package     Controllers
 * @subpackage  Form
 * @since       1.6
 */
class JSNUniformControllerForm extends JSNBaseController
{

	protected $option = JSN_UNIFORM;

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

	/**
	 * Save data submission
	 *
	 * @return Html messages
	 */
	public function save()
	{
		// Check for request forgeries.
		if (@$_SERVER['CONTENT_LENGTH'] < (int) (ini_get('post_max_size')) * 1024 * 1024)
		{
			//   JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
			$return = new stdClass;
			$post = $_POST;
			$model = $this->getModel('form');
			if (!empty($_POST['form_id']) && JSNUniformHelper::checkStateForm($_POST['form_id']))
			{
				$return = $model->save($post);
			}
			if (isset($return->error))
			{
				//echo json_encode(array('error' => $return->error));
				echo '<input type="hidden" name="error" value=\'' . htmlentities(json_encode($return->error), ENT_QUOTES, "UTF-8") . '\'/>';
				exit();
			}
			else
			{
				if (isset($return->actionForm) && $return->actionForm == 'message')
				{
					//	echo json_encode(array('message' => $return->actionFormData));
					echo '<input type="hidden" name="message" value=\'' . htmlentities($return->actionFormData, ENT_QUOTES, "UTF-8") . '\'/>';
					exit();
				}
				elseif (isset($return->actionForm) && $return->actionForm == 'url')
				{
					//echo "<div class=\"src-redirect\">{$return->actionFormData}</div>";
					echo '<input type="hidden" name="redirect" value=\'' . htmlentities($return->actionFormData, ENT_QUOTES, "UTF-8") . '\'/>';
					exit();
				}
				else
				{
					exit();
				}
			}
		}
		else
		{
			$postMaxSize = (int) ini_get('post_max_size');
			if ($postMaxSize > (int) (ini_get('upload_max_filesize')))
			{
				$postMaxSize = (int) (ini_get('upload_max_filesize'));
			}
			//echo json_encode(array('error' => array('max-upload' => JText::sprintf('JSN_UNIFORM_POST_MAX_SIZE', $postMaxSize))));
			echo '<input type="hidden" name="error" value=\'' . htmlentities(json_encode(array('max-upload' => JText::sprintf('JSN_UNIFORM_POST_MAX_SIZE', $postMaxSize))), ENT_QUOTES, "UTF-8") . '\'/>';
			exit();
		}
	}

	/**
	 *     get html form
	 *
	 * @return string
	 */
	function getHtmlForm()
	{
		$formId = JRequest::getVar('form_id', '');
		if ($formId)
		{
			$formName = md5(date("Y-m-d H:i:s")) . rand(0, 1000);
			echo JSNUniformHelper::generateHTMLPages($formId, $formName, "ajax");
			exit();
		}
	}

}
