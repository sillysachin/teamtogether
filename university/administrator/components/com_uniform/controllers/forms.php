<?php

/**
 * @version     $Id: forms.php 19014 2012-11-28 04:48:56Z thailv $
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
/**
 * Forms controllers of JControllerForm
 * 
 * @package     Controllers
 * @subpackage  Forms
 * @since       1.6
 */
class JSNUniformControllerForms extends JControllerAdmin
{

	protected $option = JSN_UNIFORM;
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		// Get input object
		$this->input = JFactory::getApplication()->input;

		parent::__construct($config);
	}
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
	public function getModel($name = 'form', $prefix = 'JSNUniformModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Removes an item.
	 *
	 * @return  void
	 */
	public function delete()
	{

		// Get items to remove from the request.
		$cid = $this->input->getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(JText::plural("JSN_UNIFORM_FORMS_DELETED", count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}

	/**
	 *  view select form
	 * 
	 * @return html code
	 */
	public function viewSelectForm()
	{

		$document = JFactory::getDocument();
		$document->addScript('../media/system/js/mootools-core.js');
		$document->addScript('../media/system/js/core.js');
		$document->addScript('../media/system/js/mootools-more.js');
		//$document->addStyleSheet(JSN_URL_ASSETS . '/3rd-party/bootstrap/css/bootstrap.min.css');
		echo JSNUniformHelper::getSelectForm('jform[params][form_id]', 'jform_params_form_id', "content");
	}
	/**
	 * get List form
	 *
	 * @return json
	 */
	public function getListFieldByForm()
	{
		$formId = isset($_GET["form_id"])?$_GET["form_id"]:0;
		$listField = JSNUniformHelper::getListFieldByForm($formId);
		echo json_encode($listField);
		jexit();
	}
	/**
	 * get List form 
	 */
	public function getListForm()
	{
		$listForm = JSNUniformHelper::getForms();
		echo json_encode($listForm);
		jexit();
	}

	/**
	 * Method to clone an existing module.
	 * @since	1.6
	 */
	public function duplicate()
	{

		// Initialise variables.
		$pks = $this->input->getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($pks);

		try
		{
			if (empty($pks))
			{
				throw new Exception(JText::_('JSN_UNIFORM_ERROR_NO_FORM_SELECTED'));
			}
			$model = $this->getModel();
			$model->duplicate($pks);
			$this->setMessage(JText::plural('JSN_UNIFORM_N_FORMS_DUPLICATED', count($pks)));
		}
		catch (Exception $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		$this->setRedirect('index.php?option=com_uniform&view=forms');
	}

}
