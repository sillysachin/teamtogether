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
 * Class to parse HTML and find css and js links to replace, populating an array with matches
 * and removing found links from HTML
 * 
 */
class JchOptimizeParser extends JchOptimizeBase
{

        /** @var string   Html of page */
        public $sHtml = '';

        /** @var array    Array of css or js urls taken from head */
        protected $aLinks = array();

        /** @var array    Array of arguments to be used in callback functions */
        public $aCallbackArgs = array();
        public $params        = null;

        /**
         * Constructor
         * 
         * @param JRegistry object $params      Plugin parameters
         * @param string  $sHtml                Page HMTL
         */
        public function __construct($oParams, $sHtml)
        {
                $this->params = $oParams;
                $this->sHtml  = $sHtml;

                $this->parseHtml();
        }

        /**
         * Removes applicable js and css links from search area
         * 
         */
        public function parseHtml()
        {
                global $_PROFILER;
                //JCH_DEBUG ? $_PROFILER->mark('beforeParseHtml plgSystem (JCH Optimize)') : null;

                $oParams = $this->params;

                $this->sSearchArea = $this->getSearchArea();

                $aExExtensions = array();

                if ($oParams->get('excludeAllExtensions', '1'))
                {

                        $aExExtensions = array('components/', 'modules/', 'plugins/', 'media/(?!system/|jui/|cms/|editors/)');
                }

                $aExJsComp  = $this->getExCompRegex($oParams->get('excludeJsComponents', ''));
                $aExCssComp = $this->getExCompRegex($oParams->get('excludeCssComponents', ''));

                $this->aCallbackArgs['counter'] = 0;
                $this->excludeIf();

                if ($oParams->get('javascript', 1))
                {
                        $sType = 'js';

                        $this->aCallbackArgs['type'] = $sType;

                        $this->aCallbackArgs['excludes']['js']     = JchOptimize::getArray($oParams->get('excludeJs', ''));
                        $this->aCallbackArgs['excludes']['script'] = JchOptimize::getArray($oParams->get('pro_excludeScripts'));

                        $sExExtensionsRegex = $this->getExExtensionsRegex($aExExtensions, $aExJsComp);

                        $aJsRegex = $this->getJsRegex();

                        $sJsRegex = $aJsRegex['start'] . $sExExtensionsRegex . $aJsRegex['end'];

                        JCH_DEBUG ? $_PROFILER->mark('beforePregReplaceJs plgSystem (JCH Optimize)') : null;

                        $sProcessedHtml = preg_replace_callback($sJsRegex, array(__CLASS__, 'replaceScripts'), $this->sSearchArea);

                        JCH_DEBUG ? $_PROFILER->mark('afterPregReplaceJs plgSystem (JCH Optimize)') : null;

                        if (is_null($sProcessedHtml))
                        {
                                throw new Exception(JText::_('Error while parsing for javascript..turn off combine javascript option'));
                        }

                        $this->sSearchArea = $sProcessedHtml;
                }

                if ($this->enableCssCompression())
                {
                        $sType                                  = 'css';
                        $this->aCallbackArgs['type']            = $sType;
                        $this->aCallbackArgs['excludes']['css'] = JchOptimize::getArray($oParams->get('excludeCss', ''));

                        $sExExtensionsRegex = $this->getExExtensionsRegex($aExExtensions, $aExCssComp);

                        $aCssRegex = $this->getCssRegex();

                        $sCssRegex = $aCssRegex['start'] . $sExExtensionsRegex . $aCssRegex['end'];

                        JCH_DEBUG ? $_PROFILER->mark('beforePregReplaceCss plgSystem (JCH Optimize)') : null;

                        $sProcessedHtml = preg_replace_callback($sCssRegex, array(__CLASS__, 'replaceScripts'), $this->sSearchArea);

                        JCH_DEBUG ? $_PROFILER->mark('afterPregReplaceCss plgSystem (JCH Optimize)') : null;

                        if (is_null($sProcessedHtml))
                        {
                                throw new Exception(JText::_('Error while parsing for CSS links..turn off combine CSS option'));
                        }

                        $this->sSearchArea = $sProcessedHtml;
                }

                //JCH_DEBUG ? $_PROFILER->mark('afterParseHtml plgSystem (JCH Optimize)') : null;
        }

        /**
         * 
         * @return type
         */
        protected function enableCssCompression()
        {
                jimport('joomla.environment.browser');
                $oBrowser = JBrowser::getInstance();

                if (($oBrowser->getBrowser() == 'msie') && ($oBrowser->getMajor() <= '9'))
                {
                        return FALSE;
                }
                else
                {
                        return ($this->params->get('css', 1) || $this->params->get('csg_enable', 0));
                }
        }

        /**
         * 
         * @param type $aArray1
         * @param type $aArray2
         * @return string
         */
        protected function getExExtensionsRegex($aArray1, $aArray2)
        {
                $sExExtensionsRegex = '';
                $aExExtensions      = array_merge($aArray1, $aArray2);

                if (!empty($aExExtensions))
                {
                        $sExExtensionsRegex .= '(?=  [^/>]*+ (?:/ (?! ' . implode('|', $aExExtensions) . ' )  [^/>]*+)*? >)';
                }

                return $sExExtensionsRegex;
        }

        /**
         * Callback function used to remove urls of css and js files in head tags
         *
         * @param array $aMatches       Array of all matches
         * @return string               Returns the url if excluded, empty string otherwise
         */
        protected function replaceScripts($aMatches)
        {

                if ((!isset($aMatches[1]) || trim($aMatches[1]) == '') && (!isset($aMatches[3]) || trim($aMatches[3]) == ''))
                {
                        return $aMatches[0];
                }

                $sType   = $this->aCallbackArgs['type'];
                $sEditor = JchOptimizeHelper::getEditorName();

                $sUrl         = $aMatches[1];
                $sFile        = isset($aMatches[2]) ? $aMatches[2] : '';
                $sDeclaration = isset($aMatches[3]) ? $aMatches[3] : '';
                $aExcludes    = array();
                $sPath        = '';

                //global $_PROFILER;

                if (isset($this->aCallbackArgs['excludes']))
                {
                        $aExcludes = $this->aCallbackArgs['excludes'];
                }

                $aExcludes['script'] = array_map(function($sScript)
                {
                        return stripslashes($sScript);
                }, $aExcludes['script']);
                $aExcludes['script'] = array_merge($aExcludes['script'], array('document.write', 'var mapconfig90'));
                $aExcludes['js']     = array_merge($aExcludes['js'],
                                                   array('.com/maps/api/js', '.com/jsapi', '.com/uds', 'plugin_googlemap3', '.com/recaptcha/api'));

                if ($sUrl != '')
                {
                        $sPath .= JchOptimizeHelper::getFilePath($sUrl);
                }

                $sMedia = '';

                if (($sType == 'css') && (preg_match('#media=(?(?=["\'])(?:["\']([^"\']+))|(\w+))#i', $aMatches[0], $aMediaTypes) > 0))
                {
                        $sMedia .= $aMediaTypes[1] ? $aMediaTypes[1] : $aMediaTypes[2];
                }
                //JCH_DEBUG ? $_PROFILER->mark('beforeReplaceScript - ' . $sFile . ' plgSystem (JCH Optimize)') : null;

                switch (TRUE)
                {
                        case (isset($aExcludes['if']) && in_array($aMatches[0], $aExcludes['if'])):
                        case (($sUrl != '') && !empty($aExcludes[$sType]) && JchOptimizeHelper::findExcludes($aExcludes[$sType], $sUrl)):
                        case ($sEditor == 'artofeditor' && $sFile == 'ckeditor.js'):
                        case (($sType == 'js') && ($sUrl != '') && $this->isEditorsExcluded($sUrl)):
                        case (($sUrl != '') && $this->isUrlFopenAllowed($sUrl)):
                        case ($sUrl != '' && preg_match('#^https#', $sUrl) && !extension_loaded('openssl')):
                        case ($sUrl != '' && preg_match('#^data:#', $sUrl)):
                        case ($sDeclaration != '' && JchOptimizeHelper::findExcludes($aExcludes['script'], $sDeclaration, TRUE)):

                                //JCH_DEBUG ? $_PROFILER->mark('afterReplaceScript - ' . $sFile . ' plgSystem (JCH Optimize)') : null;

                                return $aMatches[0];

                        case ($sUrl == '' && trim($sDeclaration) != ''):
                                $content                = str_replace(array('<!--', '-->'), '', $sDeclaration);
                                $this->aLinks[$sType][] = array('content' => $content, 'match' => $aMatches[0]);

                                //JCH_DEBUG ? $_PROFILER->mark('afterReplaceScript - ' . $sFile . ' plgSystem (JCH Optimize)') : null;

                                return '';

                        case ($this->aCallbackArgs['type'] == 'js'):
                                $this->aCallbackArgs['counter'] ++;
                                $this->aLinks[$sType][] = array(
                                        'url'   => $sUrl,
                                        'file'  => $sFile,
                                        'match' => $aMatches[0],
                                        'path'  => $sPath);

                                //JCH_DEBUG ? $_PROFILER->mark('afterReplaceScript - ' . $sFile . ' plgSystem (JCH Optimize)') : null;

                                return '<JCH_SCRIPT>';

                        case ($this->aCallbackArgs['type'] == 'css'):
                                $this->aLinks[$sType][] = array(
                                        'url'   => $sUrl,
                                        'file'  => $sFile,
                                        'media' => $sMedia,
                                        'match' => $aMatches[0],
                                        'path'  => $sPath);

                                //JCH_DEBUG ? $_PROFILER->mark('afterReplaceScript - ' . $sFile . ' plgSystem (JCH Optimize)') : null;

                                return '';
                        default:

                                JchOptimizeLogger::log(JText::_('Unknown match type'));

                                return $aMatches[0];
                }
        }

        /**
         * Generates regex for excluding components set in plugin params
         * 
         * @param string $param
         * @return string
         */
        protected function getExCompRegex($sExComParam)
        {
                $aComponents = array_filter(JchOptimize::getArray($sExComParam));
                $aExComp     = array();

                if (!empty($aComponents))
                {
                        $aExComp = array_map(function($sValue)
                        {
                                return $sValue . '/';
                        }, $aComponents);
                }

                return $aExComp;
        }

        /**
         * Add js and css urls in conditional tags to excludes list
         *
         * @param string $sType   css or js
         */
        protected function excludeIf()
        {
                //global $_PROFILER;
                //JCH_DEBUG ? $_PROFILER->mark('beforeExcludeIf plgSystem (JCH Optimize)') : null;

                if (preg_match_all('#<\!--.*?-->#is', $this->sSearchArea, $aMatches))
                {
                        foreach ($aMatches[0] as $sMatch)
                        {
                                if (preg_match_all('#<link[^>]*>|<(?:style|script)[^>]*>.*?</(?:script|style)>#is', $sMatch, $aExcludesMatches))
                                {
                                        foreach ($aExcludesMatches[0] as $sExcludesMatch)
                                        {
                                                $this->aCallbackArgs['excludes']['if'][] = @$sExcludesMatch;
                                        }
                                }
                        }
                }

                //JCH_DEBUG ? $_PROFILER->mark('afterExcludeIf plgSystem (JCH Optimize)') : null;
        }

        /**
         * Fetches Class property containing array of matches of urls to be removed from HTML
         * 
         * @return array
         */
        public function getReplacedFiles()
        {
                return $this->aLinks;
        }

        /**
         * Set the Searcharea property
         * 
         * @param type $sSearchArea
         */
        public function setSearchArea($sSearchArea)
        {
                $this->sSearchArea = $sSearchArea;
        }

        
}
