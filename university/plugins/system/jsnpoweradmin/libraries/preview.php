<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: preview.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class JSNPowerAdminBarPreview
{
	/**
	 * Contains name of the active component
	 * @var string
	 */
	private $option;
	
	/**
	 * All parameters on QueryString
	 * @var array
	 */
	private $params;
	
	/**
	 * Instance of supporter class
	 * @var JSNPowerAdminBarPreviewAdapter
	 */
	private $adapter;
	
	/**
	 * Constructor of the class
	 */
	public function __construct ()
	{
		$this->option  = JRequest::getVar('option');
		$this->params  = JRequest::get('GET', array());
		
		$this->loadAdapter();
	}
	
	/**
	 * Retrieve preview link based on params. If not match, default link will be returned
	 * @return string
	 */
	public function getPreviewLink ()
	{
		if ($this->adapter == null || !($this->adapter instanceof JSNPowerAdminBarPreviewAdapter))
			return JUri::root();
		
		$preview = $this->adapter->getPreviewLink();
		
		if (strpos($preview, '&Itemid=') === false && strpos($preview, 'option=') !== false) {
			$dbo = JFactory::getDBO();
			$dbo->setQuery(sprintf('SELECT id FROM #__menu WHERE link LIKE "%s" LIMIT 1', $preview));
			$itemId = $dbo->loadResult();
			
			if (!is_numeric($itemId)) {
				$dbo->setQuery(sprintf('SELECT id FROM #__menu WHERE home=1 LIMIT 1', $preview));
				$itemId = $dbo->loadResult();
			}
			
			$preview.= sprintf('&Itemid=%d', $itemId);
		}
		
		return JUri::root().$preview;
	}
	
	/**
	 * Load adapter for detect preview link
	 * @return JSNPowerAdminBarPreviewAdapter
	 */
	private function loadAdapter ()
	{
		$builtInFile 	= dirname(dirname(__FILE__))."/supports/{$this->option}.php";
		$builtInXmlFile = dirname(dirname(__FILE__))."/supports/{$this->option}.xml";
		$xmlFile 		= JPATH_ADMINISTRATOR."/components/{$this->option}/preview.xml";

		if (is_file($builtInFile)) {
			require_once $builtInFile;
			
			$className = sprintf(
				'JSNPowerAdminBarSupport%s', 
				ucfirst(
					substr(
						$this->option, 
						strpos($this->option, '_') + 1
					)
				)
			);
			
			$this->adapter = (class_exists($className)) ? new $className($this->option, $this->params) : null;
		}
		
		else {
			require_once dirname(dirname(__FILE__))."/supports/base.php";
			
			
			$this->adapter = new JSNPowerAdminBarSupportBase($this->option, $this->params);
			if (is_file($builtInXmlFile))
				$this->adapter->parseXml($builtInXmlFile);
			elseif (is_file($xmlFile))
				$this->adapter->parseXml($xmlFile);
		}
	}
}

class JSNPowerAdminBarPreviewAdapter
{
	/**
	 * Contains name of the active component
	 * @var string
	 */
	protected $option;
	
	/**
	 * All parameters on QueryString
	 * @var array
	 */
	protected $params;
	
	/**
	 * Constructor of the Content support class
	 *
	 * @param string $option
	 * @param array $params
	 */
	public function __construct ($option, $params)
	{
		$this->option  = $option;
		$this->params  = $params;
	}
	
	/**
	 * Retrieve preview link of current context
	 * @return string
	 */
	public function getPreviewLink()
	{
		return 'index.php';
	}
}




