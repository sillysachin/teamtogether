<?php

/**
 * @version     $Id: forms.php 19014 2012-11-28 04:48:56Z thailv $
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
 * JSNUniform model Forms
 *
 * @package     Models
 * @subpackage  Forms
 * @since       1.6
 */
class JSNUniformModelForms extends JModelList
{

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   11.1
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
			'uf.form_id', 'uf.form_state', 'uf.form_title',
			'uf.form_access', 'uf.form_created_by', 'uf.form_created_at',
			'uf.form_submission_cout', 'uf.form_last_submitted'
			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return string An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		// Select some fields
		$query->select('uf.*');

		$query->from('#__jsn_uniform_forms AS uf');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = uf.form_access');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=uf.form_created_by');
		// Filter by search in title
		$search = $this->getState('filter.search');
		// Filter by published state
		$state = $this->getState('filter.state');
		if (is_numeric($state))
		{
			$query->where('uf.form_state = ' . (int) $state, 'AND');
		}
		elseif ($state === '')
		{
			$query->where('(uf.form_state IN (0,1))');
		}
		// Filter by search in title
		if (!empty($search))
		{
			if (stripos($search, 'form_id:') === 0)
			{
				$query->where('uf.form_id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('((uf.form_title LIKE ' . $search . ' ) OR (uf.form_description LIKE ' . $search . ' ))', ' AND ');
			}
		}
		$query->order($db->escape($this->getState('list.ordering', 'uf.form_id')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
		return $query;
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

		$state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState('uf.form_id', 'DESC');
	}

}
