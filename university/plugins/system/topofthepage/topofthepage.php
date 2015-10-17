<?php
/**
 * @version		$Id: topofthepage.php 20196 2011-03-04 02:40:25Z mrichey $
 * @package		plg_sys_topofthepage
 * @copyright	Copyright (C) 2005 - 2011 Michael Richey. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemTopofthepage extends JPlugin
{
        var $_initialized = false;

//        function onAfterRoute()
        function onBeforeCompileHead()
	{
                if($this->_initialized) return true;
                $app = JFactory::getApplication();
                $doc = JFactory::getDocument();
                
                // reasons to exit
                if(
                    ($app->isAdmin() && !$this->params->get('runinadmin',0)) || // do we run in administrator ?
                    $doc->getType() != 'html' || // we don't run in pages that aren't html
                    in_array(JRequest::getString('tmpl'),array('component','raw')) // we don't run in modal pages or other incomplete pages
                ) return true;
                                
                // sweet - it's on!        
                $this->_initialized = true;
                $framework = $this->params->get('jsframework','mootools');
                $this->_loadJS($framework);                
                
                $doc->addScriptDeclaration('window.plg_system_topofthepage_options = '.$this->_loadJSOptions($framework).';'."\n");
                if($this->params->get('usestyle',1)==1) $doc->addStyleDeclaration($this->params->get('linkstyle'));
		return true;
	}
        private function _loadJS($framework = 'mootools') {
            $app = JFactory::getApplication();
            $doc = JFactory::getDocument();
            $debug = $app->getCfg('debug',false);
            
            $loadframework = $this->params->get('loadjsframework',1);
            
            switch($framework) {
                case 'jquery':
                    if($loadframework && version_compare(JVERSION,3,'>=')) JHtml::_('jquery.framework', true, true);
                    $doc->addScript(JURI::root(true).'/media/plg_system_topofthepage/jquery.easing'.($debug?'':'.min').'.js');
                    $doc->addScript(JURI::root(true).'/media/plg_system_topofthepage/jqtopofthepage'.($debug?'':'.min').'.js');
                    break;
                default:
                    if($loadframework) JHtml::_('behavior.framework',true);
                    $doc->addScript(JURI::root(true).'/media/plg_system_topofthepage/ScrollSpy'.($debug?'':'.min').'.js');
                    $doc->addScript(JURI::root(true).'/media/plg_system_topofthepage/topofthepage'.($debug?'':'.min').'.js');
                    break;
            }
        }
        private function _loadJSOptions($framework = 'mootools') {
            $options = array(
                'spyposition'=>200,
                'visibleopacity'=>100, // alter javascript
                'displaydur'=>500,
                'slidein'=>0,
                'slideindir'=>'bottom',
                'zindex'=>0,
            );
            foreach($options as $option=>$default) $options[$option]=$this->params->get($option,$default);
            
            $options['topalways']=($this->params->get('topalways',0))?true:false;
            $options['icon']=strlen(trim($this->params->get('icon',false)))?trim($this->params->get('icon',false)):false;

            if(!$this->params->get('omittext',0)) {
                JFactory::getLanguage()->load('plg_system_topofthepage',JPATH_ADMINISTRATOR);
                $options['buttontext']=JText::_('PLG_SYS_TOPOFTHEPAGE_GOTOTOP');
            } else {
                $options['buttontext']=false;
            }
            
            $linklocation = explode('_',$this->params->get('linklocation','bottom_right'));
            $options['styles']=array('position'=>'fixed');
            
            if($framework === 'mootools') {
                $options['styles']['opacity']=0;
                $options['styles']['display']='block';
            } else {
                $options['styles']['display']='none';
                $options['styles']['opacity']=$options['opacity']/100;
                $options['styles']['filter']='alpha(opacity='.$options['opacity'].')';
            }
            switch($linklocation[0]) {
                case 'top':
                    $options['styles']['top']='0px';
                    break;
                default:
                    $options['styles']['bottom']='0px';
                    break;
            }
            switch($linklocation[1]) {
                case 'left':
                    $options['styles']['left']='0px';
                    break;
                case 'center':
                    $options['styles']['left']='center';
                    break;
                default:
                    $options['styles']['right']='0px';
                    break;
            }
            
            $options['smoothscroll']['duration']=$this->params->get('smoothscrollduration',500);
            $options['smoothscroll']['transition']=$this->params->get('smoothscrolltransition','linear');
            switch($framework) {
                case 'jquery':
                    if($options['smoothscroll']['transition']=='Pow') $options['smoothscroll']['transition']='linear';
                    break;
                default:
                    if($options['smoothscroll']['transition']=='swing') $options['smoothscroll']['transition']='linear';
                    break;
            }
            if($options['smoothscroll']['transition'] != 'linear') {
                switch($framework) {
                    case 'jquery':
                        if($options['smoothscroll']['transition']!='swing')
                        $options['smoothscroll']['transition']=$this->params->get('smoothscrolleasing','easeInOut').$this->params->get('smoothscrolltransition','linear');
                        break;
                    default:
                        $easingtable=array('easeInOut'=>'in:out','easeIn'=>'in','easeOut'=>'out');
                        $options['smoothscroll']['transition']=strtolower($this->params->get('smoothscrolltransition','linear')).':'.$easingtable[$this->params->get('smoothscrolleasing','easeInOut')];
                        break;
                }
            }
            return json_encode($options);
        }
}