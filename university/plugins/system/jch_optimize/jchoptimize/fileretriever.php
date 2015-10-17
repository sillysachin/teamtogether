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
defined('_JEXEC') or die('Restricted access');

/**
 * 
 * 
 */
class JchOptimizeFileRetriever
{

        protected $bCurlEnabled     = FALSE;
        protected $bAllowFopenUrl   = FALSE;
        protected $rCh              = NULL;
        protected static $instances = array();
        public $params;

        /**
         * 
         */
        protected function __construct($params = null)
        {
                $this->params = $params;

                if (in_array('curl', get_loaded_extensions()))
                {
                        $this->bCurlEnabled = TRUE;
                }

                if (ini_get('allow_url_fopen'))
                {
                        $this->bAllowFopenUrl = TRUE;
                }
        }

        /**
         * 
         * @param type $sPath
         * @return type
         */
        public function getFileContents($sPath)
        {
                if ((strpos($sPath, 'http') === 0) && $this->bCurlEnabled)
                {
                        return $this->getContentsWithCurl($sPath);
                }
                else
                {
                        return $this->getContents($sPath);
                }
        }

        /**
         * 
         * @param type $sPath
         * @return type
         * @throws Exception
         */
        protected function getContentsWithCurl($sPath)
        {
                $rCh = $this->getCurlResource();

                curl_setopt($rCh, CURLOPT_HEADER, 0);
                curl_setopt($rCh, CURLOPT_URL, $sPath);
                curl_setopt($rCh, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($rCh, CURLOPT_AUTOREFERER, TRUE);
                curl_setopt($rCh, CURLOPT_SSL_VERIFYPEER, TRUE);
                curl_setopt($rCh, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($rCh, CURLOPT_CAINFO, JPATH_PLUGINS . '/system/jch_optimize/assets/cacert.pem');
                @curl_setopt($rCh, CURLOPT_FOLLOWLOCATION, TRUE);

                $sHtml = curl_exec($rCh);

                if ($sHtml === FALSE || trim($sHtml) == '')
                {
                        if ($this->params->get('log', 0))
                        {
                                JchOptimizeLogger::log(
                                        JText::_(
                                                sprintf(
                                                        'Curl failed with error #%d: %s '
                                                        . 'fetching contents from %s', curl_errno($rCh), curl_error($rCh), $sPath
                                                )
                                        )
                                );
                        }

                        if ($this->bAllowFopenUrl)
                        {
                                $sHtml = $this->getContents($sPath);
                        }
                        else
                        {
                                throw new Exception(curl_error($rCh), curl_errno($rCh));
                        }
                }

                return $sHtml;
        }

        /**
         * 
         * @param type $sPath
         * @return type
         */
        protected function getContents($sPath)
        {
                return file_get_contents($sPath);
        }

        /**
         * 
         * @return type
         */
        public function getCurlResource()
        {
                if (is_null($this->rCh))
                {
                        $this->rCh = curl_init();
                }

                return $this->rCh;
        }

        /**
         * 
         */
        public function __destruct()
        {
                if (!is_null($this->rCh))
                {
                        curl_close($this->rCh);
                }
        }

        /**
         * 
         * @return type
         */
        public static function getInstance($params = null)
        {
                $signature = md5(serialize($params));
                
                if (!isset(self::$instances[$signature]))
                {
                        self::$instances[$signature] = new JchOptimizeFileRetriever($params);
                }

                return self::$instances[$signature];
        }

        /**
         * 
         * @return type
         */
        public function isUrlFOpenAllowed()
        {
                return ($this->bAllowFopenUrl || $this->bCurlEnabled);
        }

}

//copied from http://slopjong.de/2012/03/31/curl-follow-locations-with-safe_mode-enabled-or-open_basedir-set/
function curl_exec_follow($ch, &$maxredirect = null)
{

        // we emulate a browser here since some websites detect
        // us as a bot and don't let us do our job
        $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5)" .
                " Gecko/20041107 Firefox/1.0";
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

        $mr = $maxredirect === null ? 5 : intval($maxredirect);

        if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off')
        {

                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
                curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
                //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        else
        {

                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

                if ($mr > 0)
                {
                        $original_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                        $newurl       = $original_url;

                        $rch = curl_copy_handle($ch);

                        curl_setopt($rch, CURLOPT_HEADER, true);
                        curl_setopt($rch, CURLOPT_NOBODY, true);
                        curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
                        do
                        {
                                curl_setopt($rch, CURLOPT_URL, $newurl);
                                $header = curl_exec($rch);
                                if (curl_errno($rch))
                                {
                                        $code = 0;
                                }
                                else
                                {
                                        $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                                        if ($code == 301 || $code == 302)
                                        {
                                                preg_match('/Location:(.*?)\n/', $header, $matches);
                                                $newurl = trim(array_pop($matches));

                                                // if no scheme is present then the new url is a
                                                // relative path and thus needs some extra care
                                                if (!preg_match("/^https?:/i", $newurl))
                                                {
                                                        $newurl = $original_url . $newurl;
                                                }
                                        }
                                        else
                                        {
                                                $code = 0;
                                        }
                                }
                        }
                        while ($code && --$mr);

                        curl_close($rch);

                        if (!$mr)
                        {
                                if ($maxredirect === null) trigger_error('Too many redirects.', E_USER_WARNING);
                                else $maxredirect = 0;

                                return false;
                        }
                        curl_setopt($ch, CURLOPT_URL, $newurl);
                }
        }
        return curl_exec($ch);
}
