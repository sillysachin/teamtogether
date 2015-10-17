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
 * 
 * 
 */
class JchOptimizeCssParser
{

        public $aUrl        = array();
        protected $sLnEnd   = '';
        protected $bBackend = FALSE;

        /**
         * 
         * @param type $sLnEnd
         * @param type $bBackend
         */
        public function __construct($sLnEnd, $bBackend)
        {
                $this->sLnEnd   = $sLnEnd;
                $this->bBackend = $bBackend;
        }

        /**
         * 
         * @param type $sContent
         * @return type
         */
        public function handleMediaQueries($sContent)
        {
                if ($this->bBackend)
                {
                        return $sContent;
                }

                $sCommentRegex = '#COMMENT_START.*?COMMENT_END#';

                if (isset($this->aUrl['media']) && ($this->aUrl['media'] != ''))
                {
                        $sMedia   = $this->aUrl['media'];
                        $sContent = preg_replace(array($sCommentRegex, '#DELIMITER#'), '', $sContent);
                        $sContent = preg_replace_callback("#@media ([^{]*+)#i", array($this, '_mediaFeaturesCB'), $sContent);
                        $sContent = preg_replace('#(@(?:-\w{1,3}-)?(?:media|font-face|page|keyframes|supports|document)[^{]*+({(?>[^{}]+|(?2))*}))#',
                                                 '}' . $this->sLnEnd . '$1' . $this->sLnEnd . '@media ' . $sMedia . ' {' . $this->sLnEnd, $sContent);

                        $sContent = '@media ' . $sMedia . ' {' . $this->sLnEnd . $sContent . $this->sLnEnd . ' }' . $this->sLnEnd;

                        $sContent = preg_replace('#@media[^{]*+{[^\S}]*+}#', '', $sContent);
                }

                return $sContent;
        }

        /**
         * 
         * @param type $aMatches
         * @return type
         */
        protected function _mediaFeaturesCB($aMatches)
        {
                return '@media ' . $this->combineMediaFeatures($this->aUrl['media'], $aMatches[1]);
        }

        /**
         * 
         * @param type $sMediaFeatures1
         * @param type $sMediaFeatures2
         * @return type
         */
        protected function combineMediaFeatures($sMediaFeatures1, $sMediaFeatures2)
        {
                $aMediaFeatures1 = preg_split('#\bor\b|,#i', $sMediaFeatures1);
                $aMediaFeatures2 = preg_split('#\bor\b|,#i', $sMediaFeatures2);

                $aMediaFeatures = array();

                foreach ($aMediaFeatures1 as $sMediaFeature1)
                {
                        $sMediaFeature1 = trim($sMediaFeature1);

                        foreach ($aMediaFeatures2 as $sMediaFeature2)
                        {
                                $sMediaFeature2 = trim($sMediaFeature2);

                                if ($sMediaFeature1 == $sMediaFeature2)
                                {
                                        $aMediaFeatures[] = $sMediaFeature1;
                                }
                                else
                                {
                                        $aMediaFeatures[] = $sMediaFeature1 . ' and ' . $sMediaFeature2;
                                }
                        }
                }

                return implode(', ', $aMediaFeatures);
        }

        /**
         * 
         * @param string $sContent
         * @param type $sAtRulesRegex
         * @param type $sUrl
         * @return string
         */
        public function removeAtRules($sContent, $sAtRulesRegex, $sUrl = array('url' => 'CSS'))
        {
                if (preg_match_all($sAtRulesRegex, $sContent, $aMatches) === FALSE)
                {
                        if ($this->params->get('log', 0))
                        {
                                JchOptimizeLogger::log(JText::_('Error parsing for at rules in ' . $sUrl['url']));
                        }

                        return $sContent;
                }

                foreach ($aMatches[0] as $key => $sAtRule)
                {
                        if (strpos($sAtRule, 'charset') !== FALSE)
                        {
                                unset($aMatches[0][$key]);
                        }
                }

                if (!empty($aMatches[0]))
                {
                        $sAtRules = implode($this->sLnEnd, $aMatches[0]);

                        $sContentReplaced = str_replace($aMatches[0], '', $sContent);

                        $sContent = $sAtRules . $sContentReplaced;
                }

                return $sContent;
        }

        /**
         * Converts url of background images in css files to absolute path
         * 
         * @param string $sContent
         * @return string
         */
        public function correctUrl($sContent)
        {
                $sCorrectedContent = preg_replace_callback('#url\([\'"]?([^\'"\)]+)[\'"]?\)#i', array(__CLASS__, '_correctUrlCB'), $sContent);

                if (is_null($sCorrectedContent))
                {
                        throw new Exception(JText::_('Failed correcting urls of background images'));
                }

                $sContent = $sCorrectedContent;

                return $sContent;
        }

        /**
         * Callback function to correct urls in aggregated css files
         *
         * @param array $aMatches Array of all matches
         * @return string         Correct url of images from aggregated css file
         */
        protected function _correctUrlCB($aMatches)
        {
                $sUriBase    = str_replace('/administrator', '', JUri::base(TRUE));
                $sImageUrl   = $aMatches[1];
                $sCssFileUrl = isset($this->aUrl['url']) ? $this->aUrl['url'] : '/';

                if (!preg_match('#^/|://|^data:#', $sImageUrl))
                {
                        $aCssUrlArray = explode('/', $sCssFileUrl);
                        array_pop($aCssUrlArray);
                        $sCssRootPath = implode('/', $aCssUrlArray) . '/';
                        $sImagePath   = $sCssRootPath . $sImageUrl;
                        $oUri         = JURI::getInstance($sImagePath);
                        $sUriPath     = preg_replace('#^' . preg_quote($sUriBase, '#') . '/#', '', $oUri->getPath());
                        $oUri->setPath($sUriBase . '/' . $sUriPath);
                        $sImageUrl    = $oUri->toString();
                }

                if (JchOptimizeHelper::isInternal($sCssFileUrl))
                {
                        if (JchOptimizeHelper::isInternal($sImageUrl))
                        {
                                $oUri      = JURI::getInstance($sImageUrl);
                                $sImageUrl = $oUri->toString(array('path', 'query', 'fragment'));
                        }
                }

                return 'url(' . $sImageUrl . ')';
        }

        /**
         * Sorts @import and @charset as according to w3C <http://www.w3.org/TR/CSS2/cascade.html> Section 6.3
         *
         * @param string $sCss       Combined css
         * @return string           CSS with @import and @charset properly sorted
         * @todo                     replace @imports with media queries
         */
        public function sortImports($sCss)
        {
                $sCssMediaImports = preg_replace_callback('#@media\s([^{]++)({(?>[^{}]++|(?2))*+})#i', array(__CLASS__, '_sortImportsCB'), $sCss);

                if (is_null($sCssMediaImports))
                {
                        if ($this->params->get('log', 0))
                        {
                                JchOptimizeLogger::log('Failed matching for imports within media queries in css');
                        }

                        return $sCss;
                }

                $sCss = $sCssMediaImports;

                $sCss = $this->removeAtRules($sCss, '#@(?:import|charset)[^;}]++(?:;|.(?=\}))#i');

                return $sCss;
        }

        /**
         * Callback function for sort Imports
         * 
         * @param type $aMatches
         * @return string
         */
        protected function _sortImportsCB($aMatches)
        {
                $sMedia = $aMatches[1];

                $sImports = preg_replace_callback('#(@import\surl\([^)]++\))([^;}]*+);?#',
                                                  function($aM) use ($sMedia)
                {
                        if (!empty($aM[2]))
                        {
                                return $aM[1] . ' ' . $this->combineMediaFeatures($sMedia, $aM[2]) . ';';
                        }
                        else
                        {
                                return $aM[1] . ' ' . $sMedia . ';';
                        }
                }, $aMatches[2]);

                $sCss = str_replace($aMatches[2], $sImports, $aMatches[0]);

                return $sCss;
        }

        
}
