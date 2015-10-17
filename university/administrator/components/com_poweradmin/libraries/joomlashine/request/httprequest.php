<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: httprequest.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );
class JSNHTTPRequest 
{ 
    var $_fp;        // HTTP socket 
    var $_url;        // full URL 
    var $_host;        // HTTP host 
    var $_protocol;    // protocol (HTTP/HTTPS) 
    var $_uri;        // request URI 
    var $_port;        // port 
    var $_headers = array();
    
    // scan url 
    function _scanUrl() 
    { 
        $req = $this->_url; 
        
        $pos = strpos($req, '://'); 
        $this->_protocol = strtolower(substr($req, 0, $pos)); 
        
        $req = substr($req, $pos+3); 
        $pos = strpos($req, '/'); 
        if($pos === false) 
            $pos = strlen($req); 
        $host = substr($req, 0, $pos); 
        
        if(strpos($host, ':') !== false) 
        { 
            list($this->_host, $this->_port) = explode(':', $host); 
        } 
        else 
        { 
            $this->_host = $host; 
            $this->_port = ($this->_protocol == 'https') ? 443 : 80; 
        } 
        
        $this->_uri = substr($req, $pos); 
        if($this->_uri == '') 
            $this->_uri = '/'; 
    } 
    
    // constructor 
    function __construct($url) 
    { 
        $this->_url = $url; 
        $this->_scanUrl(); 
    } 
    
	function &getInstance()
	{
		static $instanceHTTPRequest;
		
		if ($instanceHTTPRequest == null)
		{
			$instanceHTTPRequest = new JSNHTTPRequest();
		}
		return $instanceHTTPRequest;
	}
    
    // download URL to string 
    function DownloadToString() 
    { 
        if(!function_exists('fsockopen')) return false;
		
		$crlf = "\r\n"; 
        
        // generate request 
        $req = 'GET ' . $this->_uri . ' HTTP/1.0' . $crlf 
            .    'Host: ' . $this->_host . $crlf 
            .    $crlf; 
        
        // fetch 
        $this->_fp = @fsockopen(($this->_protocol == 'https' ? 'ssl://' : '') . $this->_host, $this->_port); 
        @fwrite($this->_fp, $req); 
        while(is_resource($this->_fp) && $this->_fp && !feof($this->_fp)) 
            @$response .= fread($this->_fp, 1024); 
        @fclose($this->_fp); 
        
        // split header and body 
        $pos = @strpos($response, $crlf . $crlf); 
        if($pos === false) 
            return(@$response); 
        $header = substr($response, 0, $pos); 
        $body 	= substr($response, $pos + 2 * strlen($crlf)); 
        
        // parse headers 
        $headers 	= array(); 
        $lines 		= explode($crlf, $header); 
        foreach($lines as $line) 
            if(($pos = strpos($line, ':')) !== false) 
                $headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1)); 
        
        $this->_headers = $headers;
        
        // redirection? 
        if(isset($headers['location'])) 
        { 
            $http = new JSNHTTPRequest($headers['location']); 
            return($http->DownloadToString($http)); 
        } 
        else 
        { 
            return($body); 
        } 
    }
    
    public function getRequestHeader()
    {
    	return $this->_headers;
    }
} 
?>