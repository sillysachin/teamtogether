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

class JchOptimizePluginHelper
{
        protected static $plugins = null;
        protected static $iPluginId = null;

        public static function getPluginId($sPlugin)
        {
                if (self::$iPluginId !== null)
                {
                        return self::$iPluginId;
                }
                
                $oDb    = JFactory::getDbo();
                $sQuery = $oDb->getQuery(TRUE)
                        ->select($oDb->qn('extension_id'))
                        ->from($oDb->qn('#__extensions'))
                        ->where($oDb->qn('name') . ' = ' . $oDb->q($sPlugin));
                
                self::$iPluginId = $oDb->setQuery($sQuery)->loadResult();

                return self::$iPluginId ;
        }

        public static function getPlugin($type, $plugin = null)
        {
                $result  = array();
                $plugins = static::load();

                // Find the correct plugin(s) to return.
                if (!$plugin)
                {
                        foreach ($plugins as $p)
                        {
                                // Is this the right plugin?
                                if ($p->type == $type)
                                {
                                        $result[] = $p;
                                }
                        }
                }
                else
                {
                        foreach ($plugins as $p)
                        {
                                // Is this plugin in the right group?
                                if ($p->type == $type && $p->name == $plugin)
                                {
                                        $result = $p;
                                        break;
                                }
                        }
                }

                return $result;
        }

        protected static function load()
        {
                if (self::$plugins !== null)
                {
                        return self::$plugins;
                }

                $user  = JFactory::getUser();
                
                //joomla bug with plugin cache
                //$cache = JFactory::getCache('com_plugins', '');

                $levels = implode(',', $user->getAuthorisedViewLevels());

               // if (!self::$plugins = $cache->get($levels))
                //{
                        $db    = JFactory::getDbo();
                        $query = $db->getQuery(true)
                                ->select('folder AS type, element AS name, params')
                                ->from('#__extensions')
                                ->where('type =' . $db->quote('plugin'))
                                ->where('state >= 0')
                                ->where('access IN (' . $levels . ')')
                                ->order('ordering');

                        self::$plugins = $db->setQuery($query)->loadObjectList();

                       // $cache->store(self::$plugins, $levels);
                //}

                return self::$plugins;
        }

}
