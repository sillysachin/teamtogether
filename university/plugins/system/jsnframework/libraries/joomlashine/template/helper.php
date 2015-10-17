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

require dirname(__FILE__) . '/model.php';

// Import necessary Joomla library
jimport('joomla.filesystem.file');

/**
 * Helper Class for template loading
 *
 * Use this class to load default site template,
 * get all positions in current template, bypass unsupported
 * templates from some providers...
 *
 * @package  JSN_Framework
 * @since    1.0.3
 */
class JSNTemplateHelper
{
	/**
	 *  Current site template
	 *
	 *  @var stdClass
	 */
	private $_template = '';

	/**
	 * Template author
	 *
	 * @var string
	 */
	private $_author   = 'default';

	/**
	* Constructor
	*/
	public function __construct()
	{
		try
		{
			/* get template assigned */
			$this->_template = JSNTemplateModel::getDefaultTemplate();

			/* get template mainfet*/
			$client = JApplicationHelper::getClientInfo($this->_template->client_id);
			@$this->_template->xml = new SimpleXMLElement($client->path . '/templates/' . $this->_template->element . '/templateDetails.xml', null, true);

			/* get author template */
			$author = JString::trim(JString::strtolower($this->_template->xml->author));

			if ( ! $author)
			{
				$author = JString::trim(JString::strtolower($this->_template->xml->authorEmail));

				if ($author)
				{
					@list($eName, $eHost) = explode('@', $author);
					@list($this->_author, $dotCom) = explode('.', $author);
				}
			}
			else
			{
				@list($this->_author, $dotCom) = explode('.', $author);
			}

			if (empty($this->_author))
			{
				$this->_author = 'default';
			}

			switch ($this->_author)
			{
				case 'joomagic':
					// An template using T3 Framework
					$this->_author = 'joomlart';
				break;
			}
		}
		catch (Exception $e)
		{
			throw new Exception(JText::_('JSN_EXTFW_NOTICE_SITE_TEMPLATE_NOT_SET'));
		}
	}

	/**
	 * Return global JSNTemplate object
	 *
	 * @return  object
	 */
	public static function getInstance()
	{
		static $instances;

		if ( ! isset($instances))
		{
			$instances = array();
		}

		if (empty($instances['jsntemplatehelper']))
		{
			$instance = new JSNTemplateHelper;
			$instances['jsntemplatehelper'] = &$instance;
		}

		return $instances['jsntemplatehelper'];
	}

	/**
	 * Get template author
	 *
	 * @return  string
	*/
	public function getAuthor()
	{
		return $this->_author;
	}

	/**
	 * Load template position to javascript array
	 *
	 * @param   boolean  $loadparameter  Whether to load parameter or not?
	 *
	 * @return  string
	 */
	public function loadArrayJavascriptTemplatePositions($loadparameter = false)
	{
		if ($loadparameter)
		{
			$positions = $this->getTemplatePositions();

			if (count($positions) == 0)
			{
				$positions = $this->loadXMLPositions();
			}

			$js_arr_positions[]		= ' var positions = new Array(' . count($positions) . ');';
			$js_arr_position_keys[]	= 'var position_keys = new Array(' . count($positions) . ');';

			for ($i = 0; $i < count($positions); $i++)
			{
				if (count($positions[$i]->params))
				{
					$params = '';

					foreach ($positions[$i]->params AS $key => $val)
					{
						if ($params == '')
						{
							$params = $key . '=' . $val;
						}
						else
						{
							$params .= ',' . $key . '=' . $val;
						}
					}
				}
				else
				{
					$params = '';
				}

				$js_arr_positions[]		= ' positions[\'' . trim(strtolower($positions[$i]->name)) . '\']= \'' . trim(strtolower($positions[$i]->name)) . '||' . $params . '\';';
				$js_arr_position_keys[]	= ' position_keys[\'' . $i . '\']= \'' . trim(strtolower($positions[$i]->name)) . '\';';
			}

			return implode(PHP_EOL, $js_arr_positions) . PHP_EOL . implode(PHP_EOL, $js_arr_position_keys);
		}

		$positions = $this->loadXMLPositions();

		$js_arr_positions[] = ' var positions = new Array();';

		for ($i = 0; $i < count($positions); $i++)
		{
			$js_arr_positions[] = ' positions[' . $i . ']= \'' . trim(strtolower($positions[$i]->name)) . '\';';
		}

		return implode(PHP_EOL, $js_arr_positions);
	}

	/**
	 * Method to load template positions
	 *
	 * @return  array
	*/
	public function loadXMLPositions()
	{
		$specialProviders = array('joomlart');

		if (in_array($this->_author, $specialProviders))
		{
			$positions = $this->loadOtherProviderPositions($this->_author);
		}
		else
		{
			$positions		= array();
			$hasPositions	= array();
			$xml_positions	= $this->_template->xml->xpath('//positions/position');

			foreach ($xml_positions AS $position)
			{
				$position = (string) $position;

				if ( ! in_array($position, $hasPositions))
				{
					$_position = new stdClass;
					$_position->name = $position;
					$_position->params = array('style' => 'none');

					array_push($hasPositions, $position);
					array_push($positions, $_position);
				}
			}
		}

		// JArrayHelper::sortObjects($positions, 'name', 1, true);
		return $positions;
	}

	/**
	 * Method to load other provider's template positions
	 *
	 * @param   string  $author  Author
	 *
	 * @return  string
	 */
	public function loadOtherProviderPositions($author)
	{
		$funcname = $author . 'PostionLoad';

		if (method_exists($this, $funcname))
		{
			return $this->$funcname();
		}
	}

	/**
	 * Method to load JA's template positions
	 *
	 * @return  string
	 */
	protected function joomlartPostionLoad()
	{
		$positions		= array();
		$hasPositions	= array();
		$jat3CommonFile	= JPATH_ROOT . '/plugins/system/jat3/jat3/core/common.php';
		$templateName	= $this->_template->element;

		if (file_exists($jat3CommonFile))
		{
			jimport($jat3CommonFile);
			$jat3_engine_layout_path = JPATH_ROOT . '/templates/' . $templateName . '/etc/layouts/default.xml';
			$layout_info = T3Common::getXML($jat3_engine_layout_path);

			if (is_file($jat3_engine_layout_path))
			{
				$layout_info = T3Common::getXML($jat3_engine_layout_path);

				foreach ($layout_info['children'] AS $v)
				{
					if ($v['name'] == 'blocks')
					{
						foreach ($v['children'] AS $block)
						{
							if ( ! $block['data'])
							{
								$position = (string) $block['attributes']['name'];

								if ( ! in_array($position, $hasPositions))
								{
									$_position = new stdClass;
									$_position->name = $position;
									$_position->params = array('style' => 'none');

									array_push($hasPositions, $position);
									array_push($positions, $_position);
								}
							}
							else
							{
								$_l = explode(",", $block['data']);

								foreach ($_l AS $position)
								{
									$_position = new stdClass;
									$_position->name = $position;
									$_position->params = array('style' => 'none');

									array_push($hasPositions, $position);
									array_push($positions, $_position);
								}
							}
						}
					}
				}
			}
		}

		return $positions;
	}

	/**
	 * Overwrite file modules loading of yootheme
	 *
	 * @return  void
	*/
	protected function yootheme()
	{
		$client = JApplicationHelper::getClientInfo($this->_template->client_id);
		$versionFolder = 'joomla.' . substr(JVERSION, 0, 3);

		if (JVERSION >= 2.5)
		{
			$modules_file	= $client->path . '/templates/' . $this->_template->element . '/warp/systems/joomla/layouts/modules.php';
			$rename_path	= $client->path . '/templates/' . $this->_template->element . '/warp/systems/joomla/layouts/modules.JSN.ORG.php';
		}
		else
		{
			$modules_file	= $client->path . '/templates/' . $this->_template->element . '/warp/systems/' . $versionFolder . '/layouts/modules.php';
			$rename_path	= $client->path . '/templates/' . $this->_template->element . '/warp/systems/' . $versionFolder . '/layouts/modules.JSN.ORG.php';
		}

		if (file_exists($modules_file))
		{
			$contents = JFile::read($modules_file);
		}
		else
		{
			$contents = '';
		}

		if ( ! preg_match('/jsn-element-container_inner/i', $contents))
		{
			jimport('joomla.filesystem.file');

			if ( ! file_exists($rename_path))
			{
				JFile::move($modules_file, $rename_path);
			}

			if (JVERSION >= 2.5)
			{
				JFile::copy(JSN_TEMPLATE_CLASSES_OVERWRITE . 'yootheme_modules_j25.php', $modules_file);
			}
			else
			{
				JFile::copy(JSN_TEMPLATE_CLASSES_OVERWRITE . 'yootheme_modules.php', $modules_file);
			}
		}
	}

	/**
	 * Overwrite index.php of T3 template
	 *
	 * @return  void
	*/
	protected function joomlart()
	{
		$client = JApplicationHelper::getClientInfo($this->_template->client_id);
		$index_file = $client->path . '/templates/' . $this->_template->element . '/index.php';
		jimport('joomla.filesystem.file');
		$contents = JFile::read($index_file);

		if ( ! preg_match('/JSNT3Template/i', $contents))
		{
			JFile::move($index_file, $client->path . '/templates/' . $this->_template->element . '/index.JSN.ORG.php');
			JFile::copy(JSN_TEMPLATE_CLASSES_OVERWRITE . 'joomlart.php', $index_file);
		}
	}

	/**
	 * Helper JSNPOWERADMIN change joomlaxtc template
	 *
	 * @return  void
	 */
	protected function joomlaxtc()
	{
		$client = JApplicationHelper::getClientInfo($this->_template->client_id);
		$index_file = $client->path . '/templates/' . $this->_template->element . '/index.php';
		jimport('joomla.filesystem.file');
		$contents = JFile::read($index_file);

		if ( ! preg_match('/JSNJoomlaXTCHelper/i', $contents))
		{
			JFile::move($index_file, $client->path . '/templates/' . $this->_template->element . '/index.JSN.ORG.php');
			JFile::copy(JSN_TEMPLATE_CLASSES_OVERWRITE . 'joomlaxtc.php', $index_file);
		}
	}

	/**
	 * Get information of positions in index.php of rockettheme template
	 *
	 * @return  mixed
	*/
	protected function rockettheme()
	{
		$client = JApplicationHelper::getClientInfo($this->_template->client_id);
		$index_file_path = $client->path . '/templates/' . $this->_template->element . '/index.php';

		if (file_exists($index_file_path))
		{
			$file_contents = JFile::read($index_file_path);

			if (preg_match_all('#displayModules(.*);#iU', $file_contents, $matches))
			{
				$positions = $this->loadXMLPositions();
				$params = array();
				$i = 0;

				foreach ($matches[1] AS $matche)
				{
					$params[$i] = explode(',', $matche);

					for ($j = 0; $j < count($params[$i]); $j++)
					{
						$params[$i][$j] = str_replace(array('(', "'", ')'), array('','',''), $params[$i][$j]);
					}
					$i++;
				}

				for ($i = 0; $i < count($positions); $i++)
				{
					$position = $positions[$i];
					$positions[$i] = new stdClass;
					$positions[$i]->name = $position->name[0];
					$positions[$i]->params = array();

					foreach ($params AS $param)
					{
						if (preg_match('/' . $param[0] . '/i', $positions[$i]->name))
						{
							$positions[$i]->params = array('style' => $param[1]);
							break;
						}
					}
				}

				return $positions;
			}
		}

		return null;
	}

	/**
	 * Get information of positions in index.php of gavickpro template
	 *
	 * @return  mixed
	*/
	protected function gavickpro()
	{
		$client = JApplicationHelper::getClientInfo($this->_template->client_id);
		$layout_settings = $client->path . '/templates/' . $this->_template->element . '/lib/framework/gk.const.php';

		if ( ! file_exists($layout_settings))
		{
			return $this->defaultTemplate();
		}
		else
		{
			include $layout_settings;

			$positions = array();

			foreach ($GK_TEMPLATE_MODULE_STYLES AS $key => $value)
			{
				$position = new stdClass;
				$position->name = $key;
				$position->params = array('style' => $value);
				$positions[] = $position;
			}

			return $positions;
		}
	}

	/**
	 * Get information of positions index.php file
	 *
	 * @return  array
	*/
	protected function defaultTemplate()
	{
		$client = JApplicationHelper::getClientInfo($this->_template->client_id);
		$index_file_path = $client->path . '/templates/' . $this->_template->element . '/index.php';

		if (file_exists($index_file_path))
		{
			$file_contents = JFile::read($index_file_path);

			if (preg_match_all('#<jdoc:include\ type="([^"]+)" (.*)\/>#iU', $file_contents, $matches))
			{
				$positions = array();
				$modules = $matches[2];

				foreach ($modules AS $module)
				{
					if ($module != "")
					{
						$params = explode(' ', $module);
						$position = new stdClass;
						$position->name = str_replace(array('name="', '"'), array('', ''), $params[0]);
						$position->params = array();

						if (count($params) > 1)
						{
							for ($i = 1; $i < count($params); $i++)
							{
								if ($params[$i] != '')
								{
									$tmp = explode('=', $params[$i]);

									if (count($tmp) > 1)
									{
										$position->params[$tmp[0]] = str_replace('"', '', $tmp[1]);
									}
								}
							}
						}

						$positions[] = $position;
					}
				}

				return $positions;
			}
		}

		return null;
	}

   /**
	 * Get includes in template
	 *
	 * @return  mixed
	*/
	public function getTemplatePositions()
	{
		switch ($this->_author)
		{
			case 'gavick':
				return $this->gavickPro();

			case 'rockettheme':
				return $this->rockettheme();

			case 'joomlajunkie':
			case 'yootheme':
				return;

			case 'default':
			default:
				return $this->defaultTemplate();
		}
	}
}
