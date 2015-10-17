<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  TPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Load framework defines
require_once dirname(__FILE__) . '/jsntplframework.defines.php';

// Load class loader
require_once JSN_PATH_TPLFRAMEWORK . '/libraries/joomlashine/loader.php';

// Import necessary libraries
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Implement joomla events for template framework
 *
 * @package     TPLFramework
 * @subpackage  Plugin
 * @since       1.0.0
 */
class PlgSystemJSNTPLFramework extends JPlugin
{
	/**
	 * Template administrator object.
	 *
	 * @var  JSNTplTemplateAdmin
	 */
	private static $_templateAdmin;

	/**
	 * Template framework parameters object.
	 *
	 * @var  object
	 */
	private static $_tplfwParams;

	/**
	 * Implement onAfterInitialise event
	 *
	 * @return  void
	 */
	public function onAfterInitialise ()
	{
		$app = JFactory::getApplication();

		// Load language
		$this->loadLanguage();

		if ($app->isAdmin())
		{
			// Register extension uninstall process hook
			$app->registerEvent('onExtensionAfterUninstall', 'PlgSystemJSNTPLFramework');

			// Stop system execution if a widget action is dispatched
			if (JSNTplWidget::dispatch() === true)
			{
				exit();
			}

			// Get requested component, view and task
			$this->option	= $app->input->getCmd('option');
			$this->view		= $app->input->getCmd('view');
			$this->task		= $app->input->getCmd('task');

			// Redirect to update page if necessary
			if ($this->option == 'com_installer' AND $this->view == 'update' AND $this->task == 'update.update' AND count($cid = (array) $app->input->getVar('cid', array())))
			{
				// Check if extension to updated is JoomlaShine product
				$db	= JFactory::getDbo();
				$q	= $db->getQuery(true);

				$q->select('e.extension_id, e.type, e.element, e.folder');
				$q->from('#__extensions AS e');
				$q->join('INNER', '#__updates AS u ON e.extension_id = u.extension_id');
				$q->where('u.update_id IN (' . implode(', ', $cid) . ')');

				$db->setQuery($q);

				if ($exts = $db->loadObjectList())
				{
					foreach ($exts AS $ext)
					{
						if (($ext->type == 'template' AND ! JSNTplTemplateRecognization::detect($ext->element)) OR $ext->element != basename(JSN_PATH_TPLFRAMEWORK))
						{
							continue;
						}

						// Get style id
						$q = $db->getQuery(true);

						$q->select('s.id');
						$q->from('#__template_styles AS s');
						$q->join('INNER', '#__extensions AS e ON s.template = e.element');

						if ($ext->type == 'template')
						{
							$q->where('e.extension_id = ' . $ext->extension_id);
						}
						else
						{
							$q->where('e.custom_data = "jsntemplate"', 'OR');
							$q->where('e.manifest_cache LIKE \'%,"group":"jsntemplate"}\'');
						}

						$q->order('s.client_id, s.home DESC, s.id DESC');

						$db->setQuery($q);

						if ($styleId = $db->loadResult())
						{
							return $app->redirect('index.php?option=com_templates&task=style.edit&id=' . $styleId);
						}
					}
				}
			}

			// Store template framework parameters
			self::$_tplfwParams = & $this->params;
		}
	}

	/**
	 * Event handler to re-parse request URI.
	 *
	 * @return  void
	 */
	public function onAfterRoute()
	{
		$app = JFactory::getApplication();

		if ($app->isSite() AND JSNTplTemplateRecognization::detect())
		{
			// Check if 'System - Cache' plugin is enabled
			if (JPluginHelper::isEnabled('system', 'cache'))
			{
				// Get active site-tool values
				$active = $app->getUserState('jsn.template.site.tool.active', array());

				// Get latest site-tool values
				$latest = isset($_COOKIE[$app->getTemplate() . '_params']) ? $_COOKIE[$app->getTemplate() . '_params'] : '';

				if ( ! empty($latest))
				{
					// Prepare cookie values
					if (get_magic_quotes_runtime() || get_magic_quotes_gpc())
					{
						$latest = stripslashes($latest);
					}

					// JSON-decode cookie values
					$latest = json_decode($latest, true);

					if ( ! empty($latest) AND is_array($latest))
					{
						// Check if latest site-tool values differ from active values
						$isChanged = false;

						foreach ($latest AS $key => $value)
						{
							if ( ! isset($active[$key]) OR $active[$key] != $value)
							{
								$isChanged = true;
								break;
							}
						}

						// If any site-tool value is changed, remove the cached 'page' directory
						jimport('joomla.filesystem.folder');

						if ($isChanged)
						{
							// Remove the cached 'page' directory if necessary
							! is_dir(JPATH_ROOT . '/cache/page') OR JFolder::delete(JPATH_ROOT . '/cache/page');

							// Update active site-tool values
							$app->setUserState('jsn.template.site.tool.active', $latest);
						}
					}
				}
			}
		}

		// Make sure our onAfterRender event handler is the last one executed
		$app->registerEvent('onAfterRender', 'jsnTplFrameworkFinalize');
	}

	/**
	 * Save active form context to memory when editing an template
	 *
	 * @param   object  $context  Current context of template form
	 * @param   object  $data     Data of the form
	 * @return  void
	 */
	public function onContentPrepareForm ($context, $data)
	{
		if ($context->getName() == 'com_templates.style' AND ! empty($data))
		{
			$templateName = is_object($data) ? $data->template : $data['template'];

			if (JSNTplTemplateRecognization::detect($templateName))
			{
				$templateManifest	= JSNTplHelper::getManifest($templateName);
				$templateGroup		= isset($templateManifest->group) ? trim((string) $templateManifest->group) : '';

				// Create template admin instance
				if ($templateGroup == 'jsntemplate')
				{
					self::$_templateAdmin = JSNTplTemplateAdmin::getInstance($context);
				}
			}
		}
	}

	/**
	 * Implement onBeforeRender event to register all needed asset files
	 *
	 * @return  void
	 */
	public function onBeforeRender ()
	{
		if (isset(self::$_templateAdmin) AND self::$_templateAdmin instanceOf JSNTplTemplateAdmin)
		{
			self::$_templateAdmin->registerAssets();
		}
	}

	/**
	 * Render template admin UI
	 *
	 * @return  void
	 */
	public static function onAfterRender ()
	{
		// Make sure our event handler is the last one executed
		if ( ! defined('JSN_TPLFW_LAST_EXECUTION'))
		{
			return;
		}

		// Get Joomla application object
		$app = JFactory::getApplication();

		// Detect method to use for getting and setting response body
		if (version_compare(JVERSION, '3.2.0', 'ge'))
		{
			$get = array($app, 'getBody');
			$set = array($app, 'setBody');
		}
		else
		{
			$get = array('JResponse', 'getBody');
			$set = array('JResponse', 'setBody');
		}

		if ($app->isAdmin())
		{
			// Alter body tag
			$html = call_user_func($get);

			if (preg_match('/<body[^>]*>/i', $html, $match) AND strpos($match[0], 'jsn-master tmpl-' . $app->getTemplate()) === false)
			{
				if (strpos($match[0], 'class=') === false)
				{
					$match[1] = substr($match[0], 0, -1) . ' class=" jsn-master tmpl-' . $app->getTemplate() . ' ">';
				}
				else
				{
					$match[1] = str_replace('class="', 'class=" jsn-master tmpl-' . $app->getTemplate() . ' ', $match[0]);
				}

				$html = str_replace($match[0], $match[1], $html);
			}

			call_user_func($set, $html);

			// Initialize template admin
			if (isset(self::$_templateAdmin) AND self::$_templateAdmin instanceOf JSNTplTemplateAdmin)
			{
				self::$_templateAdmin->render();

				// Clean-up Chosen calls if running on Joomla 3.1
				$JVersion = new JVersion;

				if (version_compare($JVersion->getShortVersion(), '3.1', '>='))
				{
					$html = call_user_func($get);

					if (preg_match('#[\r\n][\s\t]+<link rel="stylesheet" href="[^"]*/media/jui/css/chosen\.css" type="text/css" />#', $html, $match))
					{
						$html = str_replace($match[0], '', $html);
					}

					if (preg_match('#[\r\n][\s\t]+<script src="[^"]*/media/jui/js/chosen\.jquery\.min\.js" type="text/javascript"></script>#', $html, $match))
					{
						$html = str_replace($match[0], '', $html);
					}

					if (preg_match('#[\r\n][\s\t]+jQuery\(document\)\.ready\(function \(\)\{[\r\n][\s\t]+jQuery\(\'select\'\)\.chosen\(\{[^\}]+\}\);[\r\n][\s\t]+\}\);#', $html, $match))
					{
						$html = str_replace($match[0], '', $html);
					}

					call_user_func($set, $html);
				}

				// Clean-up HTML5 fall-back script if running on Joomla 3.2
				if (version_compare($JVersion->getShortVersion(), '3.2', '>='))
				{
					$html = call_user_func($get);

					if (preg_match('#[\r\n][\s\t]+<script src="[^"]*/media/system/js/html5fallback(-uncompressed)?\.js" type="text/javascript"></script>#', $html, $match))
					{
						$html = str_replace($match[0], '', $html);
					}

					call_user_func($set, $html);
				}
			}

			// Execute update checker
			self::checkUpdate();
		}
		elseif (JSNTplTemplateRecognization::detect())
		{
			$document = JFactory::getDocument();
			$config   = JFactory::getConfig();
			$html     = call_user_func($get);

			// Optimize script tags position
			self::moveScriptTags($html);

			if (isset($document->helper) && $document->helper instanceOf JSNTplTemplateHelper && $document->compression > 0)
			{
				// Verify cache directory
				if ( ! preg_match('#^(/|\\|[a-z]:)#i', $document->params->get('cacheDirectory')))
				{
					$cachePath = JPATH_ROOT . '/' . rtrim($document->params->get('cacheDirectory'), '\\/');
				}
				else
				{
					$cachePath = rtrim($document->params->get('cacheDirectory'), '\\/');
				}

				if ($config->get('ftp_enable') OR is_writable($cachePath))
				{
					// Start compress CSS
					if ($document->compression == 1 OR $document->compression == 2)
					{
						$html = preg_replace_callback('/(<link([^>]+)rel=["|\']stylesheet["|\']([^>]*)>\s*)+/i', array('JSNTplCompressCss', 'compress'), $html);
					}

					// Start compress JS
					if ($document->compression == 1 OR $document->compression == 3)
					{
						$html = preg_replace_callback('/(<script([^>]+)src=["|\']([^"|\']+)["|\']([^>]*)>\s*<\/script>\s*)+/i', array('JSNTplCompressJs', 'compress'), $html);
					}
				}
			}

			call_user_func($set, $html);
		}
	}

	/**
	 * Implement onExtensionAfterSave event to save template configuration params
	 *
	 * @param   string  $task  Extension executed task
	 * @param   mixed   $data  Data of task after executed
	 *
	 * @return  void
	 */
	public function onExtensionAfterSave ($task, $data)
	{
		$app = JFactory::getApplication();

		if ($task != 'com_templates.style')
		{
			return;
		}

		// Get options for JoomlaShine template
		$options = isset($_POST['jsn']) ? $_POST['jsn'] : array();

		if (@count($options))
		{
			// Auto strip slashes if magic_quote_gpc is on
			if (get_magic_quotes_runtime() OR get_magic_quotes_gpc())
			{
				foreach ($options AS $k => $v)
				{
					if (is_string($v))
					{
						$options[$k] = stripslashes($v);
					}
				}
			}

			// Check if compression parameters have been changed
			if
			(
				$app->getUserState('jsn.template.maxCompressionSize') != $data->params['maxCompressionSize']
				OR
				$app->getUserState('jsn.template.cacheDirectory') != $data->params['cacheDirectory']
			)
			{
				// Import necessary Joomla library
				jimport('joomla.filesystem.folder');

				// Generate path to cache directory
				if ( ! preg_match('#^(/|\\|[a-z]:)#i', $app->getUserState('jsn.template.cacheDirectory')))
				{
					$cacheDirectory = JPATH_ROOT . '/' . rtrim($app->getUserState('jsn.template.cacheDirectory'), '\\/');
				}
				else
				{
					$cacheDirectory = rtrim($app->getUserState('jsn.template.cacheDirectory'), '\\/');
				}

				// Remove entire cache directory
				! is_dir($cacheDirectory . '/' . $data->template) OR JFolder::delete($cacheDirectory . '/' . $data->template);
			}

			// Clean auto-generated font file
			if (file_exists(JPATH_ROOT . "/templates/{$data->template}/css/styles/custom.css.php"))
			{
				if (file_exists(JPATH_ROOT . "/templates/{$data->template}/css/styles/custom.css"))
				{
					JFile::delete(JPATH_ROOT . "/templates/{$data->template}/css/styles/custom.css");
				}
			}

			// Store template style params
			$data->params = json_encode($options);
			$data->store();
		}
	}

	/**
	 * Implement onExtensionAfterUninstall event to remove the template framework.
	 *
	 * @param   object   $parent  Parent installer object.
	 * @param   integer  $eid     Id of the extension that is uninstalled.
	 *
	 * @return  void
	 */
	public static function onExtensionAfterUninstall($parent, $eid)
	{
		// Count installed JoomlaShine templates
		$db	= JFactory::getDbo();
		$q	= $db->getQuery(true);

		$q->select('COUNT(*)');
		$q->from('#__extensions');
		$q->where('type = "template"');
		$q->where('(custom_data = "jsntemplate" OR manifest_cache LIKE \'%,"group":"jsntemplate"}\')');

		$db->setQuery($q);

		// If there is no any JoomlaShine template installed, uninstall the template framework
		if ((int) $db->loadResult() == 0)
		{
			JFactory::getLanguage()->load('com_installer');

			// Find extension id of the template framework
			$q = $db->getQuery(true);

			$q->select('extension_id');
			$q->from('#__extensions');
			$q->where('type = ' . $q->quote('plugin'));
			$q->where('folder = ' . $q->quote('system'));
			$q->where('element = ' . $q->quote(basename(JSN_PATH_TPLFRAMEWORK)));

			$db->setQuery($q);

			// Continue un-installation only if the extension that is uninstalled is not the template framework itself
			if (($pluginId = $db->loadResult()) AND $pluginId != $eid)
			{
				// Un-protect the template framework
				$executeMethod	= method_exists($db, 'query') ? 'query' : 'execute';
				$q				= $db->getQuery(true);

				$q->update('#__extensions');
				$q->set('protected = 0');
				$q->where('extension_id = ' . (int) $pluginId);

				$db->setQuery($q);
				$db->{$executeMethod}();

				// Get Joomla installer object to remove template framework
				$installer = JInstaller::getInstance();

				if ($installer->uninstall('plugin', $pluginId))
				{
					JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_INSTALLER_UNINSTALL_SUCCESS', 'plugin'));
				}
			}
		}
	}

	/**
	 * Method to move all script tags from head section to the end of body section.
	 *
	 * @param   string  &$html  Generated response body.
	 *
	 * @return  void
	 */
	protected static function moveScriptTags(&$html)
	{
		// Get Joomla input object
		$input = JFactory::getApplication()->input;

		// Only continue if requested return format is html
		if ($input->getCmd('format', null) != null AND $input->getCmd('format') != 'html')
		{
			return;
		}

		// Check if script movement is already done by our extension framework
		if (defined('JSN_EXTFW_SCRIPTS_MOVEMENT_COMPLETED'))
		{
			return;
		}

		// Get Joomla document object
		$document = JFactory::getDocument();

		// Prepare template parameters
		$templateParams = isset($document->params) ? $document->params : null;

		if (empty($templateParams))
		{
			$templateParams = JFactory::getApplication()->getTemplate(true);
			$templateParams = $templateParams->params;
		}

		// Then, check if script movement is disabled
		if ( ! $templateParams->get('scriptMovement'))
		{
			return;
		}

		// Move all script tags to the end of body section
		if ($n = count($parts = preg_split('/>[\s\t\r\n]*<script/', $html)))
		{
			// Re-generated script tags
			$tags = array();

			// Inline script code block combination status
			$combine = array();
			$last = 'inline';

			// Re-generate HTML document
			$temp = $parts[0];

			for ($i = 1; $i < $n; $i++)
			{
				// Get script tag
				$script = substr($parts[$i], 0, strpos($parts[$i], '</script') + 8);

				// Remove script tag from its original position
				$parts[$i] = str_replace($script, '', $parts[$i]);

				// Leave script tag as is if it is placed inside conditional comments
				if ((preg_match('/([\r\n][\s\t]*)<\!--\[if[^\]]*IE[^\]]*\]/', $temp, $match) AND strpos($temp, '<![endif]--') === false) OR (isset($notClosed) AND $notClosed))
				{
					$temp .= '>' . (isset($match[1]) ? $match[1] : '') . '<script' . $script . $parts[$i];

					// Look for the end of conditional comments
					$notClosed = strpos($parts[$i], '<![endif]--') !== false ? false : true;

					// Continue the loop
					continue;
				}

				// Leave script code block as is if document.write function is used inside
				if (strpos($script, 'document.write') !== false)
				{
					$temp .= ">\n<script" . $script . $parts[$i];

					// Continue the loop
					continue;
				}

				// Re-generate HTML document
				$temp .= $parts[$i];

				// Complete script tag
				$script = '<script' . $script . '>';

				if (strpos(preg_replace(array('/[\s\t\r\n]+/', '/[\s\t\r\n]+=[\s\t\r\n]+/'), array(' ', '='), $script), ' src=') === false)
				{
					// Clean-up inline script block
					$script = substr($script, strpos($script, '>') + 1, -9);

					if ($last == 'inline')
					{
						// Combine continuous script code block
						$combine[] = $script;
					}
					else
					{
						$combine = array($script);
						$last = 'inline';
					}
				}
				else
				{
					// Copy combined script code block
					! count($combine) OR $tags[] = '<script type="text/javascript">' . implode(";\n", $combine) . '</script>';

					// Copy script tag
					$tags[] = $script;

					// Reset variables
					$combine = array();
					$last = '';
				}
			}

			// Copy remaining combined script code block
			! count($combine) OR $tags[] = '<script type="text/javascript">' . implode(";\n", $combine) . '</script>';

			// Inject all re-generated script tags to the end of body section
			if (count($tags))
			{
				$html = str_replace('</body>', implode("\n", $tags) . '</body>', $temp);

				// Define a constant to state that scripts movement is completed
				define('JSN_TPLFW_SCRIPTS_MOVEMENT_COMPLETED', 1);
			}
		}
	}

	/**
	 * Check if there is new update for installed JoomlaShine product.
	 *
	 * @return  void
	 */
	protected static function checkUpdate()
	{
		// Check for update every predefined period of time
		if (time() - (int) self::$_tplfwParams->get('update-check', 0) < JSN_TPLFRAMEWORK_CHECK_UPDATE_PERIOD)
		{
			return;
		}

		// Backup request variable
		$backup = JFactory::getApplication()->input->getCmd('template');

		// Get method to execute database query
		$db				= JFactory::getDbo();
		$executeMethod	= method_exists($db, 'query') ? 'query' : 'execute';

		// Get list of installed JoomlaShine template
		$q	= $db->getQuery(true);

		$q->select('extension_id, name, type, element');
		$q->from('#__extensions');
		$q->where('type = ' . $q->quote('template'));
		$q->where('(custom_data = "jsntemplate" OR manifest_cache LIKE \'%,"group":"jsntemplate"}\')');

		$db->setQuery($q);

		if ($templates = $db->loadObjectList())
		{
			foreach ($templates AS $template)
			{
				// Set template name to request variable
				JFactory::getApplication()->input->set('template', $template->element);

				// Trigger check-update action of the update widget
				$widget = new JSNTplWidgetUpdate;
				$widget->checkUpdateAction();

				// Get result
				$result = $widget->getResponse();

				// Do we have update?
				foreach (array('template', 'framework') AS $ext)
				{
					if ($result[$ext]['hasUpdate'])
					{
						// Get extension details for template framework
						if ($ext == 'framework' AND ! isset($framework))
						{
							$q = $db->getQuery(true);

							$q->select('extension_id, name, type, element');
							$q->from('#__extensions');
							$q->where('type = ' . $q->quote('plugin'));
							$q->where('folder = ' . $q->quote('system'));
							$q->where('element = ' . $q->quote(basename(JSN_PATH_TPLFRAMEWORK)));

							$db->setQuery($q);

							$framework = $db->loadObject();
						}

						// Generate extension details
						$ext_id	= $ext == 'template' ? (int) $template->extension_id : (int) $framework->extension_id;
						$name	= $ext == 'template' ? $template->name : $framework->name;
						$type	= $ext == 'template' ? $template->type : $framework->type;
						$elm	= $ext == 'template' ? $template->element : $framework->element;

						// Check if update is stored before
						if ($ext == 'template' OR ! isset($current['framework']))
						{
							$q = $db->getQuery(true);

							$q->select('version');
							$q->from('#__updates');
							$q->where('extension_id = ' . $ext_id);

							$db->setQuery($q);

							$current[$ext] = $db->loadResult();
						}

						// Store update info to Joomla updates table
						$q = $db->getQuery(true);

						if ($current[$ext])
						{
							if (version_compare($current[$ext], $result[$ext]['newVersion'], '<'))
							{
								$q->update('#__updates');
								$q->set('version = ' . $q->quote($result[$ext]['newVersion']));
								$q->where('extension_id = ' . $ext_id);
								$q->where('version = ' . $q->quote($current[$ext]));

								$db->setQuery($q);
								$db->{$executeMethod}();
							}
						}
						else
						{
							$q->insert('#__updates');
							$q->columns('extension_id, name, element, type, version');
							$q->values($ext_id . ', ' . $q->quote(JText::_($name)) . ', ' . $q->quote($elm) . ', ' . $q->quote($type) . ', ' . $q->quote($result[$ext]['newVersion']));

							$db->setQuery($q);
							$db->{$executeMethod}();
						}
					}
				}
			}
		}

		// Reset update checking status
		$q = $db->getQuery(true);

		$q->update('#__extensions');
		$q->set("params = '" . json_encode(array('update-check' => time())) . "'");
		$q->where('type = ' . $q->quote('plugin'));
		$q->where('folder = ' . $q->quote('system'));
		$q->where('element = ' . $q->quote(basename(JSN_PATH_TPLFRAMEWORK)));

		$db->setQuery($q);
		$db->{$executeMethod}();

		// Restore request variable
		JFactory::getApplication()->input->set('template', $backup);
	}
}

/**
 * Finalize response body.
 *
 * @return  void
 */
function jsnTplFrameworkFinalize()
{
	define('JSN_TPLFW_LAST_EXECUTION', 1);
	PlgSystemJSNTPLFramework::onAfterRender();
}
