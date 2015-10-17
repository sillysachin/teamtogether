<?php
/**
 * @copyright	Copyright (C) 2010 Michael Richey. All rights reserved.
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
jimport('joomla.version');

class JFormFieldJavascript extends JFormField
{
	protected $type = 'Javascript';
        protected function getLabel(){
            return '';
        }
	protected function getInput()
	{
                JHtml::_('behavior.framework',true);
                $version = new JVersion;
                $shortversion = explode('.',$version->getShortVersion());
                $options = array('version'=>$shortversion[0]);
                JFactory::getDocument()->addScriptDeclaration("\n".'window.plg_system_topofthepage_admin_config = '.json_encode($options).';'."\n");             
                JFactory::getDocument()->addScript(JURI::root(true).'/media/plg_system_topofthepage/admin.js');
		return;
	}
}
