<?php

use JchOptimize\JSMinRegex;
use JchOptimize\Minify_CSSi;

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
class JchOptimizeCombiner extends JchOptimizeBase
{

        public $params            = null;
        public $sLnEnd            = '';
        protected $sTab           = '';
        protected $aCallBackArgs  = array();
        public static $bLogErrors = FALSE;
        public $bBackend          = FALSE;
        protected $js             = '';
        protected $css            = '';

        /**
         * Constructor
         * 
         */
        public function __construct($params, $bBackend = FALSE)
        {
                $this->params   = $params;
                $this->bBackend = $bBackend;

                $oDocument    = JFactory::getDocument();
                $this->sLnEnd = $oDocument->_getLineEnd();
                $this->sTab   = $oDocument->_getTab();

                self::$bLogErrors = $this->params->get('jsmin_log', 0) ? TRUE : FALSE;
        }

        /**
         * 
         * @return type
         */
        public function getLogParam()
        {
                if (self::$bLogErrors == '')
                {
                        self::$bLogErrors = $this->params->get('log', 0);
                }

                return self::$bLogErrors;
        }

        /**
         * Get aggregated and possibly minified content from js and css files
         *
         * @param array $aUrlArray      Array of urls of css or js files for aggregation
         * @param string $sType         css or js
         * @return string               Aggregated (and possibly minified) contents of files
         */
        public function getContents($aUrlArray, $sType, $sHtml, $bCssAsync = FALSE)
        {
                $oCssParser = new JchOptimizeCssParser($this->sLnEnd, $this->bBackend);

                if ($this->$sType == '')
                {
                        $this->$sType = $this->combineFiles($aUrlArray, $sType, $oCssParser, $bCssAsync);
                        $this->$sType = $this->prepareContents($this->$sType, $sType, $oCssParser);
                }

                if ($sType == 'css' && $this->params->get('pro_optimizeCssDelivery', '0'))
                {
                        $sContents = $oCssParser->optimizeCssDelivery($this->$sType, $sHtml, $bCssAsync);
                }
                else
                {
                        $sContents = $this->$sType;
                }

                $aContents = array(
                        'filemtime' => JFactory::getDate('now', 'GMT')->toUnix(),
                        'file'      => $sContents
                );

                return $aContents;
        }

        /**
         * Aggregate contents of CSS and JS files
         *
         * @param array $aUrlArray      Array of links of files
         * @param string $sType          CSS or js
         * @return string               Aggregarted contents
         * @throws Exception
         */
        public function combineFiles($aUrlArray, $sType, $oCssParser, $bCssAsync = FALSE)
        {
                global $_PROFILER;
                JCH_DEBUG ? $_PROFILER->mark('beforeCombineFiles - ' . $sType . ' plgSystem (JCH Optimize)') : null;

                $sContents = '';
                $bAsync    = false;
                $sAsyncUrl = '';

                $oFileRetriever = JchOptimizeFileRetriever::getInstance($this->params);

                foreach ($aUrlArray as $aUrl)
                {
                        $sContent = '';

                        if (isset($aUrl['url']))
                        {

                                if ($sType == 'js' && $sAsyncUrl != '')
                                {
                                        $sContents .= $this->addCommentedUrl('js', $sAsyncUrl) .
                                                'loadScript("' . $sAsyncUrl . '", function(){});  DELIMITER';
                                        $sAsyncUrl = '';
                                }

                                $sPath = $aUrl['path'];

                                if ($sType == 'js' && $this->loadAsync($aUrl['url']))
                                {
                                        $sAsyncUrl = $aUrl['url'];
                                        $bAsync    = true;

                                        continue;
                                }
                                else
                                {
                                        if (!JchOptimizeHelper::fileExists($sPath))
                                        {
                                                if ($this->params->get('log', 0))
                                                {
                                                        JchOptimizeLogger::log(JText::_('File not found: ' . $sPath));
                                                }

                                                $sFileContents = 'COMMENT_START File does not exist COMMENT_END';
                                        }
                                        else
                                        {
                                                JCH_DEBUG ? $_PROFILER->mark('beforegetFileContents - ' . $sPath . ' plgSystem (JCH Optimize)') : null;
                                                $sFileContents = $oFileRetriever->getFileContents($sPath);
                                                JCH_DEBUG ? $_PROFILER->mark('afterGetFileContents - ' . $sPath . ' plgSystem (JCH Optimize)') : null;
                                        }

                                        if ($sFileContents === FALSE)
                                        {
                                                throw new Exception(JText::_('Failed getting file contents from ' . $sPath));
                                        }

                                        $sContent .= $sFileContents;
                                        unset($sFileContents);
                                }
                        }
                        else
                        {
                                if ($sType == 'js' && $sAsyncUrl != '')
                                {
                                        $sContents .= $this->addCommentedUrl('js', $sAsyncUrl) .
                                                'loadScript("' . $sAsyncUrl . '", function(){' . $this->sLnEnd . $aUrl['content'] . $this->sLnEnd . '});  
                                        DELIMITER';
                                        $sAsyncUrl = '';
                                }
                                else
                                {
                                        $sContent .= $aUrl['content'];
                                }
                        }

                        if ($sType == 'css')
                        {
                                unset($oCssParser->sCssUrl);
                                $oCssParser->aUrl = $aUrl;

                                $sImportContent = preg_replace('#@import\s(?:url\()?[\'"]([^\'"]+)[\'"](?:\))?#', '@import url($1)', $sContent);

                                if (is_null($sImportContent))
                                {
                                        if ($this->params->get('log', 0))
                                        {
                                                JchOptimizeLogger::log(JText::_('Error occured trying to parse for @imports in ' . $aUrl['url']));
                                        }
                                        
                                        $sImportContent = $sContent;
                                }

                                $sContent = $sImportContent;
                                $sContent = $oCssParser->correctUrl($sContent);
                                $sContent = $this->replaceImports($sContent, $aUrl);
                                $sContent = $oCssParser->handleMediaQueries($sContent);
                        }

                        if ($sType == 'js' && $sContent != '')
                        {
                                $sContent = $this->addSemiColon($sContent);
                        }

                        $sContent = $this->MinifyContent($sContent, $sType, $aUrl);

                        $sContents .= $this->addCommentedUrl($sType, $aUrl) . $sContent . 'DELIMITER';
                        unset($sContent);
                }

                if ($bAsync)
                {
                        $sContents = $this->getLoadScript() . $sContents;

                        if ($sAsyncUrl != '')
                        {
                                $sContents .= $this->addCommentedUrl('js', $sAsyncUrl) . 'loadScript("' . $sAsyncUrl . '", function(){});  DELIMITER';
                                $sAsyncUrl = '';
                        }
                }

                JCH_DEBUG ? $_PROFILER->mark('afterCombineFiles - ' . $sType . ' plgSystem (JCH Optimize)') : null;

                return $sContents;
        }

        /**
         * 
         * @param type $sContent
         * @param type $sUrl
         */
        protected function MinifyContent($sContent, $sType, $aUrl)
        {
                global $_PROFILER;

                if ($this->params->get($sType . '_minify', 0) && preg_match('#\s++#', trim($sContent)))
                {
                        $sUrl = isset($aUrl['url']) ? $aUrl['url'] : ($sType == 'css' ? 'Style' : 'Script') . ' Declaration';

                        JCH_DEBUG ? $_PROFILER->mark('beforeMinifyContent - "' . $sUrl . '" plgSystem (JCH Optimize)') : null;

                        $sContent = trim($sType == 'css' ? Minify_CSSi::process($sContent) : JSMinRegex::minify($sContent));

                        JCH_DEBUG ? $_PROFILER->mark('afterMinifyContent - "' . $sUrl . '" plgSystem (JCH Optimize)') : null;
                }

                return $sContent;
        }

        /**
         * 
         * @param type $sType
         * @param type $sUrl
         * @return string
         */
        protected function addCommentedUrl($sType, $sUrl)
        {
                $sComment = $this->sLnEnd;

                if ($this->params->get('debug', '0'))
                {
                        if (is_array($sUrl))
                        {
                                $sUrl = isset($sUrl['url']) ? $sUrl['url'] : (($sType == 'js' ? 'script' : 'style') . ' declaration');
                        }

                        $sComment = 'COMMENT_START ' . $sUrl . ' COMMENT_END';
                }

                return $sComment;
        }

        /**
         * Add semi-colon to end of js files if non exists;
         * 
         * @param string $sContent
         * @return string
         */
        public function addSemiColon($sContent)
        {
                $sContent = trim($sContent);

                if (substr($sContent, -1) != ';')
                {
                        $sContent = $sContent . ';';
                }

                return $sContent;
        }

        /**
         * Remove placeholders from aggregated file for caching
         * 
         * @param string $sContents       Aggregated file contents
         * @param string $sType           js or css
         * @return string
         */
        public function prepareContents($sContents, $sType, $oCssParser)
        {
                if ($sType == 'css')
                {
                        $sContents = $oCssParser->sortImports($sContents);

                        if ($this->params->get('csg_enable', 0))
                        {
                                try
                                {
                                        $oSpriteGenerator = new JchOptimizeSpriteGenerator($this->params);
                                        $sCssContents     = $oSpriteGenerator->getSprite($sContents);
                                }
                                catch (Exception $ex)
                                {
                                        if ($this->params->get('log', 0))
                                        {
                                                JchOptimizeLogger::log($ex->getMessage());
                                        }

                                        $sCssContents = $sContents;
                                }

                                $sContents = $sCssContents;
                        }

                        if (function_exists('mb_convert_encoding'))
                        {
                                $sContents = '@charset "utf-8";' . $this->sLnEnd . trim($sContents);
                                $sContents = mb_convert_encoding($sContents, 'utf-8');
                        }
                }

                $sContents = str_replace(
                        array(
                        'COMMENT_START',
                        'COMMENT_IMPORT_START',
                        'COMMENT_END',
                        'DELIMITER'
                        ),
                        array(
                        $this->sLnEnd . $this->sLnEnd . '/***! ',
                        $this->sLnEnd . $this->sLnEnd . '/***! @import url',
                        ' !***/' . $this->sLnEnd . $this->sLnEnd,
                        ''
                        ), $sContents);

                return trim($sContents);
        }

        /**
         * 
         * @param type $aUrlArray
         * @param type $params
         * @return type
         */
        public static function getImages($aUrlArray, $params)
        {
                $oCombiner  = new JchOptimizeCombiner($params, TRUE);
                $oCssParser = new JchOptimizeCssParser($oCombiner->sLnEnd, $oCombiner->bBackend);
                
                try
                {
                        $sCss = $oCombiner->combineFiles($aUrlArray, 'css', $oCssParser);

                        $oSpriteGenerator = new JchOptimizeSpriteGenerator($params);
                        $aMatches         = $oSpriteGenerator->processCssUrls($sCss, TRUE);
                }
                catch (Exception $Ex)
                {
                        $GLOBALS['bTextArea'] = TRUE;
                        
                        if ($oCombiner->params->get('log', '0'))
                        {
                                JchOptimizeLogger::log($Ex->getMessage());
                        }

                        throw new Exception(JText::_('Failed fetching images for the multiselect exclude options in sprite generator. '
                                . 'Will render textareas instead.'));
                }

                return $aMatches;
        }

        
}
