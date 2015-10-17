<?php

/**
 * @version     $Id: submission.php 19013 2012-11-28 04:48:47Z thailv $
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

jimport('joomla.application.component.modeladmin');
jimport('joomla.html.parameter');

/**
 * JSNUniform model Submission
 *
 * @package     Models
 * @subpackage  Submission
 * @since       1.6
 */
class JSNUniformModelSubmission extends JModelAdmin
{

	protected $option = JSN_UNIFORM;
	var $_formId;
	var $_submissionId;

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
	public function getTable($type = 'submission', $prefix = 'JSNUniformTable', $config = array())
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
		$form = $this->loadForm('com_uniform.submission', 'submission', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
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
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		$table = $this->getTable();

		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
		$item = JArrayHelper::toObject($properties, 'JObject');

		if (property_exists($item, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}
		$edition = defined('JSN_UNIFORM_EDITION') ? strtolower(JSN_UNIFORM_EDITION) : "free";
		if (isset($item->form_id) && isset($item->submission_id) && $edition == "free")
		{
			$this->_db->setQuery(
				$this->_db->getQuery(true)
					->select('submission_id')
					->from("#__jsn_uniform_submissions")
					->order('submission_id ASC')
					->where('form_id =' . (int) $item->form_id, 'AND')
				, 300, 1
			);
			$maxId = $this->_db->loadResult();
			if (!empty($maxId) && $maxId < $item->submission_id)
			{
				return false;
			}
		}
		$this->_formId = $item->form_id;
		$this->_submissionId = $item->submission_id;
		$this->_input = JFactory::getApplication()->input;
		$menu = JFactory::getApplication()->getMenu();
		$menuItem = $menu->getItem((int) $this->_input->get("Itemid"));
		$params = json_decode($menuItem->params);
		$formId = !empty($params->form_id) ? $params->form_id : "0";
		if ($formId != $item->form_id)
		{
			$item = new stdClass;
		}

		return $item;
	}

	/**
	 * Retrieve fields for use in page submitted detail
	 *
	 * @return Object List
	 */
	public function getDataFields()
	{
		if (!empty($this->_formId) && is_numeric($this->_formId))
		{
			$this->_db->setQuery(
				$this->_db->getQuery(true)
					->select('fi.field_identifier,fi.field_title,fo.form_title,fo.form_id,fi.field_type,fi.field_id')
					->from('#__jsn_uniform_fields AS fi')
					->join('INNER', '#__jsn_uniform_forms AS fo ON fo.form_id = fi.form_id')
					->where('fi.form_id=' . intval($this->_formId))
					->order('fi.field_ordering ASC')
			);
			return $this->_db->loadObjectList();
		}
	}

	/**
	 * Retrieve submission for use in page submitted detail
	 *
	 * @return Object
	 */
	public function getDataSubmission()
	{
		if (!empty($this->_formId) && is_numeric($this->_formId) && !empty($this->_submissionId) && is_numeric($this->_submissionId))
		{
			$item = new stdClass;
			$query = $this->_db->getQuery(true)
				->select('*')
				->from("#__jsn_uniform_submission_data")
				->where('submission_id =' . (int) $this->_submissionId);
			$this->_db->setQuery($query);
			$submissions = $this->_db->loadObjectList();
			foreach ($submissions as $submission)
			{
				$item->{"sd_" . $submission->field_id} = $submission->submission_data_value;
			}
			return $item;
		}
	}

	/**
	 *  get info form
	 *
	 * @return type
	 */
	public function getInfoForm()
	{
		if (!empty($this->_formId) && is_numeric($this->_formId) && !empty($this->_submissionId) && is_numeric($this->_submissionId))
		{
			$this->_db->setQuery($this->_db->getQuery(true)->select('*')->from("#__jsn_uniform_forms")->where('form_id=' . intval($this->_formId)));

			return $this->_db->loadObject();
		}
	}

	/**
	 * get all page in form
	 *
	 * @return Object list
	 */
	public function getFormPages()
	{
		if (!empty($this->_formId) && is_numeric($this->_formId))
		{
			$this->_db->setQuery($this->_db->getQuery(true)->select('*')->from("#__jsn_uniform_form_pages")->where('form_id=' . intval($this->_formId)));
			return $this->_db->loadObjectList();
		}
	}

	/**
	 * getNextAndPreviousForm
	 *
	 * @return type
	 */
	public function getNextAndPreviousForm()
	{
		$formList = array();
		if (!empty($this->_formId) && is_numeric($this->_formId) && !empty($this->_submissionId) && is_numeric($this->_submissionId))
		{
			$this->_db->setQuery(
				$this->_db->getQuery(true)
					->select('submission_id')
					->from("#__jsn_uniform_submission_data")
					->where('form_id = ' . (int) $this->_formId)
					->order('submission_id ASC')
				, 300, 1
			);
			$maxId = $this->_db->loadResult();
			$edition = defined('JSN_UNIFORM_EDITION') ? strtolower(JSN_UNIFORM_EDITION) : "free";
			if ($this->_submissionId + 1 < $maxId || empty($maxId) || $edition != "free")
			{
				$this->_db->setQuery($this->_db->getQuery(true)->select('submission_id')->from("#__jsn_uniform_submission_data")->where('form_id = ' . (int) $this->_formId)->where('submission_id > ' . intval($this->_submissionId))->order('`submission_id` ASC'), 0, 1);
				$formList['next'] = $this->_db->loadResult();
			}
			$this->_db->setQuery($this->_db->getQuery(true)->select('submission_id')->from("#__jsn_uniform_submission_data")->where('form_id = ' . (int) $this->_formId)->where('submission_id < ' . intval($this->_submissionId))->order('`submission_id` DESC'), 0, 1);
			$formList['previous'] = $this->_db->loadResult();
		}
		return $formList;
	}


	/**
	 * Override save method to save form fields to database
	 *
	 * @return boolean
	 */
	public function save($data)
	{
		$formId = !empty($data['filter_form_id']) ? $data['filter_form_id'] : 0;
		$this->_db->setQuery($this->_db->getQuery(true)->select('form_access')->from("#__jsn_uniform_forms")->where('form_id=' . intval($formId)));
		$infoForm = $this->_db->loadObject();
		$user = JFactory::getUser();
		$groupEditSubmision = isset($infoForm->form_access) ? $infoForm->form_access : "";
		$checkEditSubmission = JSNUniformHelper::checkEditSubmission($user->id, $groupEditSubmision);
		if ($checkEditSubmission)
		{

			if (isset($_POST['submission']) && is_array($_POST['submission']) && isset($_POST['filter_form_id']) && isset($_POST['cid']))
			{
				foreach ($_POST['submission'] as $key => $value)
				{
					if (is_array($value) && !empty($value['likert']))
					{
						$data = array();
						foreach ($value as $items)
						{
							if (isset($items))
							{
								foreach ($items as $k => $item)
								{
									$data[$k] = $item;
								}
							}
						}
						$value = $data ? json_encode($data) : "";
					}
					$query = $this->_db->getQuery(true);
					$query->update($this->_db->quoteName("#__jsn_uniform_submission_data"));
					$query->set("submission_data_value = " . $this->_db->Quote($value));
					$query->where('submission_id = ' . (int) $_POST['cid']);
					$query->where('field_id = ' . (int) str_replace("sd_", "", $key));
					$this->_db->setQuery($query);
					$this->_db->execute();
				}
			}
		}
		return true;
	}
}
