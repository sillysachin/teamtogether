<?php

/**
 * JCH Optimize - Joomla! plugin to aggregate and minify external resources for
 * optmized downloads
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die;

include_once dirname(dirname(__FILE__)) . '/jchoptimize/loader.php';

if (version_compare(JVERSION, '3.0', '>'))
{

        class JFormFieldJchmenuitemcompat extends JFormFieldMenuitem
        {

                public function setup(SimpleXMLElement $element, $value, $group = NULL)
                {
                        $sValue = $this->setupJchMenuItem($element, $value, $group);
                        $oFileRetriever = JchOptimizeFileRetriever::getInstance();
                        
                        if(!$oFileRetriever->isUrlFOpenAllowed())
                        {
                                return FALSE;
                        }
                        
                        return parent::setup($element, $sValue, $group);
                }

        }

}
else
{
        JFormHelper::loadFieldClass('MenuItem');

        class JFormFieldJchmenuitemcompat extends JFormFieldMenuitem
        {

                public function setup(&$element, $value, $group = NULL)
                {
                        $sValue = $this->setupJchMenuItem($element, $value, $group);
                        $oFileRetriever = JchOptimizeFileRetriever::getInstance();
                        
                        if(!$oFileRetriever->isUrlFOpenAllowed())
                        {
                                return FALSE;
                        }
                        
                        if (version_compare(PHP_VERSION, '5.3.0', '>=') )
                        {
                                return parent::setup($element, $sValue, $group);
                        }
                        else
                        {

                                JFactory::getApplication()->enqueueMessage(JText::_('This plugin requires PHP 5.3.0 or greater to run. '
                                                . 'Your installed version is ' . PHP_VERSION), 'error');

                                JFormHelper::loadFieldClass('Textarea');

                                return FALSE;
                        }
                }

        }

}

/**
 * 
 */
class JFormFieldJchmenuitem extends JFormFieldJchmenuitemcompat
{

        public $type = 'jchmenuitem';

        /**
         * 
         * @param type $element
         * @param type $value
         * @param type $group
         * @return type
         */
        public function setupJchMenuItem($element, $value, $group = null)
        {
                $GLOBALS['bTextArea'] = FALSE;

                $this->loadResources();

                if (!$value)
                {
                        $value = $this->getHomePageLink();
                }

                return $value;
        }

        /**
         * 
         * @return type
         */
        public static function getHomePageLink()
        {
                $oMenu            = JFactory::getApplication()->getMenu('site');
                $oDefaultMenuItem = $oMenu->getDefault();

                return $oDefaultMenuItem->id;
        }

        /**
         * 
         */
        protected function loadResources()
        {
                
        }

}
