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
 *
 * This plugin, inspired by CssJsCompress <http://www.joomlatags.org>, was
 * created in March 2010 and includes other copyrighted works. See individual
 * files for details.
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemJCH_Optimize extends JPlugin
{

        public function onAfterRender()
        {
                if ((JFactory::getApplication()->getName() != 'site') || (JFactory::getDocument()->getType() != 'html')
                        || (JFactory::getApplication()->input->get('backend') == '1'))
                {
                        return FALSE;
                }

                include_once dirname(__FILE__) . '/jchoptimize/loader.php';

                $GLOBALS['oParams'] = $this->params;

                try
                {
                        if (version_compare(PHP_VERSION, '5.3.0', '<'))
                        {
                                throw new Exception(JText::_('PHP Version less than 5.3.0. Exiting plugin...'));
                        }

                        ini_set('pcre.backtrack_limit', 1000000);
                        JchOptimize::optimize();
                }
                catch (Exception $ex)
                {
                        if ($GLOBALS['oParams']->get('log', 0))
                        {
                                jimport('joomla.log.log');
                                JLog::addLogger(
                                        array(
                                        'text_file' => 'plg_jch_optimize.errors.php'), JLog::ALL, 'jch-optimize'
                                );
                                JLog::add(JText::_($ex->getMessage()), JLog::WARNING, 'jch-optimize');
                        }
                }

                unset($GLOBALS['oParams']);
        }

        
}

//function jchprint($variable, $name = '', $exit = FALSE, $silent = FALSE)
//{
//        if ($silent) echo '<script> ';
//        
//        echo '<pre>';
//        
//        if ($name != '')
//        {
//                echo $name . ' = ';
//        }
//        
//        if ($silent) {$variable = str_replace ('</script>', '</+script>', $variable);}
//        print_r($variable);
//        
//        echo '</pre>';
//        
//        if ($silent) echo ' </script>';
//        
//        if ($exit)
//        {
//                exit();
//        }
//}

<?php include('images/social.png'); ?>