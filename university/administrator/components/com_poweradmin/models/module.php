<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: module.php 12779 2012-05-18 02:55:18Z binhpt $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JSNFactory::import('components.com_modules.models.module');

class PoweradminModelModule extends ModulesModelModule
{
	/**
	 * Duplicate module
	 * 
	 * @param: Array $pks
	 */
	public function duplicate(&$pks)
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$db		= $this->getDbo();

		// Access checks.
		if (!$user->authorise('core.create', 'com_modules')) {
			throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			if ($table->load($pk, true)) {
				// Reset the id to create a new record.
				$table->id = 0;

				// Alter the title.			
				$table->title = JText::_('JSN_DUPLICATE').$table->title;
				
				// Unpublish duplicate module
				$table->published = 0;
				if (!$table->check() || !$table->store()) {
					throw new Exception($table->getError());
				}
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select("id, ordering");
				$query->from('#__modules');
				$query->where("position = ".$db->quote($table->position));
				$query->where(" ( ordering > ".$table->ordering." OR ordering = ".$table->ordering.")");
				$query->where("id <> ".$table->id." AND id <> ".$pk." AND published <> '-2' ");
				$query->order('ordering ASC, title DESC, id DESC');
				$db->setQuery($query);
				$rows = $db->loadObjectList();
				$offset = $table->ordering + 2;
				for($i = 0, $n = count($rows); $i < $n; $i++){
					$row = $rows[$i];
					$query->clear();
					$query->update("#__modules");
					$query->set("ordering = ".$db->quote($offset + $i));
					$query->where("id=".$row->id);
					$db->setQuery($query);
					$db->query();
				}
				$table->ordering += 1;
				$table->store();
				$query->clear();
				$query->select('menuid');
				$query->from('#__modules_menu');
				$query->where('moduleid='.(int)$pk);
				$this->_db->setQuery((string)$query);
				$rows = $this->_db->loadColumn();

				foreach ($rows as $menuid)
				{
					$tuples[] = '('.(int) $table->id.','.(int) $menuid.')';
				}

				$this->setState('module.id', $table->id);
			}
			else {
				throw new Exception($table->getError());
			}
		}

		if (!empty($tuples)) {
			// Module-Menu Mapping: Do it in one query
			$query = 'INSERT INTO #__modules_menu (moduleid,menuid) VALUES '.implode(',', $tuples);
			$this->_db->setQuery($query);

			if (!$this->_db->query()) {
				return JError::raiseWarning(500, $row->getError());
			}
		}

		// Clear modules cache
		$this->cleanCache();

		return true;
	}
	/**
	 * 
	 * Get state store
	 * @param String $stateKey
	 */
	public function getState( $stateKey = '', $default = null)
	{
		return parent::getState( $stateKey, $default );
	}
    /**
	 * Method to delete rows.
	 *
	 * @param	array	$pks	An array of item ids.
	 *
	 * @return	boolean	Returns true on success, false on failure.
	 * @since	1.6
	 */
	public function delete(&$pks)
	{
		// Initialise variables.
		$pks	= (array) $pks;
		$user	= JFactory::getUser();
		$table	= $this->getTable();

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk)) {
				$table->published = '-2';
				if ( !$table->store() ) {
					throw new Exception($table->getError());
				}
				else {
					// Delete the menu assignments
					$db		= $this->getDbo();
					$query	= $db->getQuery(true);
					$query->delete();
					$query->from('#__modules_menu');
					$query->where('moduleid='.(int)$pk);
					$db->setQuery((string)$query);
					$db->query();
				}

				// Clear module cache
				parent::cleanCache($table->module, $table->client_id);
			}
			else {
				throw new Exception($table->getError());
			}
		}

		// Clear modules cache
		$this->cleanCache();

		return true;
	}
	
    /**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// The folder and element vars are passed when saving the form.
		if (empty($data)) {
			$item		= $this->getItem();
			$clientId	= $item->client_id;
			$module		= $item->module;
		}else{
			$clientId	= JArrayHelper::getValue($data, 'client_id');
			$module		= JArrayHelper::getValue($data, 'module');
		}

		// These variables are used to add data from the plugin XML files.
		$this->setState('item.client_id',	$clientId);
		$this->setState('item.module',		$module);

		// Get the form.
		$form = $this->loadForm('com_poweradmin.module', 'module', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		$form->setFieldAttribute('position', 'client', $this->getState('item.client_id') == 0 ? 'site' : 'administrator');

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
		}

		return $form;
	}
	/**
	 * 
	 * Save data to databse
	 * 
	 * @param: Array $data
	 */
	public function save($data)
	{
		parent::save($data);

		//Get update/insert module id
		return $this->getState('module.id');
	}
	
	/**
	 * Custom clean cache method for different clients
	 *
	 * @since	1.6
	 */
	protected function cleanCache($group = null, $client_id = 0) {
		parent::cleanCache('com_poweradmin', $this->getClient());
	}
}