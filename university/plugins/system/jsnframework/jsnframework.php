<?php
/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
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
 * System plugin for initializing JSN Framework.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class PlgSystemJSNFramework extends JPlugin
{
	/**
	 * @var JApplication
	 */
	private static $_app = null;

	/**
	 * Register JSN Framework initialization.
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
		// Initialize JSN Framework
		require_once dirname(__FILE__) . '/libraries/loader.php';
		require_once dirname(__FILE__) . '/jsnframework.defines.php';

		// Get application object
		self::$_app = JFactory::getApplication();

		// Get requested component, view and task
		$this->option	= self::$_app->input->getCmd('option');
		$this->view		= self::$_app->input->getCmd('view');
		$this->task		= self::$_app->input->getCmd('task');

		// Redirect to update page if necessary
		if ($this->option == 'com_installer' AND $this->view == 'update' AND $this->task == 'update.update' AND count($cid = (array) self::$_app->input->getVar('cid', array())))
		{
			// Check if extension to updated is JoomlaShine product
			$db	= JFactory::getDbo();
			$q	= $db->getQuery(true);

			$q->select('element');
			$q->from('#__updates');
			$q->where('update_id = ' . (int) $cid[0]);

			$db->setQuery($q);
			$ext = $db->loadResult();

			if (in_array($ext, JSNVersion::$products))
			{
				return self::$_app->redirect('index.php?option=' . $ext . '&view=update');
			}
		}

		// Get active language
		$lang = JFactory::getLanguage();

		// Check if language file exists for active language
		if ( ! file_exists(JPATH_ROOT . '/administrator/language/' . $lang->get('tag') . '/' . $lang->get('tag') . '.plg_system_jsnframework.ini'))
		{
			// If requested component has the language file, install then load it
			if (file_exists(JPATH_ROOT . '/administrator/components/' . $this->option . '/language/admin/' . $lang->get('tag') . '/' . $lang->get('tag') . '.plg_system_jsnframework.ini'))
			{
				JSNLanguageHelper::install((array) $lang->get('tag'), false, true);
				$lang->load('plg_system_jsnframework', JPATH_ADMINISTRATOR, null, true);
			}
			// Otherwise, try to load language file from plugin directory
			else
			{
				$lang->load('plg_system_jsnframework', JSN_PATH_FRAMEWORK, null, true);
			}
		}
		else
		{
			$lang->load('plg_system_jsnframework', JPATH_ADMINISTRATOR, null, true);
		}

		// Disable notice and warning by default for our products.
		// The reason for doing this is if any notice or warning appeared then handling JSON string will fail in our code.
		if (function_exists('error_reporting') AND in_array($this->option, JSNVersion::$products))
		{
			error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT);
		}

		// Set event handlers to detect and update dependency installation / removal
		self::$_app->registerEvent('onExtensionAfterInstall',		'jsnExtFrameworkUpdateDependencyAfterInstallExtension');
		self::$_app->registerEvent('onExtensionBeforeUninstall',	'jsnExtFrameworkUpdateDependencyBeforeUninstallExtension');

		// Do update checking for Joomla! 3.1
		if (self::$_app->isAdmin() AND JSNVersion::isJoomlaCompatible('3.1') AND in_array($this->option, JSNVersion::$products) AND $this->task == 'checkUpdate')
		{
			$content = $this->checkUpdate();

			// Set header for XML content
			header('Content-Type: application/xml');

			// Send XML content to client
			echo $content;

			// Exit immediately to prevent Joomla! to process further
			exit;
		}
	}

	/**
	 * Event handler to re-parse request URI.
	 *
	 * @return  void
	 */
	public function onAfterRoute()
	{
		// Make sure our onAfterRender event handler is the last one executed
		self::$_app->registerEvent('onAfterRender', 'jsnExtFrameworkFinalize');
	}

	/**
	 * Before render needs using this function to make format of HTML of modules
	 *
	 * @return  Changed HTML format
	 */
	public function onBeforeRender()
	{
		if ( ! JSNVersion::isJoomlaCompatible(JSN_FRAMEWORK_REQUIRED_JOOMLA_VER))
		{
			return;
		}

		if (self::$_app->isAdmin())
		{
			// Ask user to review JoomlaShine product on JED
			$this->askForReview();
		}
		elseif (JSNVersion::isJoomlaCompatible('3.0') AND self::$_app->input->getCmd('poweradmin', 0) == 1)
		{
			$jsnHelper = JSNPositionsModel::_getInstance();
			$jsnHelper->renderEmptyModule();
		}
	}

	/**
	 * Do some output manipulation.
	 *
	 * Auto-inject <b>jsn-master tmpl-nameOfDefaultTemplate</b> into the class
	 * attribute of <b>&lt;body&gt;</b> tag if not already exists. This
	 * automation only affects backend page.
	 *
	 * @return  void
	 */
	public static function onAfterRender()
	{
		// Make sure the remaining process is executed in last order
		if ( ! defined('JSN_EXTFW_LAST_EXECUTION'))
		{
			return;
		}

		// Get active component
		$option = self::$_app->input->getCmd('option');

		// Get the rendered HTML code
		$html = JResponse::getBody();

		if (self::$_app->input->getVar('poweradmin'))
		{
			preg_match_all('#<a[^\>]*href\s*=\s*[\'"]([^"]*[^"]+)[\'"]#i', $html, $ms, PREG_SET_ORDER);
			if (count($ms))
			{
				foreach ($ms as $m)
				{
					$html = str_replace($m[0], str_replace($m[1], 'javascript:void(0)', $m[0]), $html) ;
				}
			}
		}

		// Do some fixes if this is admin page
		if (self::$_app->isAdmin())
		{
			// Fix asset links for Joomla 2.5
			if (JSNVersion::isJoomlaCompatible('2.5') AND ! JSNVersion::isJoomlaCompatible('3.0') AND strpos($html, JSN_URL_ASSETS) !== false)
			{
				// Get asset link
				if (preg_match_all('#<(link|script)([^\>]*)(href|src)="([^"]*' . JSN_URL_ASSETS . '[^"]+)"#i', $html, $matches, PREG_SET_ORDER))
				{
					foreach ($matches AS $match)
					{
						// Do replace
						$html = str_replace(
							$match[0],
							'<' . $match[1] . $match[2] . $match[3] . '="' . dirname(dirname($match[4])) . '/' . str_replace('.', '-', basename(dirname($match[4]))) . '/' . basename($match[4]) . '"',
							$html
						);
					}
				}
			}

			// Remove our extensions from the Joomla 3.0's global config page
			if ($option == 'com_config' AND JSNVersion::isJoomlaCompatible('3.0'))
			{
				$html = preg_replace(
					'#[\s\t]*<li[^\r\n]*>[\r\n\s\t]*<a[^\r\n]+index.php\?option=com_config&view=component&component=(' . implode('|', JSNVersion::$products) . ')[^\r\n]+</a>[\r\n\s\t]*</li>[\r\n]#',
					'',
					$html
				);
			}

			// Alter body tag
			if (preg_match('/<body[^>]*>/i', $html, $match) AND strpos($match[0], 'jsn-master tmpl-' . self::$_app->getTemplate()) === false)
			{
				if (strpos($match[0], 'class=') === false)
				{
					$match[1] = substr($match[0], 0, -1) . ' class=" jsn-master tmpl-' . self::$_app->getTemplate() . ' ">';
				}
				else
				{
					$match[1] = str_replace('class="', 'class=" jsn-master tmpl-' . self::$_app->getTemplate() . ' ', $match[0]);
				}

				$html = str_replace($match[0], $match[1], $html);
			}

			if (JSNVersion::isJoomlaCompatible('3.2'))
			{
				// Clean-up HTML5 fall-back script if running on Joomla 3.2
				if (
					in_array($option, JSNVersion::$products)
					AND
					preg_match('#[\r\n][\s\t]+<script src="[^"]*/media/system/js/html5fallback(-uncompressed)?\.js"[^>]+></script>#', $html, $match)
				)
				{
					$html = str_replace($match[0], '', $html);
				}

				// Temporary fix jQuery version conflict on Joomla 3.2
				$pos = strpos($html, JSN_URL_ASSETS . '/3rd-party/jquery/jquery.min.js');

				if ($pos !== false AND preg_match('#<script[^>]+src="' . JSN_URL_ASSETS . '/3rd-party/jquery/jquery.min.js"[^>]*></script>#', $html, $match))
				{
					$html = explode($match[0], $html);

					// Do some tricks on multiple jQuery instances
					$script
						= '<script type="text/javascript">'
						. "\n\t\t" . '(JoomlaShine = window.JoomlaShine || {});'
						. "\n\t\t" . '(!window.jQuery || (JoomlaShine.jQueryBackup = jQuery));'
						. "\n\t" . '</script>'
						. "\n\t" . $match[0];

					// Update document header
					$html[0] .= $script;

					// Truncate content
					$html[2] = substr($html[1], strpos($html[1], '</head>'));
					$html[1] = substr($html[1], 0, strpos($html[1], '</head>'));

					if (preg_match('#<script[^>]+src="[^"]*/media/jui/js/jquery(\.min)?\.js"[^>]*></script>#', $html[1], $match))
					{
						$script
							= '<script type="text/javascript">'
							. "\n\t\t" . '(JoomlaShine = window.JoomlaShine || {});'
							. "\n\t\t" . '(!window.jQuery || (JoomlaShine.jQuery = jQuery));'
							. "\n\t" . '</script>'
							. "\n\t" . $match[0];

						// Update document header
						$html[1] = str_replace($match[0], $script, $html[1]);
					}
					elseif (preg_match('#<script[^>]+src="[^"]*/js/template\.js[^"]*"[^>]*></script>#', $html[1], $match))
					{
						$script
							= '<script type="text/javascript">'
							. "\n\t\t" . '(JoomlaShine = window.JoomlaShine || {});'
							. "\n\t\t" . '(!window.jQuery || (JoomlaShine.jQuery = jQuery));'
							. "\n\t\t" . '(!JoomlaShine.jQueryBackup || (jQuery = JoomlaShine.jQueryBackup));'
							. "\n\t" . '</script>'
							. "\n\t" . $match[0];

						// Update document header
						$html[1] = str_replace($match[0], $script, $html[1]);
					}
					elseif (preg_match('#<script type="text/javascript">#', $html[1], $match))
					{
						$script
							= '<script type="text/javascript">'
							. "\n\t\t" . '(JoomlaShine = window.JoomlaShine || {});'
							. "\n\t\t" . '(!window.jQuery || (JoomlaShine.jQuery = jQuery));'
							. "\n\t\t" . '(!JoomlaShine.jQueryBackup || (jQuery = JoomlaShine.jQueryBackup));';

						// Update document header
						$html[1] = str_replace($match[0], $script, $html[1]);
					}

					$html = implode($html);

					// Fix for (function($) { ... })(jQuery);
					$tmp = preg_split('/\}[\s\t\r\n]*\)*\([^\r\n]*jQuery[^\r\n]*\)\s*;?/', $html);
					$html = '';
					$i = 0;

					foreach ($tmp AS $part)
					{
						$i++;

						if ($i == count($tmp))
						{
							$html .= $part;
						}
						else
						{
							$parts = preg_split('/\(\s*function\s*\(\s*\$\s*\)\s*\{/', $part, 2);

							if (count($parts) < 2)
							{
								$html .= $part;
							}
							elseif (stripos($parts[1], 'jsn') !== false)
							{
								$html .= "{$parts[0]}(function($) {{$parts[1]}})((window.JoomlaShine && JoomlaShine.jQuery) ? JoomlaShine.jQuery : jQuery);";
							}
							else
							{
								$html .= "{$parts[0]}(function($) {{$parts[1]}})(jQuery);";
							}
						}
					}

					// Remove JSN ImageShow's buggy fix for jQuery conflict
					if (strpos($html, 'administrator/components/com_imageshow/assets/js/joomlashine/jquery.safe.element.js') !== false)
					{
						$html = preg_replace('#[\r\n][\s\t]*<script[^>]+src="[^"]*/joomlashine/jquery.safe.element.js[^"]*"[^>]*></script>#', '', $html);
					}
				}
			}
		}

		// Attach JS declaration
		$html = str_replace('</head>', JSNHtmlAsset::buildHeader() . '</head>', $html);

		// Optimize script tags position
		self::moveScriptTags($html);

		// Fix compatibility problem between require.js and RokPad editor
		if (strpos($html, '/plugins/editors/rokpad/'))
		{
			if (preg_match_all('#[\r\n][\s\t]*<script[^>]+src="[^"]*/plugins/editors/rokpad/[^"]+"[^>]*></script>#', $html, $matches, PREG_SET_ORDER))
			{
				foreach ($matches AS $match)
				{
					// Clean the script tag from its original position
					$html = str_replace($match[0], '', $html);

					// Inject the removed script tag into the end of head section
					$html = str_replace('</head>', $match[0] . '</head>', $html);
				}
			}
		}

		// Set new response body
		JResponse::setBody($html);

		// Execute update checker
		if ( ! JSNVersion::isJoomlaCompatible('3.1'))
		{
			! isset($this) OR $this->checkUpdate();
		}
	}

	/**
	 * Proceed positions rendering
	 *
	 * Remove default tp=1 layout, replace by jsn style to
	 * show page positions
	 *
	 * @return  void
	 */
	public function onAfterDispatch()
	{
		if ( ! JSNVersion::isJoomlaCompatible(JSN_FRAMEWORK_REQUIRED_JOOMLA_VER))
		{
			return;
		}

		// Keep this for joomla 2.5. Will be deprecated.
		if (JSNVersion::isJoomlaCompatible('2.5') AND ! JSNVersion::isJoomlaCompatible('3.0'))
		{
			if (self::$_app->isAdmin()
				AND self::$_app->input->getVar('format', '') != 'raw'
				AND self::$_app->input->getVar('option', '') == 'com_poweradmin'
				AND self::$_app->input->getVar('view') != 'update'
				AND self::$_app->input->getVar('view') != 'changeposition')
			{
				$version = PoweradminHelper::getVersion();

				if (version_compare($version, '1.1.3', '>='))
				{
					$JSNMedia = JSNFactory::getMedia();
					$JSNMedia->addMedia();
				}
			}
		}

		if (self::$_app->input->getCmd('poweradmin', 0) == 1)
		{
			$jsnHelper = JSNPositionsModel::_getInstance();
			$jsnHelper->renderEmptyComponent();
			$jsnHelper->renderEmptyModule();
		}
	}

	/**
	 * Method to move all script tags from head section to the end of body section.
	 *
	 * @param   string  &$html  Generated response body.
	 *
	 * @return  void
	 */
	private static function moveScriptTags(&$html)
	{
		// Get active component
		$option = self::$_app->input->getCmd('option');

		// First, check if current page is rendered by our products
		if ( ! in_array($option, JSNVersion::$products))
		{
			return;
		}

		// Second, check if script movement is already done by our template framework
		if (defined('JSN_TPLFW_SCRIPTS_MOVEMENT_COMPLETED'))
		{
			return;
		}

		// Now, get configuration for our product
		$cfg = JSNConfigHelper::get($option);

		// Do not continue if script movement is disabled
		if ( ! $cfg->get('script_movement'))
		{
			return;
		}

		// Move all script tags to the end of body section
		if ((self::$_app->isSite() OR in_array($option, JSNVersion::$products)) AND $n = count($parts = preg_split('/>[\s\t\r\n]*<script/', $html)))
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
				define('JSN_EXTFW_SCRIPTS_MOVEMENT_COMPLETED', 1);
			}
		}
	}

	/**
	 * Ask user to review JoomlaShine product on JED.
	 *
	 * @return  void
	 */
	private function askForReview()
	{
		// Continue only if this is admin page of JoomlaShine product
		if (self::$_app->isAdmin() AND in_array($this->option, JSNVersion::$products))
		{
			// Get product options
			$config = JSNConfigHelper::get($this->option);

			if ($config->get('review_popup', 1))
			{
				// Get time difference
				$time = time();
				$last = $config->get('last_ask_for_review', 0);

				if ($last == 0)
				{
					$last = filemtime(JPATH_ROOT . "/administrator/components/{$this->option}/" . substr($this->option, 4) . '.xml');
				}

				// Check if it's time to ask for review
				if ($time - $last >= REVIEW_POPUP_PERIOD)
				{
					// Load script to popup a modal ask user for review
					JSNHtmlAsset::loadScript(
						'jsn/askforreview',
						array(
							'url'		=> JUri::root() . "plugins/system/jsnframework/libraries/joomlashine/choosers/review/index.php?component={$this->option}",
							'language'	=> JSNUtilsLanguage::getTranslated(array('JSN_EXTFW_CHOOSERS_REVIEW_ON_JED'))
						)
					);

					// Get config model
					$model = new JSNConfigModel;

					// Store time of last popup
					$form = $model->getForm(array(), true, JPATH_ROOT . '/administrator/components/' . $this->option . '/config.xml');
					$data = array('last_ask_for_review' => $time);

					try
					{
						// Save new configuration
						$model->save($form, $data);
					}
					catch (Exception $e)
					{
						// Do nothing as this is a background process
					}
				}
			}
		}
	}

	/**
	 * Check if there is new update for installed JoomlaShine product.
	 *
	 * @return  mixed
	 */
	private function checkUpdate()
	{
		// Initialize extensions to check for update
		$exts = JSNVersion::isJoomlaCompatible('3.1') ? array($this->option) : JSNVersion::$products;

		// Get Joomla version
		$joomlaVersion = new JVersion;

		// Get list of JoomlaShine product need to be checked for new update
		$checks = $products = array();

		foreach ($exts AS $product)
		{
			// Instantiate JSN Config model
			self::$_app->input->set('option', $product);
			$model = new JSNConfigModel;

			// Only continue if product is installed
			if (is_readable(JPATH_ROOT . '/administrator/components/' . $product . '/config.xml'))
			{
				// Get product configuration
				$cfg = JSNConfigHelper::get($product);

				if ( ! $cfg->get('config-table-not-exists') AND ! $cfg->get('live_update_checking', 0))
				{
					if (time() - $cfg->get('live_update_last_check', 0) >= ((int) $cfg->get('live_update_check_interval', 6) * 60 * 60))
					{
						// Store necessary data to config table
						$form = $model->getForm(array(), true, JPATH_ROOT . '/administrator/components/' . $product . '/config.xml');
						$data = array('live_update_checking' => 1);

						try
						{
							$model->save($form, $data);

							// Store product for checking new update later
							file_exists($define = JPATH_ROOT . '/administrator/components/' . $product . '/' . substr($product, 4) . '.defines.php')
								OR file_exists($define = JPATH_ROOT . '/administrator/components/' . $product . '/defines.' . substr($product, 4) . '.php')
								OR file_exists($define = JPATH_ROOT . '/administrator/components/' . $product . '/defines.php')
								OR $define = null;

							if ( ! empty($define))
							{
								if ($product == 'com_imageshow' AND ! class_exists('JSNISFactory'))
								{
									// JSN ImageShow specific fix
									if (file_exists(JPATH_ROOT . '/administrator/components/com_imageshow/classes/jsn_is_factory.php'))
									{
										require_once JPATH_ROOT . '/administrator/components/com_imageshow/classes/jsn_is_factory.php';
									}
									else
									{
										// Do nothing as this is a background process
										continue;
									}
								}

								require_once $define;
								$products[JSNUtilsText::getConstant('IDENTIFIED_NAME')] = $product;
								$checks[JSNUtilsText::getConstant('IDENTIFIED_NAME')] = JSNUtilsText::getConstant('VERSION', $product);
							}
						}
						catch (Exception $e)
						{
							// Do nothing as this is a background process
							continue;
						}
					}
				}
			}
		}

		// Check update for installed JoomlaShine product
		if (count($products))
		{
			if ($checks = JSNUpdateHelper::check($checks, $joomlaVersion->RELEASE))
			{
				// Get database object
				$db = JFactory::getDbo();

				foreach ($checks AS $id => & $result)
				{
					// Instantiate JSN Config model
					self::$_app->input->set('option', $products[$id]);
					$model = new JSNConfigModel;

					// Get product configuration
					$cfg = JSNConfigHelper::get($products[$id]);

					// Decode last update info
					$last = is_string($last = $cfg->get('live_update_last_notified', '{"version":"0.0.0"}')) ? json_decode($last) : $last;

					if ($result AND version_compare($result->version, $last->version, '>'))
					{
						// Get product details
						if ($info = JSNUtilsXml::loadManifestCache($products[$id]))
						{
							$name = 'JSN ' . preg_replace('/^JSN\s*/i', '', $info->name) . (($edition = JSNUtilsText::getConstant('EDITION', $products[$id])) ? ' ' . $edition : '');

							if ( ! JSNVersion::isJoomlaCompatible('3.1'))
							{
								// Get extension ID
								$q = $db->getQuery(true);

								$q->select('extension_id');
								$q->from('#__extensions');
								$q->where("element = '" . $products[$id] . "'");
								$q->where("type = 'component'", 'AND');

								$db->setQuery($q);

								$eid = $db->loadResult();

								// Check if update is stored before
								$q = $db->getQuery(true);

								$q->select('version');
								$q->from('#__updates');
								$q->where("extension_id = '{$eid}'");

								$db->setQuery($q);

								// Store update info to Joomla updates table
								if ($current = $db->loadResult())
								{
									$q = $db->getQuery(true);

									$q->update('#__updates');
									$q->set("version = '{$result->version}'");
									$q->where("extension_id = '{$eid}'");
									$q->where("version = '{$current}'", 'AND');
								}
								else
								{
									$q = $db->getQuery(true);

									$q->insert('#__updates');
									$q->columns('extension_id, name, element, type, version');
									$q->values("{$eid}, '{$name}', '{$products[$id]}', 'component', '{$result->version}'");
								}

								$db->setQuery($q);
								$db->execute();
							}
							else
							{
								// Generate XML content for latest update
								$xml[] = '<?xml version="1.0" encoding="utf-8"?>';
								$xml[] = '<extensionset name="JoomlaShine Extensions" description="JoomlaShine Extensions Updates">';
								$xml[] = '	<extension name="' . $name . '" element="' . $products[$id] . '" type="component" client="administrator" version="' . $result->version . '" detailsurl="" />';
								$xml[] = '</extensionset>';

								// Write XML content to a local cache file
								JFile::write(JFactory::getConfig()->get('tmp_path') . '/' . str_replace('com_', 'jsn_', $products[$id]) . '_update.xml', implode("\n", $xml));
							}

							if ($cfg->get('live_update_notification', 0))
							{
								// Get super administrator account to notify about update available
								if ( ! isset($admins))
								{
									// Get Joomla config
									$joomlaConfig = JFactory::getConfig();

									// Build query to get super administrator account
									$q	= $db->getQuery(true);

									$q->select('u.id, u.email');
									$q->from('#__user_usergroup_map AS g');
									$q->join('INNER', '#__users AS u ON g.user_id = u.id');
									$q->where('g.group_id = 8');
									$q->where('u.sendEmail = 1');
									$q->where('u.block = 0');

									$db->setQuery($q);

									try
									{
										if ( ! ($admins = $db->loadObjectList()))
										{
											return;
										}
									}
									catch (Exception $e)
									{
										return;
									}
								}

								// Generate replacement
								$substitutions = array(
									'__SITE_NAME__'	=> $joomlaConfig->get('sitename'),
									'__SITE_URL__'	=> JUri::root(),
									'__PRODUCT__'	=> $name,
									'__CURRENT__'	=> JSNUtilsText::getConstant('VERSION', $products[$id]),
									'__NEW__'		=> $result->version,
									'__LINK__'		=> JUri::root() . 'administrator/index.php?option=' . $products[$id] . '&view=update'
								);

								// Initialize email subject and message
								$subject = JText::_('JSN_EXTFW_UPDATE_NOTIFICATION_SUBJECT');
								$message = JText::_('JSN_EXTFW_UPDATE_NOTIFICATION_MESSAGE');

								foreach ($substitutions AS $k => $v)
								{
									$subject = str_replace($k, $v, $subject);
									$message = str_replace($k, $v, $message);
								}

								foreach ($admins AS $admin)
								{
									// Initialize mailer then send update notification email
									try
									{
										$mailer = JFactory::getMailer();
										$mailer->setSender(array($joomlaConfig->get('mailfrom'), $joomlaConfig->get('fromname')));
										$mailer->addRecipient($admin->email);
										$mailer->setSubject($subject);
										$mailer->setBody($message);
										$mailer->Send();
									}
									catch (Exception $e)
									{
										return;
									}
								}
							}
						}
					}

					// Store last check update time
					$form = $model->getForm(array(), true, JPATH_ROOT . '/administrator/components/' . $products[$id] . '/config.xml');
					$data = array('live_update_checking' => '0', 'live_update_last_check' => time());

					if ($result)
					{
						// Store last version notified
						$data['live_update_last_notified'] = json_encode($result);
					}

					try
					{
						$model->save($form, $data);
					}
					catch (Exception $e)
					{
						// Do nothing as this is a background process
						continue;
					}
				}
			}
		}

		// Restore current option
		self::$_app->input->set('option', $this->option);

		// In Joomla! 3.1, return generated XML content about latest update
		if (JSNVersion::isJoomlaCompatible('3.1'))
		{
			if (isset($xml))
			{
				return implode("\n", $xml);
			}
			elseif (is_readable($cache = JFactory::getConfig()->get('tmp_path') . '/' . str_replace('com_', 'jsn_', $this->option) . '_update.xml'))
			{
				return JFile::read($cache);
			}
			else
			{
				return '<?xml version="1.0" encoding="utf-8"?><extensionset />';
			}
		}
	}
}

/**
 * Finalize response body.
 *
 * @return  void
 */
function jsnExtFrameworkFinalize()
{
	define('JSN_EXTFW_LAST_EXECUTION', 1);
	PlgSystemJSNFramework::onAfterRender();
}

/**
 * Update dependency after an extension is installed.
 *
 * @param   object  $installer   Joomla installer object.
 * @param   mixed   $identifier  Extension ID on installation success, boolean FALSE otherwise.
 *
 * @return  void
 */
function jsnExtFrameworkUpdateDependencyAfterInstallExtension($installer, $identifier)
{
	if (is_integer($identifier))
	{
		// Get installed extension
		$ext = basename($installer->getPath('extension_administrator'));

		// Check if our product is installed
		if (in_array($ext, JSNVersion::$products))
		{
			// Build query to get dependency installation status
			$db	= JFactory::getDbo();
			$q	= $db->getQuery(true);

			$q->select('manifest_cache, custom_data');
			$q->from('#__extensions');
			$q->where("element = 'jsnframework'");
			$q->where("type = 'plugin'", 'AND');
			$q->where("folder = 'system'", 'AND');

			$db->setQuery($q);

			// Load dependency installation status
			$status = $db->loadObject();

			$ext = substr($ext, 4);
			$dep = ! empty($status->custom_data) ? (array) json_decode($status->custom_data) : array();

			// Update dependency list
			in_array($ext, $dep) OR $dep[] = $ext;
			$status->custom_data = array_unique($dep);

			// Build query to update dependency data
			$q = $db->getQuery(true);

			$q->update('#__extensions');
			$q->set("custom_data = '" . json_encode($status->custom_data) . "'");

			// Backward compatible: keep data in this column for older product to recognize
			$manifestCache = json_decode($status->manifest_cache);
			$manifestCache->dependency = $status->custom_data;

			$q->set("manifest_cache = '" . json_encode($manifestCache) . "'");

			// Backward compatible: keep data in this column also for another old product to recognize
			$q->set("params = '" . json_encode((object) array_combine($status->custom_data, $status->custom_data)) . "'");

			$q->where("element = 'jsnframework'");
			$q->where("type = 'plugin'", 'AND');
			$q->where("folder = 'system'", 'AND');

			$db->setQuery($q);
			$db->execute();
		}
	}
}

/**
 * Update dependency before an extension is being removed.
 *
 * @param   integer  $identifier  Extension ID.
 *
 * @return  boolean
 */
function jsnExtFrameworkUpdateDependencyBeforeUninstallExtension($identifier)
{
	// Get extension being removed
	$ext = JTable::getInstance('Extension');
	$ext->load($identifier);
	$ext = $ext->element;

	// Check if our product is being removed
	if (in_array($ext, JSNVersion::$products))
	{
		// Build query to get dependency installation status
		$db	= JFactory::getDbo();
		$q	= $db->getQuery(true);

		$q->select('manifest_cache, custom_data');
		$q->from('#__extensions');
		$q->where("element = 'jsnframework'");
		$q->where("type = 'plugin'", 'AND');
		$q->where("folder = 'system'", 'AND');

		$db->setQuery($q);

		// Load dependency installation status
		$status = $db->loadObject();

		$ext	= substr($ext, 4);
		$deps	= ! empty($status->custom_data) ? (array) json_decode($status->custom_data) : array();

		// Update dependency tracking
		$status->custom_data = array();

		foreach ($deps AS $dep)
		{
			// Backward compatible: ensure that product is not removed
			// if ($dep != $ext)
			if ($dep != $ext AND is_dir(JPATH_BASE . '/components/com_' . $dep))
			{
				$status->custom_data[] = $dep;
			}
		}

		// Build query to update dependency data
		$q = $db->getQuery(true);

		$q->update('#__extensions');
		$q->set("custom_data = '" . json_encode($status->custom_data) . "'");

		// Backward compatible: keep data in this column for older product to recognize
		$manifestCache = json_decode($status->manifest_cache);
		$manifestCache->dependency = $status->custom_data;

		$q->set("manifest_cache = '" . json_encode($manifestCache) . "'");

		// Backward compatible: keep data in this column also for another old product to recognize
		$q->set("params = '" . (count($status->custom_data) ? json_encode((object) array_combine($status->custom_data, $status->custom_data)) : '') . "'");

		$q->where("element = 'jsnframework'");
		$q->where("type = 'plugin'", 'AND');
		$q->where("folder = 'system'", 'AND');

		$db->setQuery($q);
		$db->execute();
	}

	// Always return TRUE so the extension can be removed
	return true;
}
