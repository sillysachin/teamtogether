<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPL
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
 * Class to recognize whether a template is made by JoomlaShine or not.
 *
 * @package     JSNTPL
 * @subpackage  Recognization
 * @since       2.0.8
 */
class JSNTplTemplateRecognization
{
	/**
	 * Template information.
	 *
	 * @var  array
	 */
	protected static $templateDetails = array();

	/**
	 * Detect if a template is made by JoomlaShine or not.
	 *
	 * @param   string  $template  Template folder name. If leave empty then active template will be detected.
	 *
	 * @return  mixed  An object containing template name, edition and version if JoomlaShine template detected or boolean FALSE otherwise.
	 */
	public static function detect($template = null)
	{
		! empty($template) OR $template = JFactory::getApplication()->getTemplate();

		if ( ! isset(self::$templateDetails[$template]))
		{
			// Unset our unique variable
			unset($JoomlaShine_Template_Name);

			if (is_file(JPATH_ROOT . "/templates/{$template}/template.defines.php"))
			{
				// Load template definition
				include_once JPATH_ROOT . "/templates/{$template}/template.defines.php";
			}

			// Check if our unique variable is defined
			if ( ! isset($JoomlaShine_Template_Name))
			{
				// Parse templateDetails.xml file for necessary information
				if ($xml = @simplexml_load_file(JPATH_ROOT . "/templates/{$template}/templateDetails.xml"))
				{
					if (preg_match('/^jsn_([^_]+)(_free|_pro)?$/', (string) $xml->name, $match))
					{
						self::mark($template, $match[1], isset($xml->edition) ? (string) $xml->edition : $match[2], (string) $xml->version);
					}
					elseif (isset($xml->group) AND (string) $xml->group == 'jsntemplate' AND isset($xml->identifiedName))
					{
						$name = str_replace('tpl_', '', (string) $xml->identifiedName);

						self::mark($template, $name, isset($xml->edition) ? (string) $xml->edition : 'free', (string) $xml->version);
					}
				}
			}
			else
			{
				// Store necessary template information
				self::$templateDetails[$template] = (object) array(
					'name' => $JoomlaShine_Template_Name,
					'edition' => strcasecmp($JoomlaShine_Template_Name, 'Boot') == 0 ? 'PRO STANDARD' : $JoomlaShine_Template_Edition,
					'version' => $JoomlaShine_Template_Version
				);
			}
		}

		return isset(self::$templateDetails[$template]) ? self::$templateDetails[$template] : false;
	}

	/**
	 * Mark a template with necessary information to recognize it later.
	 *
	 * @param   string  $template  Template folder name.
	 * @param   string  $name      Template name.
	 * @param   string  $edition   Template edition.
	 * @param   string  $version   Template version.
	 *
	 * @return  void
	 */
	protected static function mark($template, $name, $edition, $version)
	{
		$name = ucfirst($name);
		$edition = strtoupper(trim($edition, '_'));

		// Generate template information
		$content = "<?php
/**
 * @author    JoomlaShine.com http://www.joomlashine.com
 * @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted index access');

// Define template name
\$JoomlaShine_Template_Name = '{$name}';

// Define template edition
\$JoomlaShine_Template_Edition = '{$edition}';

// Define template version
\$JoomlaShine_Template_Version = '{$version}';
";

		// Write to file
		JFile::write(JPATH_ROOT . "/templates/{$template}/template.defines.php", $content);

		// Store necessary template information
		self::$templateDetails[$template] = (object) array(
			'name' => $name,
			'edition' => $edition,
			'version' => $version
		);
	}
}
