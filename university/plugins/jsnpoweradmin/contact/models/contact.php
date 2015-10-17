<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN PowerAdmin support for com_content
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');

JSNFactory::import('components.com_contact.models.contact', 'site');
JSNFactory::import('components.com_contact.models.category', 'site');
JSNFactory::import('components.com_contact.helpers.route', 'site');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin extend com_content
 * @since		1.7
 */
class PoweradminContactModelContact extends ContactModelContact
{
	/**
	 *
	 * Get params of current view
	 */
	protected function populateState()
	{
		// Load the parameters.
		$params = JComponentHelper::getParams('com_contact');
		$this->setState('params', $params);
	}

	public function getItem( $pk = Array() )
	{
		$item = parent::getItem( $pk['id'] );
		return $item;
	}


	public function getForm($data = array(), $loadData = true)
	{
		$form = JForm::getInstance('contactform', JPATH_ROOT .  '/components/com_contact/models/forms/contact.xml');
		if (empty($form)) {
			return false;
		}
		$form->removeField("captcha");

		$id = $this->getState('contact.id');
		$params = $this->getState('params');
		$contact = $this->_item[$id];
		$params->merge($contact->params);

		if(!$params->get('show_email_copy', 0)){
			$form->removeField('contact_email_copy');
		}
		return $form;
	}
	/**
	 *
	 * Get data
	 * @param Array $pk
	 */
	public function prepareDisplayedData( $pk )
	{
		$data = null;
		$item  = $this->getItem($pk);
		$state = $this->getState();
		$form	= $this->getForm();
		// Get the parameters
		$params = JComponentHelper::getParams('com_contact');
		if ($item) {
			// If we found an item, merge the item parameters
			$params->merge($item->params);

			// Get Category Model data
			$categoryModel = JModelLegacy::getInstance('Category', 'ContactModel', array('ignore_request' => true));
			$categoryModel->setState('category.id', $item->catid);
			$categoryModel->setState('list.ordering', 'a.name');
			$categoryModel->setState('list.direction', 'asc');
			$categoryModel->setState('filter.published', 1);

			$contacts = $categoryModel->getItems();
		}

		// Handle email cloaking
		if ($item->email_to && $params->get('show_email')) {
			$item->email_to = JHtml::_('email.cloak', $item->email_to);
		}
		if ($params->get('show_street_address') || $params->get('show_suburb') || $params->get('show_state') || $params->get('show_postcode') || $params->get('show_country')) {
			if (!empty ($item->address) || !empty ($item->suburb) || !empty ($item->state) || !empty ($item->country) || !empty ($item->postcode)) {
				$params->set('address_check', 1);
			}
		}
		else {
			$params->set('address_check', 0);
		}

		$params->set('marker_address',	JText::_('COM_CONTACT_ADDRESS').": ");
		$params->set('marker_email',		JText::_('JGLOBAL_EMAIL').": ");
		$params->set('marker_telephone',	JText::_('COM_CONTACT_TELEPHONE').": ");
		$params->set('marker_fax',		JText::_('COM_CONTACT_FAX').": ");
		$params->set('marker_mobile',		JText::_('COM_CONTACT_MOBILE').": ");
		$params->set('marker_misc',		JText::_('COM_CONTACT_OTHER_INFORMATION').": ");
		$params->set('marker_class',		'jicons-text');

		$JSNConfig = JSNFactory::getConfig();
		$JSNConfig->megreMenuParams( $pk['Itemid'], $params, 'com_contact' );
		$JSNConfig->megreGlobalParams( 'com_contact', $params, true );

		$data->params	= $params;
		$data->return	= $return;
		$data->state	= $state;
		$data->item		= $item;
		$data->contacts	= $contacts;
		$data->form		= $form;
		return $data;
	}

	// Override getContactQuery function to avoid language problem
	protected function getContactQuery($pk = null)
	{
		// TODO: Cache on the fingerprint of the arguments
		$db		= $this->getDbo();
		$user	= JFactory::getUser();
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('contact.id');

		$query	= $db->getQuery(true);
		if ($pk) {
			//sqlsrv changes
			$case_when = ' CASE WHEN ';
			$case_when .= $query->charLength('a.alias');
			$case_when .= ' THEN ';
			$a_id = $query->castAsChar('a.id');
			$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
			$case_when .= ' ELSE ';
			$case_when .= $a_id.' END as slug';

			$case_when1 = ' CASE WHEN ';
			$case_when1 .= $query->charLength('cc.alias');
			$case_when1 .= ' THEN ';
			$c_id = $query->castAsChar('cc.id');
			$case_when1 .= $query->concatenate(array($c_id, 'cc.alias'), ':');
			$case_when1 .= ' ELSE ';
			$case_when1 .= $c_id.' END as catslug';
			$query->select('a.*, cc.access as category_access, cc.title as category_name, '
					.$case_when.','.$case_when1);

			$query->from('#__contact_details AS a');

			$query->join('INNER', '#__categories AS cc on cc.id = a.catid');

			$query->where('a.id = ' . (int) $pk);
			$published = $this->getState('filter.published');
			$archived = $this->getState('filter.archived');
			if (is_numeric($published)) {
				$query->where('a.published IN (1,2)');
				$query->where('cc.published IN (1,2)');
			}
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');

			try {
				$db->setQuery($query);
				$result = $db->loadObject();

				if ($error = $db->getErrorMsg()) {
					throw new Exception($error);
				}

				if (empty($result)) {
					throw new JException(JText::_('COM_CONTACT_ERROR_CONTACT_NOT_FOUND'), 404);
				}

				// If we are showing a contact list, then the contact parameters take priority
				// So merge the contact parameters with the merged parameters
				if ($this->getState('params')->get('show_contact_list')) {
					$registry = new JRegistry;
					$registry->loadString($result->params);
					$this->getState('params')->merge($registry);
				}
			} catch (Exception $e) {
				$this->setError($e);
				return false;
			}

			if ($result) {
				$user	= JFactory::getUser();
				$groups	= implode(',', $user->getAuthorisedViewLevels());

				//get the content by the linked user
				$query	= $db->getQuery(true);
				$query->select('a.id');
				$query->select('a.title');
				$query->select('a.state');
				$query->select('a.access');
				$query->select('a.created');

				// SQL Server changes
				$case_when = ' CASE WHEN ';
				$case_when .= $query->charLength('a.alias');
				$case_when .= ' THEN ';
				$a_id = $query->castAsChar('a.id');
				$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
				$case_when .= ' ELSE ';
				$case_when .= $a_id.' END as slug';
				$case_when1 = ' CASE WHEN ';
				$case_when1 .= $query->charLength('c.alias');
				$case_when1 .= ' THEN ';
				$c_id = $query->castAsChar('c.id');
				$case_when1 .= $query->concatenate(array($c_id, 'c.alias'), ':');
				$case_when1 .= ' ELSE ';
				$case_when1 .= $c_id.' END as catslug';
				$query->select($case_when1 . ',' . $case_when);

				$query->from('#__content as a');
				$query->leftJoin('#__categories as c on a.catid=c.id');
				$query->where('a.created_by = '.(int)$result->user_id);
				$query->where('a.access IN ('. $groups.')');
				$query->order('a.state DESC, a.created DESC');

				if (is_numeric($published)) {
					$query->where('a.state IN (1,2)');
				}
				$db->setQuery($query, 0, 10);
				$articles = $db->loadObjectList();
				$result->articles = $articles;

				//get the profile information for the linked user
				require_once JPATH_ADMINISTRATOR.'/components/com_users/models/user.php';
				$userModel = JModelLegacy::getInstance('User', 'UsersModel', array('ignore_request' => true));
				$data = $userModel->getItem((int)$result->user_id);

				JPluginHelper::importPlugin('user');
				$form = new JForm('com_users.profile');
				// Get the dispatcher.
				$dispatcher	= JDispatcher::getInstance();

				// Trigger the form preparation event.
				$dispatcher->trigger('onContentPrepareForm', array($form, $data));
				// Trigger the data preparation event.
				$dispatcher->trigger('onContentPrepareData', array('com_users.profile', $data));

				// Load the data into the form after the plugins have operated.
				$form->bind($data);
				$result->profile = $form;

				$this->contact = $result;
				return $result;
			}
		}
	}

}