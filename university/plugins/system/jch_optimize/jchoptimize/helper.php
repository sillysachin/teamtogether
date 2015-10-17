<?php

use JchOptimize\JSMinRegex;

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
defined('_JEXEC') or die('Restricted access');

/**
 * Some helper functions
 * 
 */
class JchOptimizeHelper
{

        /**
         * Checks if file (can be external) exists
         * 
         * @param type $sPath
         * @return boolean
         */
        public static function fileExists($sPath)
        {
                //global $_PROFILER;
                //JCH_DEBUG ? $_PROFILER->mark('beforeFileExists - ' . $sPath . ' plgSystem (JCH Optimize)') : null;
                
                $bExists = (file_exists($sPath) || @fopen($sPath, "r") != FALSE);
                
                //JCH_DEBUG ? $_PROFILER->mark('afterFileExists - ' . $sPath . ' plgSystem (JCH Optimize)') : null;
                
                return $bExists;
        }

        /**
         * Get local path of file from the url if internal
         * If external or php file, the url is returned
         *
         * @param string  $sUrl  Url of file
         * @return string       File path
         */
        public static function getFilePath($sUrl)
        {
               // global $_PROFILER;
                //JCH_DEBUG ? $_PROFILER->mark('beforeGetFilePath - ' . $sUrl . ' plgSystem (JCH Optimize)') : null;
                
                $sUriBase = str_replace('/administrator/', '', JUri::base());
                $sUriPath = str_replace('/administrator', '', JUri::base(TRUE));

                $oUri = clone JUri::getInstance($sUriBase);

                if (JchOptimizeHelper::isInternal($sUrl) && !preg_match('#\.php#i', $sUrl))
                {
                        $sUrl = preg_replace(
                                array(
                                '#^' . preg_quote($sUriBase, '#') . '#',
                                '#^' . preg_quote($sUriPath, '#') . '/#',
                                '#\?.*?$#'
                                ), '', $sUrl);
                        
                        //JCH_DEBUG ? $_PROFILER->mark('afterGetFilePath - ' . $sUrl . ' plgSystem (JCH Optimize)') : null;

                        return JPATH_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $sUrl);
                }
                else
                {
                        switch (TRUE)
                        {
                                case preg_match('#://#', $sUrl):

                                        break;

                                case (substr($sUrl, 0, 2) == '//'):

                                        $sUrl = $oUri->toString(array('scheme')) . substr($sUrl, 2);
                                        break;

                                case (substr($sUrl, 0, 1) == '/'):

                                        $sUrl = $oUri->toString(array('scheme', 'host')) . $sUrl;
                                        break;

                                default:

                                        $sUrl = $sUriBase . $sUrl;
                                        break;
                        }
                        
                        
                        //JCH_DEBUG ? $_PROFILER->mark('afterGetFilePath - ' . $sUrl . ' plgSystem (JCH Optimize)') : null;

                        return html_entity_decode($sUrl);
                }
        }

        /**
         * Gets the name of the current Editor
         * 
         * @staticvar string $sEditor
         * @return string
         */
        public static function getEditorName()
        {
                static $sEditor;

                if (!isset($sEditor))
                {
                        $sEditor = JFactory::getUser()->getParam('editor');
                        $sEditor = !isset($sEditor) ? JFactory::getConfig()->get('editor') : $sEditor;
                }

                return $sEditor;
        }

        /**
         * Determines if file is internal
         * 
         * @param string $sUrl  Url of file
         * @return boolean
         */
        public static function isInternal($sUrl)
        {
                $oUrl = JUri::getInstance($sUrl);
                //trying to resolve bug in php with parse_url before 5.4.7
                if (preg_match('#^//([^/]+)(/.*)$#i', $oUrl->getPath(), $aMatches))
                {
                        if (!empty($aMatches))
                        {
                                $oUrl->setHost($aMatches[1]);
                                $oUrl->setPath($aMatches[2]);
                        }
                }
                
                $sUrlBase = $oUrl->toString(array('scheme', 'host', 'port', 'path'));
		$sUrlHost = $oUrl->toString(array('scheme', 'host', 'port'));

                $sBase = str_replace('administrator/', '', JUri::base());
                
		if (stripos($sUrlBase, $sBase) !== 0 && !empty($sUrlHost))
		{
			return FALSE;
		}

		return TRUE;
        }

        /**
         * 
         * @staticvar string $sContents
         * @return boolean
         */
        public static function modRewriteEnabled()
        {
               
                if (function_exists('apache_get_modules'))
                {
                        return (in_array('mod_rewrite', apache_get_modules()));
                }
                elseif (!ini_get('open_basedir') && function_exists('shell_exec') && file_exists('/usr/local/apache/bin/apachectl'))
                {
                        return (strpos(shell_exec('/usr/local/apache/bin/apachectl -l'), 'mod_rewrite') !== false);
                }
                else
                {
                        static $sContents = '';

                        if ($sContents == '')
                        {
                                $oFileRetriever = JchOptimizeFileRetriever::getInstance($GLOBALS['oParams']);
                                
                                if (!$oFileRetriever->isUrlFOpenAllowed())
                                {
                                        return FALSE;
                                }

                                $sJbase         = JUri::base(true);
                                $sBaseFolder = $sJbase == '/' ? $sJbase : $sJbase . '/';
                                
                                $sUrl        = JUri::base() . 'plugins/system/jch_optimize/assets' . $sBaseFolder . 'test_mod_rewrite';

                                $sContents = $oFileRetriever->getFileContents($sUrl);
                        }

                        if ($sContents == 'TRUE')
                        {
                                return TRUE;
                        }
                        else
                        {
                                return FALSE;
                        }
                }
        }

        /**
         * 
         * @param type $aArray
         * @param type $sString
         * @return boolean
         */
        public static function findExcludes($aArray, $sString, $bScript=FALSE)
        {
                foreach ($aArray as $sValue)
                {
                        if($bScript)
                        {
                                $sString = JSMinRegex::minify($sString);
                        }

                        if ($sValue && strpos($sString, $sValue) !== FALSE)
                        {
                                return TRUE;
                        }
                }

                return FALSE;
        }

}
