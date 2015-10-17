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

include_once dirname(dirname(__FILE__)) . '/helpers/plugin.php';

JFormHelper::loadFieldClass('Textarea');

class JFormFieldExclude extends JFormFieldTextarea
{

        protected static $oParams = null;
        protected static $oParser = null;
        protected $bTextArea      = FALSE;

        /**
         * 
         * @return type
         */
        protected function getParams()
        {
                if (!self::$oParams)
                {
                        $plugin       = JchOptimizePluginHelper::getPlugin('system', 'jch_optimize');
                        $pluginParams = new JRegistry();
                        $pluginParams->loadString($plugin->params);

                        if (!defined('JCH_DEBUG'))
                        {
                                define('JCH_DEBUG', ($pluginParams->get('debug', 0) && JDEBUG));
                        }

                        self::$oParams = $pluginParams;
                }

                return self::$oParams;
        }

        /**
         * 
         * @return type
         */
        protected function getParser()
        {
                if (!self::$oParser)
                {
                        $sHtml   = $this->getHtml();
                        $oParams = $this->getParams();



                        self::$oParser = new JchOptimizeParser($oParams, $sHtml);
                }

                return self::$oParser;
        }

        /**
         * 
         * @return type
         */
        protected function getHTML()
        {
                static $sHtml = '';

                if ($sHtml == '')
                {
                        $oFileRetriever = JchOptimizeFileRetriever::getInstance($this->getParams());
                        $sHtml          = $oFileRetriever->getFileContents($this->getMenuUrl());

                        if (!trim($sHtml))
                        {
                                $GLOBALS['bTextArea'] = TRUE;

                                throw new Exception(JText::_('Failed fetching front end HTML. '));
                        }
                }

                return $sHtml;
        }

        /**
         * 
         * @return string
         */
        protected function getMenuUrl()
        {
                $oParams     = $this->getParams();
                $iMenuLinkId = $oParams->get('jchmenu');

                if (!$iMenuLinkId)
                {
                        require_once dirname(__FILE__) . '/jchmenuitem.php';
                        $iMenuLinkId = JFormFieldJchmenuitem::getHomePageLink();
                }

                $oMenu     = JFactory::getApplication()->getMenu('site');
                $oMenuItem = $oMenu->getItem($iMenuLinkId);

                $oUri = clone JUri::getInstance();

                $oUri->setPath(str_replace('administrator/', '', $oUri->getPath()));
                $oUri->setQuery(str_replace('index.php?', '', $oMenuItem->link) . '&Itemid=' . $oMenuItem->id . '&backend=1');

                $sMenuUrl = $oUri->toString();

                return $sMenuUrl;
        }

        /**
         * 
         * @return type
         */
        protected function getInput()
        {
                $oFileRetriever = JchOptimizeFileRetriever::getInstance($this->getParams());

                if ($GLOBALS['bTextArea'])
                {
                        return parent::getInput();
                }

                if (!$oFileRetriever->isUrlFOpenAllowed())
                {
                        $GLOBALS['bTextArea'] = TRUE;

                        JFactory::getApplication()->enqueueMessage(JText::_('Your host does not have cURL or allow_url_fopen enabled. '
                                        . 'Will use text areas instead of multiselect options for exclude settings'), 'notice');

                        return parent::getInput();
                }

                try
                {
                        $aOptions = $this->getFieldOptions();
                }
                catch (Exception $ex)
                {
                        JFactory::getApplication()->enqueueMessage($ex->getMessage(), 'warning');

                        return parent::getInput();
                }

                $sField = JHTML::_('select.genericlist', $aOptions, $this->name . '[]', 'class="inputbox" multiple="multiple" size="5"', 'id', 'name',
                                   $this->value, $this->id);

                return $sField;
        }

        /**
         * 
         * @param type $sType
         * @param type $sExclude
         * @return type
         */
        protected function getOptions($sType, $sExclude = 'files')
        {
                $oParams = $this->getParams();

                $oParams->set('javascript', '1');
                $oParams->set('css', '1');
                $oParams->set('excludeAllExtensions', '0');
                $oParams->set('pro_inlineScripts', '1');
                $oParams->set('pro_inlineStyle', '1');
                $oParams->set('pro_phpAndExternal', '1');

                $aLinks = $this->getLinks($oParams);

                $aOptions = array();

                if (!empty($aLinks[$sType]))
                {
                        foreach ($aLinks[$sType] as $aLink)
                        {
                                if (isset($aLink['url']) && $aLink['url'] != '')
                                {
                                        if ($sExclude == 'files')
                                        {
                                                $sFile = preg_replace('#[?\#].*$#', '', $aLink['url']);

                                                $aOptions[$sFile] = $this->prepareFileValues($sFile);
                                        }
                                        elseif ($sExclude == 'extensions')
                                        {
                                                $sExtension = $this->prepareExtensionValues($aLink['url']);

                                                if ($sExtension === FALSE)
                                                {
                                                        continue;
                                                }

                                                $aOptions[$sExtension] = $sExtension;
                                        }
                                }
                                elseif (isset($aLink['content']) && $aLink['content'] != '')
                                {
                                        if ($sExclude == 'scripts')
                                        {
                                                $sScript = preg_replace('#<!--.*?-->#s', '', $aLink['content']);
                                                $sScript = trim(JchOptimize\JSMinRegex::minify($sScript));

                                                $aOptions[addslashes($sScript)] = $this->prepareScriptValues($sScript);
                                        }
                                }
                        }
                }

                return array_unique($aOptions);
        }

        /**
         * 
         * @param type $sContent
         */
        protected function prepareScriptValues($sScript)
        {
                $sEps = '';

                if (strlen($sScript) > 52)
                {
                        $sScript = substr($sScript, 0, 52);
                        $sEps    = '...';
                        $sScript = $sScript . $sEps;
                }

                if (strlen($sScript) > 26)
                {
                        $sScript = str_replace($sScript[26], $sScript[26] . "\n", $sScript);
                }

                return $sScript;
        }

        /**
         * 
         * @param type $sUrl
         * @return type
         */
        protected function prepareFileValues($sFile)
        {
                $sEps = '';

                if (strlen($sFile) > 27)
                {
                        $sFile = substr($sFile, -27);
                        $sFile = preg_replace('#^.*?/#', '/', $sFile);
                        $sEps  = '...';
                }

                return $sEps . $sFile;
        }

        /**
         * 
         * @staticvar string $sUriBase
         * @staticvar string $sUriPath
         * @param type $sUrl
         * @return boolean
         */
        protected function prepareExtensionValues($sUrl)
        {
                static $sUriBase = '';
                static $sUriPath = '';

                $sUriBase = $sUriBase == '' ? str_replace('/administrator/', '', JUri::base()) : $sUriBase;
                $sUriPath = $sUriPath == '' ? str_replace('/administrator', '', JUri::base(TRUE)) : $sUriPath;

                $sExtension = preg_replace('#(?:' . preg_quote($sUriBase, '#') . '|' . preg_quote($sUriPath, '#') . ')/'
                        . '(?:components|modules|plugins|media)/([^/]+)/.*$#', '$1', $sUrl, 1, $iCnt);

                if (preg_match('#(?:system|jui|cms|editors)#', $sExtension) || $iCnt != 1)
                {
                        return FALSE;
                }


                return $sExtension;
        }

        /**
         * 
         * @param type $sAction
         * @return type
         */
        protected function getImages($sAction = 'exclude')
        {
                $oParams = $this->getParams();

                $oParams->set('csg_enable', '1');
                $oParams->set('css_minify', '0');
                $oParams->set('csg_exclude_images', '');
                $oParams->set('csg_include_images', '');

                $oLinkBuilder = new JchOptimizeLinkBuilder($oParams);

                $aLinks = $this->getLinks($oParams);

                $sId       = md5('getImages' . serialize($oParams->get('jchmenu')));
                $aArgs     = array($aLinks['css'], $oParams);
                $aFunction = array('JchOptimizeCombiner', 'getImages');

                $aImages  = $oLinkBuilder->loadCache($aFunction, $aArgs, $sId);
                $aOptions = array();

                if (!empty($aImages[$sAction][1]))
                {
                        foreach ($aImages[$sAction][1] as $sImage)
                        {
                                $aImage = explode('/', $sImage);
                                $sImage = array_pop($aImage);

                                $aOptions[$sImage] = $sImage;
                        }
                }

                return array_unique($aOptions);
        }

        /**
         * 
         * @param type $oParams
         * @return type
         */
        protected function getLinks($oParams)
        {
                $oLinkBuilder = new JchOptimizeLinkBuilder($oParams);
                $sId          = md5('getLinks' . serialize($oParams->get('jchmenu') . $oParams->get('pro_searchBody')));
                $aFunction    = array($this, 'processLinks');

                $aLinks = $oLinkBuilder->loadCache($aFunction, FALSE, $sId);

                return $aLinks;
        }

        /**
         * 
         * @return type
         */
        public function processLinks()
        {
                try
                {
                        $oParser = $this->getParser();
                        $aLinks  = $oParser->getReplacedFiles();
                }
                catch (Exception $ex)
                {
                        $GLOBALS['bTextArea'] = TRUE;

                        if ($this->getParams()->get('log', 0))
                        {
                                JchOptimizeLogger::log($ex->getMessage());
                        }

                        throw new Exception(JText::_('Failed fetching links for the multiselect exclude options. '
                                . 'Will render textareas instead.'));
                }

                return $aLinks;
        }

}
