<?php

/**
 * @version     $Id: submissions.php 19013 2012-11-28 04:48:47Z thailv $
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

jimport('joomla.application.component.modellist');


/**
 * JSNUniform model Submissions
 *
 * @package     Models
 * @subpackage  Submissions
 * @since       1.6
 */
class JSNUniformModelSubmissions extends JModelList
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return    string    An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$filterFormId = $this->getState('filter.filter_form_id');
		// Select some fields
		$query->select('distinct sb.*');
		$query->from('#__jsn_uniform_submissions AS sb');
		//$query->join('INNER', '#__jsn_uniform_submission_data AS sd ON sd.submission_id = sb.submission_id');
		$query->select('us.username as submission_created_by');
		$query->join('LEFT', '#__users AS us ON us.id = sb.submission_created_by');
		$query->join('LEFT', '#__jsn_uniform_submission_data AS sd ON sb.submission_id = sd.submission_id');
		// Filter by search in title
		$search = $this->getState('filter.search');
		$dateSubmission = $this->getState('filter.date_submission');
		$where = "";
		// Filter by search in title
		if (!empty($dateSubmission))
		{
			$dateSubmission = @explode(" - ", $dateSubmission);
			$dateStart = @explode("/", $dateSubmission[0]);
			$dateStart = @$dateStart[2] . "-" . @$dateStart[0] . "-" . @$dateStart[1];
			if (@$dateSubmission[1])
			{
				$dateEnd = @explode("/", $dateSubmission[1]);
				$dateEnd = @$dateEnd[2] . "-" . @$dateEnd[0] . "-" . @$dateEnd[1];
				$query->where('( date(submission_created_at) BETWEEN ' . $db->quote($dateStart) . ' AND ' . $db->quote($dateEnd) . ')', 'AND');
			}
			else
			{
				$query->where(' date(submission_created_at) = ' . $db->quote($dateStart), 'AND');
			}
		}
		if (!empty($search))
		{
			if (stripos($search, 'submission_id:') === 0)
			{
				$query->where('sb.submission_id = ' . (int) substr($search, 3));
			}
			else
			{
				$listviewField = $this->getState('filter.list_view_field');
				$search = $db->escape($search, true);
				$search = str_replace("  ", " ", $search);
				$search = str_replace(" ", "%", $search);
				$search = $db->Quote('%' . $search . '%');
				if ($listviewField)
				{
					$listviewField = str_replace(array('"', "'"), '', $listviewField);
					$listviewField = explode(",", $listviewField);
					foreach ($listviewField as $viewField)
					{
						if (strpos($viewField, "sd_") === false)
						{
							$where[] = '(' . preg_replace('/[^a-z0-9-._]/i', "", "sb." . $viewField) . ' LIKE ' . $search . ')';
						}
						else
						{
							$fieldId = (int) str_replace("sd_", "", $viewField);
							$where[] = '(sd.field_id = ' . $fieldId . ' AND sd.submission_data_value LIKE ' . $search . ')';
						}
					}
				}
			}
		}
		if ($where)
		{
			$query->where("(" . implode(" OR ", $where) . ")", 'AND');
		}
		$edition = defined('JSN_UNIFORM_EDITION') ? strtolower(JSN_UNIFORM_EDITION) : "free";
		if ($edition == "free")
		{
			$db->setQuery(
				$db->getQuery(true)
					->select('submission_id')
					->from("#__jsn_uniform_submission_data")
					->order('submission_id ASC')
					->where('form_id =' . (int) $filterFormId, 'AND')
				, 300, 1
			);
			$maxId = $db->loadResult();
			if (!empty($maxId))
			{
				$query->where('sb.submission_id <' . (int) $maxId, 'AND');
			}
		}
		$query->where('sb.form_id = ' . (int) $filterFormId, 'AND');
		$order = $this->getState('list.ordering', 'sb.submission_id');

		if (strpos($order, "sd.sd_") === false)
		{
			$query->order($db->escape($this->getState('list.ordering', 'sb.submission_id')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
		}
		else
		{
			$fielId = str_replace("sd.sd_", "", $order);
			$query->where('sd.field_id = ' . (int) $fielId, 'AND');
			$query->order($db->escape('sd.submission_data_value') . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
		}
		return $query;
	}
	/**
	 * Returns a record count for the query.
	 *
	 * @param   JDatabaseQuery|string  $query  The query.
	 *
	 * @return  integer  Number of rows for query.
	 *
	 * @since   12.2
	 */
	protected function _getListCount($query)
	{
		// Otherwise fall back to inefficient way of counting all results.
		$this->_db->setQuery($query);
		$this->_db->execute();
		return (int) $this->_db->getNumRows();
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
	public function getItems()
	{
		$db = JFactory::getDBO();
		$getItems = parent::getItems();
		$items = array();
		foreach ($getItems as $item)
		{
			$query = $db->getQuery(true)
				->select('*')
				->from("#__jsn_uniform_submission_data")
				->where('submission_id =' . (int) $item->submission_id);
			$db->setQuery($query);
			$submissions = $db->loadObjectList();
			foreach ($submissions as $submission)
			{
				$item->{"sd_" . $submission->field_id} = $submission->submission_data_value;
			}
			$items[] = $item;
		}
		return $items;
	}
	/**
	 * Method to get data export.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   11.1
	 */
	public function getExport()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$queryString = $this->_getListQuery();
		$db->setQuery($queryString);
		$getItems = $db->loadObjectList();

		$items = array();
		foreach ($getItems as $item)
		{
			$query = $db->getQuery(true)
				->select('*')
				->from("#__jsn_uniform_submission_data")
				->where('submission_id =' . (int) $item->submission_id);
			$db->setQuery($query);
			$submissions = $db->loadObjectList();
			foreach ($submissions as $submission)
			{
				$item->{"sd_" . $submission->field_id} = $submission->submission_data_value;
			}
			$items[] = $item;
		}
		return $items;
	}
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 *
	 * to be called on the first call to the getState() method unless the model
	 *
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		$post = $_POST;

		$filterFormId = $this->getUserStateFromRequest($this->context . '.filter.filter_form_id', 'filter_form_id', '', 'string');
		$this->setState('filter.filter_form_id', $filterFormId);
		if (!empty($post['old_form_id']) && $post['old_form_id'] != $filterFormId)
		{
			$this->setState('filter.list_view_field', "");
			$this->setState('filter.position_field', "");
			$this->setState('filter.position_title_field', "");
			$this->setState('filter.search', "");
			$this->setState('filter.date_submission', "");
		}
		else
		{
			$listViewField = $this->getUserStateFromRequest($this->context . '.filter.list_view_field', 'list_view_field', '', 'string');
			$this->setState('filter.list_view_field', $listViewField);

			$filterPositionField = $this->getUserStateFromRequest($this->context . '.filter.position_field', 'filter_position_field', '', 'string');
			$this->setState('filter.position_field', $filterPositionField);

			$filterPositionTitleField = $this->getUserStateFromRequest($this->context . '.filter.position_title_field', 'filter_position_title_field', '', 'string');
			$this->setState('filter.position_title_field', $filterPositionTitleField);

			$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
			$this->setState('filter.search', $search);

			$dateSubmission = $this->getUserStateFromRequest($this->context . '.filter.date_submission', 'filter_date_submission');
			$this->setState('filter.date_submission', $dateSubmission);
		}

		$filterFields = array(
			'sb.submission_ip', 'sb.submission_country', 'sb.submission_browser', 'sb.submission_os', 'sb.submission_created_by'
		, 'sb.submission_created_at', 'sb.submission_state'
		);
		$getFieldForm = $this->GetFieldsForm();
		if (!empty($getFieldForm))
		{
			foreach ($getFieldForm as $field)
			{
				$filterFields[] = "sd.sd_" . $field->field_id;
			}
		}

		$this->filter_fields = $filterFields;

		// List state information.
		parent::populateState('sb.submission_id', 'DESC');
	}

	/**
	 * Retrieve fields from for use in page list submission
	 *
	 * @return Object List
	 */
	public function GetFieldsForm()
	{
		$formId = $this->getUserStateFromRequest($this->context . '.filter.filter_form_id', 'filter_form_id', '', 'string');
		if (!empty($formId) && is_numeric($formId))
		{
			$this->_db->setQuery(
				$this->_db->getQuery(true)
					->select('*')
					->from('#__jsn_uniform_fields')
					->where('form_id=' . intval($formId))
					->where('field_type!="static-heading"')
					->where('field_type!="paragraph-text"')
					->order('field_id DESC')
			);
			return $this->_db->loadObjectList();
		}
	}

	/**
	 * get data Pages
	 *
	 * @return Object
	 */
	public function getDataPages()
	{
		$filterFormId = $this->getState('filter.filter_form_id');
		$this->_db->setQuery(
			$this->_db->getQuery(true)
				->select('*')
				->from("#__jsn_uniform_form_pages")
				->where("form_id=" . (int) $filterFormId)
		);
		return $this->_db->loadObjectList();
	}

	/**
	 * get count submission
	 *
	 * @return Object
	 */
	public function getCountSubmission()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$filterFormId = $this->getState('filter.filter_form_id');
		$db->setQuery(
			$db->getQuery(true)
				->select('count(submission_id)')
				->from("#__jsn_uniform_submissions")
				->where("form_id = " . (int) $filterFormId)
		);
		return $db->loadResult();
	}

	/**
	 * get info form
	 *
	 * @return Object
	 */
	public function getInfoForm()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$filterFormId = $this->getState('filter.filter_form_id');
		$db->setQuery(
			$db->getQuery(true)
				->select('*')
				->from('#__jsn_uniform_forms')
				->where('form_id=' . (int) $filterFormId)
		);
		return $db->loadObject();
	}
}
