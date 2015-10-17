<?php
/**
 * @version     $Id: uniform.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Plugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

/**
 * Uniform Content Plugin
 *
 * @package     Joomla.Plugin
 *
 * @subpackage  Content.joomla
 *
 * @since       1.6
 */
class plgContentUniform extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 *
	 * @param   array   $config    An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Content before display
	 *
	 * @param   string  $context   The context of the content being passed to the plugin.
	 * @param   object  &$article  The article object.  Note $article->text is also
	 *
	 * @return	void
	 */
	public function onContentBeforeDisplay($context, &$article)
	{
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer')
		{
			return true;
		}
		// expression to search for (positions)
		$regex = '/{uniform form=+(.*?)\/}/i';
		$style = $this->params->def('style', 'none');
		if (!empty($article->text))
		{
			// Find all instances of plugin and put in $matches for loadposition
			// $matches[0] is full pattern match, $matches[1] is the position
			preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

			// No matches, skip this
			if ($matches)
			{
				foreach ($matches as $index => $match)
				{
					$matcheslist = explode(',', $match[1]);

					// We may not have a module style so fall back to the plugin default.
					if (!array_key_exists(1, $matcheslist))
					{
						$matcheslist[1] = $style;
					}

					$formID = trim($matcheslist[0]);
					$style = trim($matcheslist[1]);

					if (isset($formID))
					{
						$output = $this->loadJSNUniform($formID, $index);
						$article->text = @preg_replace("|$match[0]|", addcslashes($output, '\\$'), $article->text, 1);
					}
					// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				}
			}
		}
	}

	/**
	 * Load Form
	 *
	 * @param   Int  $formID  Form id
	 * @param   Imt  $index   Form Index
	 *
	 * @return void
	 */
	public function loadJSNUniform($formID, $index)
	{
		require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_uniform' . DS . 'uniform.defines.php';
		$formName = md5(date("Y-m-d H:i:s") . $index);
		return JSNUniformHelper::generateHTMLPages($formID, $formName);
	}
}
