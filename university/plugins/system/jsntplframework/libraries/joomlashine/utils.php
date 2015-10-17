<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import necessary Joomla libraries
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Utilities class
 *
 * @package     JSNTPLFramework
 * @subpackage  Template
 * @since       1.0.0
 */
class JSNTplUtils
{
	/**
	 * Return class instance
	 *
	 */
	public static function getInstance() {
		static $instance;

		if ($instance == null) {
			$instance = new JSNTplUtils();
		}

		return $instance;
	}
	/**
	 * Get and store template attributes
	 *
	 */
	public function getTemplateAttributes($attrs_array, $template_prefix, $pageclass) {
		$template_attrs = null;
		if(count($attrs_array)) {
			foreach ($attrs_array as $attr_name => $attr_values) {
				$t_attr = null;

				// Get template settings from page class suffix
				if(!empty($pageclass)){
					$pc = 'custom-'.$attr_name.'-';
					$pc_len = strlen($pc);
					$pclasses = explode(" ", $pageclass);
					foreach($pclasses as $pclass){
						if(substr($pclass, 0, $pc_len) == $pc) {
							$t_attr = substr($pclass, $pc_len, strlen($pclass)-$pc_len);
						}
					}
				}
				if( isset( $_GET['jsn_setpreset'] ) && $_GET['jsn_setpreset'] == 'default' ) {
					setcookie($template_prefix.$attr_name, '', time() - 3600, '/');
				} else {
					// Apply template settings from cookies
					if (isset($_COOKIE[$template_prefix.$attr_name])) {
						$t_attr = $_COOKIE[$template_prefix.$attr_name];
					}

					// Apply template settings from permanent request parameters
					if (isset($_GET['jsn_set'.$attr_name])) {
						setcookie($template_prefix.$attr_name, trim($_GET['jsn_set'.$attr_name]), time() + 3600, '/');
						$t_attr = trim($_GET['jsn_set'.$attr_name]);
					}
				}

				// Store template settings
				$template_attrs[$attr_name] = null;
				if(is_array($attr_values)){
					if (in_array($t_attr, $attr_values)) {
						$template_attrs[$attr_name] = $t_attr;
					}
				} else if($attr_values == 'integer'){
					$template_attrs[$attr_name] = intval($t_attr);
				}
			}
		}

		return $template_attrs;
	}

	public function getTemplateDetails()
	{
		require_once 'jsn_readxmlfile.php';
		$jsn_readxml = new JSNReadXMLFile();

		return $jsn_readxml->getTemplateInfo();
	}

	private $_isJoomla3 = null;
	public function isJoomla3 () {
		if ($this->_isJoomla3 == null) {
			$version = new JVersion();
			$this->_isJoomla3 = version_compare($version->getShortVersion(), '3.0', '>=');
		}

		return $this->_isJoomla3;
	}

	/**
	 * Get template parameters
	 *
	 */
	function getTemplateParameters()
	{
		return JFactory::getApplication()->getTemplate(true)->params;
	}
	/**
	 * Get the front-end template name
	 *
	 */
	public function getTemplateName()
	{
		$document = JFactory::getDocument();
		return $document->template;
	}

	/**
	 * Add template attribute to URL, used by Site Tools
	 *
	 */
	public function addAttributeToURL($key, $value) {
		$url = $_SERVER['REQUEST_URI'];
		$url = JFilterOutput::ampReplace($url);
		for($i = 0, $count_key = substr_count($url, 'jsn_set'); $i < $count_key; $i ++) {
			$url = preg_replace('/(.*)(\?|&)jsn_set[a-z]{0,30}=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
			$url = substr($url, 0, -1);
		}
		if (strpos($url, '?') === false) {
			return ($url . '?' . $key . '=' . $value);
		} else {
			return ($url . '&amp;' . $key . '=' . $value);
		}
	}

	/**
	 * Return the number of module instance assigned to show in the specified position.
	 *
	 * @param   string  $position  Position to count module instance for.
	 *
	 * @return  integer
	 */
	public function countModules($position)
	{
		// Get all modules assigned to this position
		$modules = JModuleHelper::getModules($position);
		$numMods = count($modules);

		// Check if user is using mobile device
		$mobile = $this->checkMobile();

		// Count modules
		foreach ($modules as $module)
		{
			$params = (object) (is_string($module->params) ? json_decode($module->params) : $module->params);

			if ( ! isset($params->moduleclass_sfx))
			{
				continue;
			}

			if ($mobile)
			{
				if (strpos($params->moduleclass_sfx, 'display-desktop') !== false)
				{
					$numMods--;
				}
			}
			else
			{
				if (strpos($params->moduleclass_sfx, 'display-mobile') !== false)
				{
					$numMods--;
				}
			}
		}

		return $numMods;
	}

	/**
	 * Return the number of module positions count
	 *
	 * @return  int
	 */
	public function countPositions()
	{
		$numPositions = 0;

		foreach (func_get_args() as $position)
		{
			if ($this->countModules($position))
			{
				$numPositions++;
			}
		}

		return $numPositions;
	}

	/**
	 * Get template positions
	 *
	 */
	public function getPositions($template)
	{
		jimport('joomla.filesystem.folder');
		$result 		= array();
		$client 		= JApplicationHelper::getClientInfo(0);

		if ($client === false)
		{
			return false;
		}

		require_once 'jsn_readxmlfile.php';
		$jsn_readxml = new JSNReadXMLFile();
		$positions = $jsn_readxml->getTemplatePositions();

		$positions = array_unique($positions);
		if(count($positions))
		{
			foreach ($positions as $value)
			{
				$classModule 	= new stdClass();
				$classModule->value = $value;
				$classModule->text = $value;
				if(preg_match("/-m+$/", $value))
				{
					$result['mobile'] [] = $classModule;
				}
				else
				{
					$result['desktop'] [] = $classModule;
				}
			}
		}
		return $result;
	}
	/**
	 * render positions ComboBox
	 *
	 */
	public function renderPositionComboBox($ID, $data, $elementText, $elementName, $parameters = '')
	{
		array_unshift($data, JHTML::_('select.option', 'none', JText::_('NO_MAPPING'), 'value', 'text'));
		return JHTML::_('select.genericlist', $data, $elementName, $parameters, 'value', 'text', $ID);
	}
	/**
	 * Wrap first word inside a <span>
	 *
	 */
	public function wrapFirstWord( $value )
	{
	 	$processed_string =  null;
	 	$explode_string = explode(' ', trim( $value ) );
	 	for ( $i=0; $i < count( $explode_string ); $i++ )
	 	{
	 		if( $i == 0 )
	 		{
	 			$processed_string .= '<span>'.$explode_string[$i].'</span>';
	 		}
	 		else
	 		{
	 			$processed_string .= ' '.$explode_string[$i];
	 		}
	 	}

	 	return $processed_string;
	 }

	/**
	 * Trim precedding slash
	 *
	 */
	public function trimPreceddingSlash($string)
	{
		$string = trim($string);

		if (substr($string, 0, 1) == '\\' || substr($string, 0, 1) == '/') {
			$string = substr($string, 1);
		}

		return $string;
	}
	/**
	 * Trim ending slash
	 *
	 */
	public function trimEndingSlash($string)
	{
		$string = trim($string);

		if (substr($string, -1) == '\\' || substr($string, -1) == '/') {
			$string = substr($string, 0, -1);
		}

		return $string;
	}
	/**
	 * Trim both ending slash
	 *
	 */
	public function trimSlash($string)
	{
		$string = trim($string);

		$string = $this->trimPreceddingSlash($string);
		$string = $this->trimEndingSlash($string);

		return $string;
	}
	/**
	 * Strip extra space
	 *
	 */
	public function StripExtraSpace($s)
	{
		$newstr = "";
		for($i = 0; $i < strlen($s); $i++)
		{
			$newstr = $newstr.substr($s, $i, 1);
			if(substr($s, $i, 1) == ' ')
			while(substr($s, $i + 1, 1) == ' ')
			$i++;
		}
		return $newstr;
	}

	/**
	 * Detect whether user is using a mobile device or desktop computer.
	 *
	 * @return  boolean
	 */
	public function checkMobile()
	{
		$usrAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$mobiles  = '/midp|240x320|blackberry|netfront|nokia|panasonic|portalmmm|sharp|sie-|sonyericsson|symbian|windows ce|benq|mda|mot-|opera mini|philips|pocket pc|sagem|samsung|sda|sgh-|vodafone|xda|palm|iphone|ipod|android|ipad/';

		return preg_match($mobiles, $usrAgent);
	}

	/**
	 * Get mobile device
	 *
	 */
	public function getMobileDevice()
	{
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		$mobileDeviceName = null;
		switch( true )
		{
			case ( preg_match( '/ipod/i', $user_agent ) || preg_match( '/iphone/i', $user_agent ) ):
				$mobileDeviceName = 'iphone';
			break;
			case ( preg_match( '/ipad/i', $user_agent ) ):
				$mobileDeviceName = 'ipad';
			break;
			case ( preg_match( '/android/i', $user_agent ) ):
				$mobileDeviceName = 'android';
			break;
			case ( preg_match( '/opera mini/i', $user_agent ) ):
				$mobileDeviceName = 'opera';
			break;
			case ( preg_match( '/blackberry/i', $user_agent ) ):
				$mobileDeviceName = 'blackberry';
			break;
			case ( preg_match( '/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i', $user_agent ) ):
				$mobileDeviceName = 'palm';
			break;
			case ( preg_match( '/(windows ce; ppc;|windows mobile;|windows ce; smartphone;|windows ce; iemobile|windows phone)/i', $user_agent ) ):
				$mobileDeviceName = 'windows';
			break;
		}
		return $mobileDeviceName;
	}

	/**
	 * Check folder is writable or not.
	 */
	public function checkFolderWritable($path)
	{
		$config = JFactory::getConfig();

		if ( ! $config->get('ftp_enable') AND ! is_writable($path))
		{
			return false;
		}

		return true;
	}

	/**
	 * Clean up cache folder.
	 */
	public function cleanupCacheFolder($template_name = '', $css_js_compression = 0, $cache_folder_path)
	{
		$cache_folder_path = str_replace('/', DIRECTORY_SEPARATOR, $cache_folder_path);

		if ($css_js_compression !=  1 && $css_js_compression != 2)
		{
			if ($handle = opendir($cache_folder_path))
			{
				while (false !== ($file = readdir($handle)))
				{
					$pattern = '/^' . $template_name . '_css/';

					if (preg_match($pattern, $file) > 0)
					{
						JFile::delete($cache_folder_path . '/' . $file);
					}
				}
			}
		}

		if ($css_js_compression !=  1 && $css_js_compression != 3)
		{
			if ($handle = opendir($cache_folder_path))
			{
				while (false !== ($file = readdir($handle)))
				{
					$pattern = '/^' . $template_name . '_js/';

					if (preg_match($pattern, $file) > 0)
					{
						JFile::delete($cache_folder_path . '/' . $file);
					}
				}
			}
		}
	}

	public function getAllFileInHeadSection(&$header_stuff, $type, &$ref_data)
	{
		$uri = JURI::base(true);

		if ($type == 'css')
		{
			$datas 	=& $header_stuff['styleSheets'];
			$file_extensions = '.css';
		}

		if ($type == 'js')
		{
			$datas =& $header_stuff['scripts'];
			$file_extensions = '.js';
		}

		foreach ($datas as $key => $script)
		{
			$cleaned_url = $this->clarifyUrl($key);
			if ($cleaned_url)
			{
				if (preg_match('#\.'.$type.'$#', $cleaned_url))
				{
					$file_name 		= basename($cleaned_url);
					$file_rel_path  = dirname($cleaned_url);
					$file_abs_path	= JPATH_ROOT. DIRECTORY_SEPARATOR .str_replace("/", DIRECTORY_SEPARATOR, $file_rel_path);
					$ref_data[$uri.'/'.$file_rel_path.'/'.$file_name]['file_abs_path'] 	= $file_abs_path;
					$ref_data[$uri.'/'.$file_rel_path.'/'.$file_name]['file_name']		= $file_name;
					// Remove them from HEAD
					unset($datas[$key]);
				}
			}
		}
	}

	function arrangeFileInHeadSection(&$header_stuff, $topScripts, $compressedFiles = array())
	{
		$data =& $header_stuff['scripts'];

		if (count($data))
		{
			/**
			 * Remove compressed scripts in Header Data if they are still available (inserted by others)
			 * However, exclude "jsn_noconflict.js" file as it might needed for external jQuery lib (Google API)
			 */
			foreach ($compressedFiles as $file => $fileDetails)
			{
				if (array_key_exists($file, $data) && strpos($file, 'jsn_noconflict.js') === false)
				{
					unset($data[$file]);
				}
			}

			/* re-arrange file to ensure most "important" scripts are loaded first */
			$loadFirst = array();
			foreach ($topScripts as $script)
			{
				if (array_key_exists($script, $data))
				{
					$loadFirst[$script] = $data[$script];
					unset($data[$script]);
				}
			}

			$data = $loadFirst + $data;
		}
	}

	/**
	 * Check item menu is the last menu
	 *
	 */
	public function isLastMenu($item)
	{
		if(isset($item->tree[0]) && isset($item->tree[1])) {
			$db	= JFactory::getDbo();
			$q	= $db->getQuery(true);

			$q->select('lft, rgt');
			$q->from('#__menu');
			$q->where('id = ' . (int) $item->tree[0], 'OR');
			$q->where('id = ' . (int) $item->tree[1]);

			$db->setQuery($q);

			$results = $db->loadObjectList();

		 	if ($results[1]->rgt == ((int) $results[0]->rgt - 1) && $item->deeper)
		 	{
		 		return true;
		 	}
		 	else
		 	{
		 		return false;
		 	}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get browser specific information
	 *
	 */
	public function getBrowserInfo($agent = null)
	{
		$browser = array("browser"=>'', "version"=>'');
		$known = array("firefox", "msie", "opera", "chrome", "safari",
					"mozilla", "seamonkey", "konqueror", "netscape",
					"gecko", "navigator", "mosaic", "lynx", "amaya",
					"omniweb", "avant", "camino", "flock", "aol");
		$agent = strtolower($agent ? $agent : $_SERVER['HTTP_USER_AGENT']);
		foreach($known as $value)
		{
			if (preg_match("#($value)[/ ]?([0-9.]*)#", $agent, $matches))
			{
				$browser['browser'] = $matches[1];
				$browser['version'] = $matches[2];
				break;
			}
		}
		return $browser;
	}
	/**
	 * Get current URL
	 *
	 */
	public function getCurrentUrl() {
		return JURI::getInstance()->toString();
	}
	/**
	 * check System Cache - Plugin
	 *
	 */
	public function checkSystemCache() {
		$db	= JFactory::getDbo();
		$q	= $db->getQuery(true);

		$q->select('enabled');
		$q->from('#__extensions');
		$q->where('name = ' . $q->quote('plg_system_cache'));

		$db->setQuery($q);

		return (bool) $db->loadResult();
	}

	/**
	 * Check if current page is rendered by a specific component and/or has any module of that specific component assigned to.
	 *
	 * @param   string  $option  Component folder name, e.g. com_k2
	 * @param   string  $module  Prefix of module belonging to the specified component above, e.g. mod_k2_
	 *
	 * @return  boolean
	 */
	protected function checkExt($option, $module = '')
	{
		// Get input object
		$input = JFactory::getApplication()->input;

		// Check if current page is generated by specified component
		if ($input->getCmd('option') == $option)
		{
			return true;
		}

		if ( ! empty($module))
		{
			// Get current page menu item id
			$itemId = $input->getInt('Itemid', 0);

			// Get template positions
			static $positions;

			if ( ! isset($positions))
			{
				// Read template manifest file for available positions
				$xml = simplexml_load_file(JPATH_ROOT . '/templates/' . JFactory::getApplication()->getTemplate(true)->template . '/templateDetails.xml');

				foreach ($xml->xpath('positions/position') AS $position)
				{
					$positions[] = (string) $position;
				}
			}

			// Get Joomla database object
			$db	= JFactory::getDbo();

			// First query for module instances that are always hidden in current page
			$q	= $db->getQuery(true);

			$q->select('m.id');
			$q->from('#__modules AS m');
			$q->join('INNER', '#__modules_menu AS mm ON mm.moduleid = m.id');
			$q->where('m.client_id = 0');
			$q->where('m.published = 1');
			$q->where('m.module LIKE ' . $q->quote("{$module}%"));
			$q->where('(mm.menuid < 0 AND mm.menuid = ' . (0 - $itemId) . ')');

			if (isset($positions))
			{
				$q->where('m.position IN ("' . implode('", "', $positions) . '")');
			}

			$db->setQuery($q);

			$excludes = is_array($excludes = $db->loadColumn()) ? $excludes : array();

			// Then query for modules instances that are assigned to show in all page or current page
			$q	= $db->getQuery(true);

			$q->select('m.id');
			$q->from('#__modules AS m');
			$q->join('INNER', '#__modules_menu AS mm ON mm.moduleid = m.id');
			$q->where('m.client_id = 0');
			$q->where('m.published = 1');
			$q->where('m.module LIKE ' . $q->quote("{$module}%"));
			$q->where('(mm.menuid = ' . $itemId . ' OR mm.menuid = 0 OR (mm.menuid < 0 AND mm.menuid != ' . (0 - $itemId) . '))');
			$q->group('m.id');

			if (isset($positions))
			{
				$q->where('m.position IN ("' . implode('", "', $positions) . '")');
			}

			$db->setQuery($q);

			if (is_array($includes = $db->loadColumn()))
			{

				// Compare include and exclude arrays
				$includes = array_diff($includes, $excludes);

				return count($includes);
			}
		}

		return false;
	}

	/**
	 * Check if current page is rendered by K2 component and/or has any K2 module assigned to.
	 *
	 * @return  boolean
	 */
	public function checkK2()
	{
		return $this->checkExt('com_k2', 'mod_k2_');
	}

	/**
	 * Check if current page is rendered by VirtueMart component and/or has any VirtueMart module assigned to.
	 *
	 */
	public function checkVM()
	{
		return $this->checkExt('com_virtuemart', 'mod_virtuemart_');
	}

	/**
	 * Check if current page is rendered by Jomres component and/or has any Jomres module assigned to.
	 *
	 */
	public function checkJomres()
	{
		return ($this->checkExt('com_jomres', 'mod_jomres_') OR $this->checkExt('com_jomres', 'mod_jomsearch_'));
	}

	/**
	 * Check if current page is rendered by Ohanah component and/or has any Ohanah module assigned to.
	 *
	 */
	public function checkOhanah()
	{
		return $this->checkExt('com_ohanah', 'mod_ohanah');
	}

	/**
	 * Check if current page is rendered by Hikashop component and/or has any Hikashop module assigned to.
	 *
	 */
	public function checkHikashop()
	{
		return $this->checkExt('com_hikashop', 'mod_hikashop');
	}

	/**
	 * Check if current page is rendered by Redshop component and/or has any Redshop module assigned to.
	 *
	 */
	public function checkRedshop()
	{
		return $this->checkExt('com_redshop', 'mod_redshop');
	}

	/**
	 * Check if current page is rendered by Easysocial component and/or has any Easysocial module assigned to.
	 *
	 */
	public function checkEasysocial()
	{
		return $this->checkExt('com_easysocial', 'mod_easysocial');
	}
	/**
	 * Check if current page is rendered by Mijoshop component and/or has any Mijoshop module assigned to.
	 *
	 */
	public function checkMijoshop()
	{
		return $this->checkExt('com_mijoshop', 'mod_mijoshop');
	}
	/**
	 * Check if current page is rendered by DJ Extension component and/or has any DJ Extension module assigned to.
	 *
	 */
	public function checkDJ()
	{
		return $this->checkExt('com_dj', 'mod_dj');
	}
	/**
	 * Check if current page is rendered by Advanced Portfolio Pro component and/or has any DJ Extension module assigned to.
	 *
	 */
	public function checkAdvportfoliopro()
	{
		return $this->checkExt('com_advportfoliopro', 'mod_advportfoliopro');
	}
	/**
	 * Check if current page is rendered by Advanced Portfolio Pro component and/or has any DJ Extension module assigned to.
	 *
	 */
	public function checkJ2Store()
	{
		return ($this->checkExt('com_content', 'mod_j2store') OR $this->checkExt('com_j2store', 'mod_jomsearch_'));
	}

	public function getJoomlaVersion($glue = false)
	{
		$objVersion = new JVersion();
		$version	= (float) $objVersion->RELEASE;

		if ($version <= 1.5)
		{
			return ($glue)?'1.5':'15';
		}
		elseif ($version >= 1.6 && $version <= 1.7)
		{
			return ($glue)?'2.5':'25';
		}
		else
		{
			return ($glue)?$objVersion->RELEASE:str_replace('.', '', $objVersion->RELEASE);
		}
	}

	public function cURLCheckFunctions()
	{
	  if(!function_exists("curl_init") && !function_exists("curl_setopt") && !function_exists("curl_exec") && !function_exists("curl_close")) return false;
	  return true;
	}

	public function fOPENCheck()
	{
		return (boolean) ini_get('allow_url_fopen');
	}

	public function fsocketopenCheck()
	{
		if (!function_exists('fsockopen')) return false;
		return true;
	}

	public function compareVersion($version1 , $version2)
	{
		//-1: if the first version < the second
		//0: if they are equal
		//1: if the first version > the second
		return version_compare($version1, $version2);
	}

	public function getTemplateManifestCache()
	{
		$template_defacto_name = $this->getTemplateName();

		$db	= JFactory::getDbo();
		$q	= $db->getQuery(true);

		$q->select('manifest_cache');
		$q->from('#__extensions');
		$q->where('type = ' . $q->quote('template'));
		$q->where('element = ' . $q->quote($template_defacto_name));

		$db->setQuery($q);

		return $db->loadResult();
	}

	function clarifyUrl($url)
	{
		$url = preg_replace('/[?\#]+.*$/', '', $url);

		if (preg_match('/^https?\:/', $url))
		{
			if (!preg_match('#^'.preg_quote(JURI::root()).'#', $url))
			{
				return false;
			}
			$url = str_replace(JURI::root(), '', $url);
		}

		if (preg_match('/^\/\//', $url))
		{
			$JUriInstance = JURI::getInstance();
			if (!strstr($url, $JUriInstance->getHost()))
			{
				return false;
			}
		}

		if (preg_match('/^\//', $url) && JURI::root(true))
		{
			if (!preg_match('#^'.preg_quote(JURI::root(true)).'#', $url))
			{
				return false;
			}
			$url = preg_replace('#^'.preg_quote(JURI::root(true)).'#', '', $url);
		}

		$url = preg_replace('/^\//', '', preg_replace('#[/\\\\]+#', '/', $url));
		return $url;
	}

	public function checkProEditionExist($templateName, $pro = false)
	{
		if ($pro === true)
		{
			$templateName = str_replace('free', 'pro', $templateName);
		}

		/* First, check the database */
		$db	= JFactory::getDbo();
		$q	= $db->getQuery(true);

		$q->select('COUNT(*)');
		$q->from('#__extensions');
		$q->where('type = ' . $q->quote('template'));
		$q->where('client_id = 0');
		$q->where('element = ' . $q->quote($templateName));

		$db->setQuery($q);

		$proRecord = $db->loadResult();

		if ($proRecord >= 1)
		{
			return true;
		}
		else
		{
			/* Check whether the template folder exists */
			$templateFolderPath = JPATH_ROOT. DIRECTORY_SEPARATOR .'templates'. DIRECTORY_SEPARATOR .$templateName;
			jimport('joomla.filesystem.folder');
			if (JFolder::exists($templateFolderPath))
			{
				return true;
			}
		}

		return false;
	}

	public function getLatestProductVersion($productIdName, $catName = 'template')
	{
		$codeName = 'cat_' . $catName;
		$latestInfo = $this->parseJsonServerInfo($codeName);

		if (count($latestInfo) === 0)
		{
			return false;
		}
		else
		{
			$catTemplateInfo = $latestInfo[$codeName];

			return $catTemplateInfo[$productIdName]->version;
		}
	}

	private function getLatestProductCatInfo($categoryName)
	{
		$httpRequestInstance = new JSNHTTPSocket(
										JSN_CAT_INFO_URL.$categoryName,
										null, null, 'get');

		return $httpRequestInstance->socketDownload();
	}

	/**
	 * This function parses product information returned by JSN server
	 * @param 	string 	$catName 	JSON-encoded string represents product info.
	 * @return 	array 				array of product information
	 */
	private function parseJsonServerInfo($categoryName)
	{
		$result = array();
		$catInfo = $this->getLatestProductCatInfo($categoryName);

		if ($catInfo !== false && $catInfo !== '')
		{
			$data = json_decode($catInfo);
			if (!is_null($data))
			{
				if (isset($data->items))
				{
					$category_codename = trim($data->category_codename);
					foreach ($data->items as $item)
					{
						if (!isset($item->category_codename) || $item->category_codename == '')
						{
							$result[$category_codename][trim($item->identified_name)] = $item;
						}
						else
						{
							$result[$category_codename][trim($item->category_codename)] = $item;
						}
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Detect site with multilang setup to correctly determine the path to front-end
	 * index so there will be no redirection because of not having lang sef in URLs.
	 */
	public function determineFrontendIndex()
	{
		/* Get JSite object for further actions */
		$app = JFactory::getApplication();
		$frontIndexPath = 'index.php';

		if ($app->isSite())
		{
			$languageFilter = $app->getLanguageFilter();

			$router  = $app->getRouter();
			$sefMode = ($router->getMode() == JROUTER_MODE_SEF) ? '1' : '0';
		}
		else
		{
			/* Determine multilang site and add appropriate lang sef to path */
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('enabled')
					->from($db->quoteName('#__extensions'))
					->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
					->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
					->where($db->quoteName('element') . ' = ' . $db->quote('languagefilter'));
			$db->setQuery($query);
			$languageFilter = $db->loadResult();

			$sefMode = JFactory::getConfig()->get('sef', '0');
		}

		if ($languageFilter)
		{
			$langCode    = JLanguageHelper::getLanguages('lang_code');
			$langDefault = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');

			if (isset($langCode[$langDefault]))
			{
				if ($sefMode === '1')
				{
					$frontIndexPath = JFactory::getConfig()->get('sef_rewrite') ? '' : 'index.php/';
					$frontIndexPath .= $langCode[$langDefault]->sef.'/';
				}
				else
				{
					$frontIndexPath = 'index.php?lang='.$langCode[$langDefault]->sef;
				}
			}
		}

		return $frontIndexPath;
	}
}
