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
defined('_JEXEC') or die('Restricted access');

/**
 * Autoloader for plugin classes
 * 
 * @param type $class
 * @return boolean
 */
function loadJchOptimizeClass($sClass)
{
        $class = $sClass;
        // If the class already exists do nothing.
        if (class_exists($class, false))
        {
                return true;
        }

        if (substr($class, 0, 11) != 'JchOptimize')
        {
                return false;
        }
        else
        {
                $class = str_replace('JchOptimize', '', $class);
        }

        if (strpos($class, '\\') === 0)
        {
                $class = substr($class, 1);
                $file  = dirname(dirname(__FILE__)) . '/libs/' . $class . '.php';
        }
        else
        {
                $class = strtolower(($class == '') ? 'jchoptimize' : $class);
                $file  = dirname(__FILE__) . '/' . $class . '.php';
        }

        if (!file_exists($file))
        {
                throw new Exception(JText::_('File not found: ' . $file));
        }
        else
        {
                include_once $file;
                
                if(!class_exists($sClass))
                {
                        throw new Exception(JText::_('Class not found: ' . $sClass));
                }        
        }
}

spl_autoload_register('loadJchOptimizeClass');


