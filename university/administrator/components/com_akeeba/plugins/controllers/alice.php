<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2014 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/**
 * The Control Panel controller class
 *
 */
class AkeebaControllerAlices extends FOFController
{
	public function  __construct($config = array())
	{
		parent::__construct($config);

		$base_path  = JPATH_COMPONENT_ADMINISTRATOR.'/plugins';
		$model_path = $base_path.'/models';
		$view_path  = $base_path.'/views';

		$this->addModelPath($model_path);
		$this->addViewPath($view_path);
	}

	public function ajax()
	{
		$model = $this->getThisModel();

		$model->setState('ajax', $this->input->get('ajax', '', 'cmd'));
		$model->setState('log', $this->input->get('log', '', 'cmd'));

		$ret_array = $model->runAnalysis();

		@ob_end_clean();
		header('Content-type: text/plain');
		echo '###' . json_encode($ret_array) . '###';
		flush();
		JFactory::getApplication()->close();
	}

	public function domains()
	{
		$return  = array();
		$domains = AliceUtilScripting::getDomainChain();

		foreach($domains as $domain)
		{
			$return[] = array($domain['domain'], $domain['name']);
		}

		@ob_end_clean();
		header('Content-type: text/plain');
		echo '###'.json_encode($return).'###';
		flush();
		JFactory::getApplication()->close();
	}
}