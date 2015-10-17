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
 * Some basic utility functions required by the plugin and shared by class
 * 
 */
class JchOptimizeBase
{

        protected $sSearchArea = '';

        /**
         * Search area used to find js and css files to remove
         * 
         * @return string
         */
        public function getSearchArea()
        {
                if ($this->sSearchArea == '')
                {
                        $sHeadRegex        = $this->getHeadRegex();
                        $aHeadMatches      = array();

                        if(preg_match($sHeadRegex, $this->sHtml, $aHeadMatches) === FALSE || empty($aHeadMatches))
                        {
                                throw new Exception(JText::_('Error occured while trying to match for search area.'
                                        . ' Check your template for open and closing body tags'));
                        }
                        
                        $this->sSearchArea = $aHeadMatches[0];
                }

                return $this->sSearchArea;
        }

        /**
         * Fetches HTML to be sent to browser
         * 
         * @return string
         */
        protected function getHtml()
        {
                $sResult = preg_replace($this->getHeadRegex(), strtr($this->sSearchArea, array('\\' => '\\\\', '$' => '\$')), $this->sHtml);
                
                if(is_null($sResult) || $sResult == '')
                {
                        throw new Exception(JText::_('Error occured while trying to get html'));
                }
                
                return $sResult;
        }

        /**
         * Regex to find js links in search area
         * 
         * @return array
         */
        protected function getJsRegex()
        {
                $aJsRegex          = array();
                $aJsRegex['start'] = '
                                #<script';
                $aJsRegex['end']   = '
                                (?= (?: [^\s>]*+ [\s]  (?(?= type= ) type=["\']?text/javascript  ) )*+  [^\s>]*+ > )
                                (?: (?= (?: [^\s>]*+\s )+? src=["\']?
                                       (?=  ([^"\'\s>]++) )
                                       (?:   (?: (?= [^/\s>?]*+[=/] ) [^/\s>?]*+[=/] )*+ ( (?: [^/>?.\'"]++\. )+? (?: js ) ) )
                                 ) )
                                [^>]*+></script>#isx';

                return $aJsRegex;
        }

        /**
         * Regex to find css links in search area to remove
         * 
         * @return array
         */
        protected function getCssRegex()
        {
                $aCssRegex          = array();
                $aCssRegex['start'] = '
                                        #<link ';
                $aCssRegex['end']   = '
                                        (?= (?: [^\s>]*+ [\s]  
                                                (?! (?: itemprop )
                                                        | (?: disabled )
                                                        | (?: type= (?! ["\']?text/css ) )
                                                        | (?: rel= (?! ["\']?stylesheet ) )
                                                ) 
                                        )*+  [^\s>]*+ > )
                                        (?: (?= (?: [^\s>]*+\s )+? href=["\']?
                                                (?=  ([^"\'\s>]++) )
                                                (?:   (?: (?= [^/\s>?]*+[=/] ) [^/\s>?]*+[=/] )*+ ( (?: [^/>?.\'"]++\. )+? (?: css ) ) )
                                        ) )
                                      [^>]*+>#ix';

                return $aCssRegex;
        }

        /**
         * 
         * @param type $sContent
         * @return type
         */
        protected function replaceImports($sContent)
        {
                return $sContent;
        }

        /**
         * Determines if file requires http protocol to get contents (Not allowed)
         * 
         * @param string $sUrl
         * @return boolean
         */
        protected function isUrlFopenAllowed($sUrl)
        {
                return ((preg_match('#(?:.*\.php)#', $sUrl) === 1) || !JchOptimizeHelper::isInternal($sUrl));
        }

        /**
         * 
         * @param type $sPath
         * @return boolean
         */
        protected function loadAsync()
        {
                return false;
        }

        /**
         * Excludes js files in editor folders
         * 
         * @param string $sUrl  File url
         * @return boolean
         */
        protected function isEditorsExcluded($sUrl)
        {
                return (preg_match('#.*/editors/#i', $sUrl));
        }

        /**
         * 
         * @return string
         */
        protected function getAsyncAttribute()
        {
                return '';
        }

        /**
         * Regex for head search area
         * 
         * @return string
         */
        public function getHeadRegex()
        {
                return '#<head[^>]*+>(?><?[^<]++)*?</head>#i';
        }
        
        
}
