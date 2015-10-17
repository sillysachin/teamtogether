<?php

/**
 * JCH Optimize - Joomla! plugin to aggregate and minify external resources for 
 *   optmized downloads
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall. All rights reserved.
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
 * This plugin includes other copyrighted works. See individual 
 * files for details.
 */
define('_JEXEC', 1);
define('JPATH_BASE', dirname(dirname(dirname(dirname(dirname(__FILE__))))));
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';
$aGet = JFactory::getApplication('site')->input->getArray(array(
        'f'    => 'alnum',
        'd'    => 'int',
        'gz'   => 'word',
        'type' => 'word'
        ));

$aOptions = array(
        'defaultgroup' => 'plg_jch_optimize',
        'storage'      => 'file',
        'checkTime'    => FALSE,
        'caching'      => 1,
        'application'  => 'site',
        'language'     => 'en-GB'
);
$oCache   = JCache::getInstance('output', $aOptions);

$aCache = $oCache->get($aGet['f']);

if ($aCache === FALSE)
{
        die('File not found');
}

$sFile  = $aCache['result']['file'];

$iTimeMFile = JFactory::getDate($aCache['result']['filemtime']);
$sTimeMFile = $iTimeMFile->format('D, d M Y H:i:s');

$iTimeMFile->add(DateInterval::createFromDateString($aGet['d'] . ' days'));
$sExpiryDate = $iTimeMFile->format('D, d M Y H:i:s');

if ($aGet['type'] == 'css')
{
        header('Content-type: text/css; charset=UTF-8');
}
elseif ($aGet['type'] == 'js')
{
        header('Content-type: text/javascript; charset=UTF-8');
}

header('Expires: ' . $sExpiryDate . ' GMT');
header('Last-Modified: ' . $sTimeMFile . ' GMT');
header('Cache-Control: Public');
header('Vary: Accept-Encoding');

if (isset($aGet['gz']))
{
        if ($aGet['gz'] == 'gz')
        {
                $aAccepted  = array_map('trim', (array) explode(',', $_SERVER['HTTP_ACCEPT_ENCODING']));
                $aSupported = array(
                        'x-gzip'  => 'gz',
                        'gzip'    => 'gz',
                        'deflate' => 'deflate'
                );
                $aEncodings = array_intersect($aAccepted, array_keys($aSupported));

                if (!empty($aEncodings))
                {
                        foreach ($aEncodings as $sEncoding)
                        {
                                if (($aSupported[$sEncoding] == 'gz') || ($aSupported[$sEncoding] == 'deflate'))
                                {
                                        $sGzFile = gzencode($sFile, 4, ($aSupported[$sEncoding] == 'gz') ? FORCE_GZIP : FORCE_DEFLATE);

                                        if($sGzFile === FALSE)
                                        {
                                                continue;
                                        }
                                        
                                        header('Content-Encoding: ' . $sEncoding);
                                        header('Content-Length: ' . strlen($sGzFile));
                                        
                                        $sFile = $sGzFile;
                                        
                                        break;
                                }
                        }
                }
        }
}

echo $sFile;
