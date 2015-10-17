<?php
defined('_JEXEC') or die('Access denied');

/*------------------------------------------------------------------------
# Full Name of JSN Extension(e.g: JSN PowerAdmin)
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
# @license - GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
# @version $Id$
-------------------------------------------------------------------------*/

jimport('joomla.base.tree');

class JSNAdministratorMenu extends JTree
{
	/**
	 * CSS string to add to document head
	 * @var string
	 */
	protected $_css = null;

	function __construct()
	{
		$this->_root = new JAdministratorMenuNode('ROOT');
		$this->_current = & $this->_root;
	}

	function addSeparator()
	{
		$this->addChild(new JAdministratorMenuNode(null, null, 'separator', false));
	}

	function renderMenu($id = 'menu', $class = '')
	{
		$depth = 1;

		if (!empty($id)) {
			$id='id="'.$id.'"';
		}

		if (!empty($class)) {
			$class='class="'.$class.'"';
		}

		/*
		 * Recurse through children if they exist
		 */
		while ($this->_current->hasChildren())
		{
			echo "<ul ".$id." ".$class.">\n";
			foreach ($this->_current->getChildren() as $child)
			{
				$this->_current = & $child;
				$this->renderLevel($depth++);
			}
			echo "</ul>\n";
		}

		if ($this->_css) {
			// Add style to document head
			$doc = JFactory::getDocument();
			$doc->addStyleDeclaration($this->_css);
		}
	}

	function renderLevel($depth)
	{
		/*
		 * Build the CSS class suffix
		 */
		$class = '';
		if ($this->_current->hasChildren()) {
			$class = ' class="has-child"';
		}

		if ($this->_current->class == 'separator') {
			$class = ' class="separator"';
		}

		if ($this->_current->class == 'disabled') {
			$class = ' class="disabled"';
		}

		/*
		 * Print the item
		 */
		echo "<li".$class.">";

		/*
		 * Print a link if it exists
		 */

		$linkClass = '';

		if ($this->_current->link != null) {
			$linkClass = $this->getIconClass($this->_current->class);
			if (!empty($linkClass)) {
				$linkClass = ' class="'.$linkClass.'"';
			}
		}

		if ($this->_current->link != null && $this->_current->target != null) {
			echo "<a".$linkClass." href=\"".$this->_current->link."\" target=\"".$this->_current->target."\" >".$this->_current->title."</a>";
		} elseif ($this->_current->link != null && $this->_current->target == null) {
			echo "<a".$linkClass." href=\"".$this->_current->link."\">".$this->_current->title."</a>";
		} elseif ($this->_current->title != null) {
			echo "<a>".$this->_current->title."</a>\n";
		} else {
			echo "<span></span>";
		}

		/*
		 * Recurse through children if they exist
		 */
		while ($this->_current->hasChildren())
		{
			if ($this->_current->class) {
				$id = '';
				if (!empty($this->_current->id)) {
					$id = ' id="menu-'.strtolower($this->_current->id).'"';
				}
				echo '<ul'.$id.' class="menu-component">'."\n";
			} else {
				echo '<ul>'."\n";
			}
			foreach ($this->_current->getChildren() as $child)
			{
				$this->_current = & $child;
				$this->renderLevel($depth++);
			}
			echo "</ul>\n";
		}
		echo "</li>\n";
	}

	/**
	 * Method to get the CSS class name for an icon identifier or create one if
	 * a custom image path is passed as the identifier
	 *
	 * @access	public
	 * @param	string	$identifier	Icon identification string
	 * @return	string	CSS class name
	 * @since	1.5
	 */
	function getIconClass($identifier)
	{
		static $classes;

		// Initialise the known classes array if it does not exist
		if (!is_array($classes)) {
			$classes = array();
		}

		/*
		 * If we don't already know about the class... build it and mark it
		 * known so we don't have to build it again
		 */
		if (!isset($classes[$identifier])) {
			if (substr($identifier, 0, 6) == 'class:') {
				// We were passed a class name
				$class = substr($identifier, 6);
				$classes[$identifier] = "icon-16-$class";
			} else {
				if ($identifier == null) {
					return null;
				}
				// Build the CSS class for the icon
				$class = preg_replace('#\.[^.]*$#', '', basename($identifier));
				$class = preg_replace('#\.\.[^A-Za-z0-9\.\_\- ]#', '', $class);

				$this->_css  .= "\n#jsn-adminbar #menu a.icon-16-$class {\n" .
						"\tbackground-image: url($identifier) !important;\n" .
						"}\n";

				$classes[$identifier] = "icon-16-$class";
			}
		}
		return $classes[$identifier];
	}
}

class JAdministratorMenuNode extends JNode
{
	/**
	 * Node Title
	 */
	public $title = null;

	/**
	 * Node Id
	 */
	public $id = null;

	/**
	 * Node Link
	 */
	public $link = null;

	/**
	 * Link Target
	 */
	public $target = null;

	/**
	 * CSS Class for node
	 */
	public $class = null;

	/**
	 * Active Node?
	 */
	public $active = false;

	public function __construct($title, $link = null, $class = null, $active = false, $target = null, $titleicon = null)
	{
		$this->title	= $titleicon ? $title.$titleicon : $title;
		$this->link		= JFilterOutput::ampReplace($link);
		$this->class	= $class;
		$this->active	= $active;

		$this->id = null;
		if (!empty($link) && $link !== '#') {
			$uri = new JURI($link);
			$params = $uri->getQuery(true);
			$parts = array();

			foreach ($params as $name => $value) {
				$parts[] = str_replace(array('.', '_'), '-', $value);
 			}

 			$this->id = implode('-', $parts);
		}

		$this->target	= $target;
	}
}

class JSNPowerAdminMenuHelper 
{
	public static function renderMenus ()
	{
		$menu = new JSNAdministratorMenu();

		$shownew = true;
		$showhelp = true;
		$user = JFactory::getUser();
		$lang = JFactory::getLanguage();

		//
		// Site SubMenu
		//
		$menu->addChild(new JAdministratorMenuNode(JText::_('JSITE'), '#'), true);
		$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_CONTROL_PANEL'), 'index.php', 'class:cpanel'));
		$menu->addSeparator();
		$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_USER_PROFILE'), 'index.php?option=com_admin&task=profile.edit&id='.$user->id, 'class:profile'));
		$menu->addSeparator();

		if ($user->authorise('core.admin')) {
			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_CONFIGURATION'), 'index.php?option=com_config', 'class:config'));
			$menu->addSeparator();
		}

		$chm = $user->authorise('core.admin', 'com_checkin');
		$cam = $user->authorise('core.manage', 'com_cache');

		if ($chm || $cam ) {
			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_MAINTENANCE'), 'index.php?option=com_checkin', 'class:maintenance'), true);

			if ($chm) {
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_GLOBAL_CHECKIN'), 'index.php?option=com_checkin', 'class:checkin'));
				$menu->addSeparator();
			}

			if ($cam) {
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_CLEAR_CACHE'), 'index.php?option=com_cache', 'class:clear'));
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_PURGE_EXPIRED_CACHE'), 'index.php?option=com_cache&view=purge', 'class:purge'));
			}

			$menu->getParent();
		}

		$menu->addSeparator();
		if ($user->authorise('core.admin')) {
			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_SYSTEM_INFORMATION'), 'index.php?option=com_admin&view=sysinfo', 'class:info'));
			$menu->addSeparator();
		}

		$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_LOGOUT'), JRoute::_('index.php?option=com_login&task=logout&'. JSession::getFormToken() .'=1'), 'class:logout'));
		$menu->getParent();

		//
		// Users Submenu
		//
		if ($user->authorise('core.manage', 'com_users')) {
			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_USERS_USERS'), '#'), true);
			$createUser = $shownew && $user->authorise('core.create', 'com_users');
			$createGrp = $user->authorise('core.admin', 'com_users');
			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_USERS_USER_MANAGER'), 'index.php?option=com_users&view=users', 'class:user'), $createUser);

			if ($createUser) {
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_USERS_ADD_USER'), 'index.php?option=com_users&task=user.add', 'class:newarticle'));
				$menu->getParent();
			}

			if ($createGrp) {
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_USERS_GROUPS'), 'index.php?option=com_users&view=groups', 'class:groups'), $createUser);
				if ($createUser) {
					$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_USERS_ADD_GROUP'), 'index.php?option=com_users&task=group.add', 'class:newarticle'));
					$menu->getParent();
				}

				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_USERS_LEVELS'), 'index.php?option=com_users&view=levels', 'class:levels'), $createUser);
				if ($createUser) {
					$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_USERS_ADD_LEVEL'), 'index.php?option=com_users&task=level.add', 'class:newarticle'));
					$menu->getParent();
				}
			}

			$menu->addSeparator();
			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_USERS_NOTES'), 'index.php?option=com_users&view=notes', 'class:user-note'), $createUser);

			if ($createUser) {
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_USERS_ADD_NOTE'), 'index.php?option=com_users&task=note.add', 'class:newarticle'));
				$menu->getParent();
			}

			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_USERS_NOTE_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_users.notes', 'class:category'), $createUser);
			if ($createUser) {
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_CONTENT_NEW_CATEGORY'), 'index.php?option=com_categories&task=category.add&extension=com_users', 'class:newarticle'));
				$menu->getParent();
			}

			$menu->addSeparator();
			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_MASS_MAIL_USERS'), 'index.php?option=com_users&view=mail', 'class:massmail'));

			$menu->getParent();
		}

		//
		// Menus Submenu
		//
		if ($user->authorise('core.manage', 'com_menus'))
		{
			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_MENUS'), '#'), true);
			$createMenu = $shownew && $user->authorise('core.create', 'com_menus');
			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_MENU_MANAGER'), 'index.php?option=com_menus&view=menus', 'class:menumgr'), $createMenu);

			if ($createMenu) {
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_MENU_MANAGER_NEW_MENU'), 'index.php?option=com_menus&view=menu&layout=edit', 'class:newarticle'));
				$menu->getParent();
			}

			$menu->addSeparator();

			// Menu Types
			foreach (JSNPowerAdminMenuHelper::getMenus() as $menuType)
			{
				$alt = '*' .$menuType->sef. '*';
				if ($menuType->home == 0) {
					$titleicon = '';
				}
				elseif ($menuType->home == 1 && $menuType->language == '*') {
					$titleicon = ' <span><img src="'.JUri::root(true).'/plugins/system/jsnpoweradmin/assets/images/menu/icon-16-default.png" alt="*" title="'.JText::_('MOD_MENU_HOME_DEFAULT').'" /></span>';
				}
				elseif ($menuType->home > 1) {
					$titleicon = ' <span><img src="'.JUri::root(true).'/plugins/system/jsnpoweradmin/assets/images/menu/icon-16-language.png" alt="*" title="'.JText::_('MOD_MENU_HOME_MULTIPLE').'" /></span>';
				}
				else {
					$image = JHtml::_('image', 'mod_languages/'.$menuType->image.'.gif', NULL, NULL, true, true);
					if (!$image) {
						$titleicon = ' <span>'.JHtml::_('image', 'menu/icon-16-language.png', $alt, array('title' => $menuType->title_native), true).'</span>';
					}
					else {
						$titleicon = ' <span>'.JHtml::_('image', 'mod_languages/'.$menuType->image.'.gif', $alt, array('title'=>$menuType->title_native), true).'</span>';
					}
				}

				$menu->addChild(new JAdministratorMenuNode($menuType->title,	'index.php?option=com_menus&view=items&menutype='.$menuType->menutype, 'class:menu', null, null, $titleicon), $createMenu);
				if ($createMenu) {
					$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_MENU_MANAGER_NEW_MENU_ITEM'), 'index.php?option=com_menus&view=item&layout=edit&menutype='.$menuType->menutype, 'class:newarticle'));
					$menu->getParent();
				}
			}

			$menu->getParent();
		}

		//
		// Content Submenu
		//
		if ($user->authorise('core.manage', 'com_content')) {
			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_CONTENT'), '#'), true);
			$createContent =  $shownew && $user->authorise('core.create', 'com_content');
			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_CONTENT_ARTICLE_MANAGER'), 'index.php?option=com_content', 'class:article'), $createContent);

			if ($createContent) {
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_CONTENT_NEW_ARTICLE'), 'index.php?option=com_content&task=article.add', 'class:newarticle'));
				$menu->getParent();
			}

			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_CONTENT_CATEGORY_MANAGER'), 'index.php?option=com_categories&extension=com_content', 'class:category'), $createContent);

			if ($createContent) {
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_CONTENT_NEW_CATEGORY'), 'index.php?option=com_categories&task=category.add&extension=com_content', 'class:newarticle'));
				$menu->getParent();
			}

			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COM_CONTENT_FEATURED'), 'index.php?option=com_content&view=featured', 'class:featured'));
			$menu->addSeparator();

			if ($user->authorise('core.manage', 'com_media')) {
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_MEDIA_MANAGER'), 'index.php?option=com_media', 'class:media'));
			}

			$menu->getParent();
		}

		//
		// Components Submenu
		//

		// Get the authorised components and sub-menus.
		$components = JSNPowerAdminMenuHelper::getComponents(true);

		// Check if there are any components, otherwise, don't render the menu
		if ($components)
		{
			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_COMPONENTS'), '#'), true);

			foreach ($components as &$component)
			{
				if (!empty($component->submenu)) {
					// This component has a db driven submenu.
					$menu->addChild(new JAdministratorMenuNode($component->text, $component->link, $component->img), true);
					foreach ($component->submenu as $sub) {
						$menu->addChild(new JAdministratorMenuNode($sub->text, $sub->link, $sub->img));
					}

					$menu->getParent();
				}
				else {
					$menu->addChild(new JAdministratorMenuNode($component->text, $component->link, $component->img));
				}
			}

			$menu->getParent();
		}

		//
		// Extensions Submenu
		//
		$im = $user->authorise('core.manage', 'com_installer');
		$mm = $user->authorise('core.manage', 'com_modules');
		$pm = $user->authorise('core.manage', 'com_plugins');
		$tm = $user->authorise('core.manage', 'com_templates');
		$lm = $user->authorise('core.manage', 'com_languages');

		if ($im || $mm || $pm || $tm || $lm)
		{
			$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_EXTENSIONS_EXTENSIONS'), '#'), true);

			if ($im)
			{
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_EXTENSIONS_EXTENSION_MANAGER'), 'index.php?option=com_installer', 'class:install'));
				$menu->addSeparator();
			}

			if ($mm)
			{
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_EXTENSIONS_MODULE_MANAGER'), 'index.php?option=com_modules', 'class:module'));
			}

			if ($pm)
			{
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_EXTENSIONS_PLUGIN_MANAGER'), 'index.php?option=com_plugins', 'class:plugin'));
			}

			if ($tm)
			{
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_EXTENSIONS_TEMPLATE_MANAGER'), 'index.php?option=com_templates', 'class:themes'));
			}

			if ($lm)
			{
				$menu->addChild(new JAdministratorMenuNode(JText::_('MOD_MENU_EXTENSIONS_LANGUAGE_MANAGER'), 'index.php?option=com_languages', 'class:language'));
			}
			$menu->getParent();
		}

		//
		// Help Submenu
		//
		if ($showhelp == 1)
		{
			$menu->addChild(
				new JAdministratorMenuNode(JText::_('MOD_MENU_HELP'), '#'), true
			);
			$menu->addChild(
				new JAdministratorMenuNode(JText::_('MOD_MENU_HELP_JOOMLA'), 'index.php?option=com_admin&view=help', 'class:help')
			);
			$menu->addSeparator();

			$menu->addChild(
				new JAdministratorMenuNode(JText::_('MOD_MENU_HELP_SUPPORT_OFFICIAL_FORUM'), 'http://forum.joomla.org', 'class:help-forum', false, '_blank')
			);

			$debug = $lang->setDebug(false);
			if ($lang->hasKey('MOD_MENU_HELP_SUPPORT_OFFICIAL_LANGUAGE_FORUM_VALUE') && JText::_('MOD_MENU_HELP_SUPPORT_OFFICIAL_LANGUAGE_FORUM_VALUE') != '')
			{
				$forum_url = 'http://forum.joomla.org/viewforum.php?f=' . (int) JText::_('MOD_MENU_HELP_SUPPORT_OFFICIAL_LANGUAGE_FORUM_VALUE');
				$lang->setDebug($debug);
				$menu->addChild(
					new JAdministratorMenuNode(JText::_('MOD_MENU_HELP_SUPPORT_OFFICIAL_LANGUAGE_FORUM'), $forum_url, 'class:help-forum', false, '_blank')
				);
			}
			$lang->setDebug($debug);
			$menu->addChild(
				new JAdministratorMenuNode(JText::_('MOD_MENU_HELP_DOCUMENTATION'), 'http://docs.joomla.org', 'class:help-docs', false, '_blank')
			);
			$menu->addSeparator();
			$menu->addChild(
				new JAdministratorMenuNode(JText::_('MOD_MENU_HELP_LINKS'), '#', 'class:weblinks'), true
			);
			$menu->addChild(
				new JAdministratorMenuNode(JText::_('MOD_MENU_HELP_EXTENSIONS'), 'http://extensions.joomla.org', 'class:help-jed', false, '_blank')
			);
			$menu->addChild(
				new JAdministratorMenuNode(JText::_('MOD_MENU_HELP_TRANSLATIONS'), 'http://community.joomla.org/translations.html', 'class:help-trans', false, '_blank')
			);
			$menu->addChild(
				new JAdministratorMenuNode(JText::_('MOD_MENU_HELP_RESOURCES'), 'http://resources.joomla.org', 'class:help-jrd', false, '_blank')
			);
			$menu->addChild(
				new JAdministratorMenuNode(JText::_('MOD_MENU_HELP_COMMUNITY'), 'http://community.joomla.org', 'class:help-community', false, '_blank')
			);
			$menu->addChild(
				new JAdministratorMenuNode(JText::_('MOD_MENU_HELP_SECURITY'), 'http://developer.joomla.org/security.html', 'class:help-security', false, '_blank')
			);
			$menu->addChild(
				new JAdministratorMenuNode(JText::_('MOD_MENU_HELP_DEVELOPER'), 'http://developer.joomla.org', 'class:help-dev', false, '_blank')
			);
			$menu->addChild(
				new JAdministratorMenuNode(JText::_('MOD_MENU_HELP_SHOP'), 'http://shop.joomla.org', 'class:help-shop', false, '_blank')
			);
			$menu->getParent();
			$menu->getParent();
		}

		ob_start();
		$menu->renderMenu();
		$content = ob_get_clean();

		return $content;
	}

	/**
	 * Get a list of the available menus.
	 *
	 * @return	array	An array of the available menus (from the menu types table).
	 * @since	1.6
	 */
	public static function getMenus()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.*, SUM(b.home) AS home');
		$query->from('#__menu_types AS a');
		$query->leftJoin('#__menu AS b ON b.menutype = a.menutype AND b.home != 0');
		$query->select('b.language');
		$query->leftJoin('#__languages AS l ON l.lang_code = language');
		$query->select('l.image');
		$query->select('l.sef');
		$query->select('l.title_native');
		$query->where('(b.client_id = 0 OR b.client_id IS NULL)');
		//sqlsrv change
		$query->group('a.id, a.menutype, a.description, a.title, b.menutype,b.language,l.image,l.sef,l.title_native');

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Get a list of the authorised, non-special components to display in the components menu.
	 *
	 * @param	boolean	$authCheck	An optional switch to turn off the auth check (to support custom layouts 'grey out' behaviour).
	 *
	 * @return	array	A nest array of component objects and submenus
	 * @since	1.6
	 */
	public static function getComponents($authCheck = true)
	{
		// Initialise variables.
		$lang	= JFactory::getLanguage();
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$result	= array();
		$langs	= array();

		// Prepare the query.
		$query->select('m.id, m.title, m.alias, m.link, m.parent_id, m.img, e.element');
		$query->from('#__menu AS m');

		// Filter on the enabled states.
		$query->leftJoin('#__extensions AS e ON m.component_id = e.extension_id');
		$query->where('m.client_id = 1');
		$query->where('e.enabled = 1');
		$query->where('m.id > 1');

		// Order by lft.
		$query->order('m.lft');

		$db->setQuery($query);
		// component list
		$components	= $db->loadObjectList();

		// Parse the list of extensions.
		foreach ($components as &$component) {
			// Trim the menu link.
			$component->link = trim($component->link);

			if ($component->parent_id == 1) {
				// Only add this top level if it is authorised and enabled.
				if ($authCheck == false || ($authCheck && $user->authorise('core.manage', $component->element))) {
					// Root level.
					$result[$component->id] = $component;
					if (!isset($result[$component->id]->submenu)) {
						$result[$component->id]->submenu = array();
					}

					// If the root menu link is empty, add it in.
					if (empty($component->link)) {
						$component->link = 'index.php?option='.$component->element;
					}

					if (!empty($component->element)) {
						// Load the core file then
						// Load extension-local file.
						$lang->load($component->element.'.sys', JPATH_BASE, null, false, false)
					||	$lang->load($component->element.'.sys', JPATH_ADMINISTRATOR.'/components/'.$component->element, null, false, false)
					||	$lang->load($component->element.'.sys', JPATH_BASE, $lang->getDefault(), false, false)
					||	$lang->load($component->element.'.sys', JPATH_ADMINISTRATOR.'/components/'.$component->element, $lang->getDefault(), false, false);
					}
					$component->text = $lang->hasKey($component->title) ? JText::_($component->title) : $component->alias;
				}
			} else {
				// Sub-menu level.
				if (isset($result[$component->parent_id])) {
					// Add the submenu link if it is defined.
					if (isset($result[$component->parent_id]->submenu) && !empty($component->link)) {
						$component->text = $lang->hasKey($component->title) ? JText::_($component->title) : $component->alias;
						$result[$component->parent_id]->submenu[] = &$component;
					}
				}
			}
		}

		$result = JArrayHelper::sortObjects($result, 'text', 1, true, $lang->getLocale());

		return $result;
	}
}
