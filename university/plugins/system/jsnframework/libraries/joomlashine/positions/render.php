<?php
/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Position selection rendering class.
 *
 * @package  JSN_Framework
 * @since    1.0.3
 */
class JSNPositionsRender
{
	// Contents of page
	private $_contents = '';

	// Current URL of page
	public $_renderUrl = '';

	// Variable to able the url render is internal/external
	protected $_isExternal = false;

	/**
	 *  Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Check link render is internal/external.
	 *
	 * @return  boolean
	 */
	public function isExternal()
	{
		return $this->_isExternal;
	}

	/**
	 * Return global JSNRender object.
	 *
	 * @return  string
	 */
	public static function getInstance()
	{
		static $instances;

		if ( ! isset($instances))
		{
			$instances = array();
		}

		if (empty($url))
		{
			$url = JURI::root() . '?poweradmin=1';
		}

		if (empty($instances['JSNPositionsRender']))
		{
			$instance = new JSNPositionsRender;
			$instances['JSNPositionsRender'] = &$instance;
		}

		return $instances['JSNPositionsRender'];
	}

	/**
	 * Using CURL get page source.
	 *
	 * @param   string  $url  Link to get content
	 *
	 * @return  string
	 */
	protected function curlResponse($url)
	{
		$posts = array();

		if (isset($_POST['return']) AND count($_POST) > 0)
		{
			$posts	= $_POST;
			$uri	= new JURI($this->_renderUrl);

			if ($uri->hasVar('task') AND ( $uri->getVar('task') == 'user.login' OR $uri->getVar('task') == 'user.logout' ))
			{
				$posts['option'] = 'com_users';
				$posts['view']   = 'user';
			}
		}

		$options = array(
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_HEADER			=> false,
			CURLOPT_FOLLOWLOCATION	=> true,
			CURLOPT_USERAGENT		=> "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13",
			CURLOPT_AUTOREFERER		=> true,
			CURLOPT_CONNECTTIMEOUT	=> 120,
			CURLOPT_TIMEOUT			=> 120,
			CURLOPT_MAXREDIRS		=> 10,
			CURLOPT_POSTFIELDS		=> $posts,
			CURLOPT_COOKIEJAR		=> dirname(__FILE__) . '/jsn_poweradmin_cookie.txt',
			CURLOPT_COOKIEFILE		=> dirname(__FILE__) . '/jsn_poweradmin_cookie.txt',
			CURLOPT_SSL_VERIFYPEER 	=> false
		);

		$ch = curl_init($url);

		curl_setopt_array($ch, $options);

		$contents	= curl_exec($ch);
		$err		= curl_errno($ch);
		$errmsg		= curl_error($ch);
		$header		= curl_getinfo($ch);

		curl_close($ch);

		if ($err > 0) {
			exit('cUrl error number: ' . $err);
		}

		$response = new stdClass;
		$response->contents			= $contents;
		$response->redirect_url		= $header['redirect_url'];

		if ($response->redirect_url != $this->_renderUrl AND JString::trim($response->redirect_url) != '')
		{
			$old_uri		= new JURI($this->_renderUrl);
			$redirect_uri	= new JURI($response->redirect_url);

			if (!$redirect_uri->hasVar('poweradmin'))
			{
				$redirect_uri->setVar('poweradmin', 1);
			}

			if ($old_uri->hasVar('tp') AND ! $redirect_uri->hasVar('tp'))
			{
				$redirect_uri->setVar('tp', 1);
			}

			if ($old_uri->hasVar('Itemid') AND ! $redirect_uri->hasVar('Itemid'))
			{
				$redirect_uri->setVar('Itemid', $old_uri->getVar('Itemid'));
			}

			// Save redirect url
			$this->_renderUrl  = $redirect_uri->toString();

			$response	= $this->curlResponse($this->_renderUrl);
		}

		return $response;
	}

	/**
	* Get content front-end joomla.
	*
	* @param   string  $viewMode  View mode
	*
	* @return  void
	*/
	protected function getContents($viewMode)
	{
		/** get contents of front-end page **/
		try
		{
			if (function_exists('curl_init'))
			{
				$response = $this->curlResponse($this->_renderUrl);

				// Parse front-end content
				list($head, $body) = explode('</head>', $response->contents);
				list($temp, $head) = explode('<head>', $head);
				list($body, $temp) = explode('</body>', $body);

				$head = preg_replace(
						array(
								'#<base\s+href="[^"]+"\s*/>#',
								'#<meta\s+[^>]+/>#',
								'#<title>[^\r\n]+</title>#'
						),
						'',
						$head
				);

				$body = preg_replace('#<body[^>]*>#', '', $body);

				$this->_contents = array(
					'head' => $head,
					'body' => $body
				);
			}
			else
			{
				throw new Exception(JText::_('JSN_EXTFW_ERROR_CURL_NOT_ENABLE'));
			}
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * Get current URL string.
	 *
	 * @return  void
	 */
	public function getCurrentUrlInfos()
	{
		$currentUrl	= $this->_renderUrl;
		$url		= new JURI($currentUrl);

		$urlInfos = new stdClass;
		$urlInfos->urlString			= '';
		$urlInfos->showTemplatePosition	= false;

		if ($url->hasVar('tp') AND $url->getVar('tp') == 1)
		{
			$url->delVar('tp');
			$urlInfos->showTemplatePosition = true;
		}

		$urlInfos->urlString = $url->toString();

		return $urlInfos;
	}

	/**
	* DOMDocument get inner HTML
	*
	* @param   object  $element  DOMElement object
	*
	* @return  string
	*/
	protected function DOMinnerHTML($element)
	{
		$innerHTML = "";
		$children = $element->childNodes;

		if (count($children) > 0)
		{
			foreach ($children AS $child)
			{
				$tmp_dom = new DOMDocument;
				$tmp_dom->appendChild($tmp_dom->importNode($child, true));
				$innerHTML .= trim($tmp_dom->saveHTML());
			}
		}

		return $innerHTML;
	}

	/**
	* Get html in <head> of front-end page.
	*
	* @return  array
	*/
	public function getHeader()
	{
		return $this->_contents['head'];
	}

	/**
	* Get html in <body> of front-end page.
	*
	* @return  string
	*/
	public function getBody()
	{
		return $this->_contents['body'];
	}

	/**
	* Get HTML of component.
	*
	* @return  string
	*/
	public function getComponent()
	{
		$html = '<div class="jsn-component-container" id="jsnrender-component" >';
		$doc = new DOMDocument;

		if (@$doc->loadhtml($this->_contents))
		{
			$doc->preserveWhiteSpace = false;
			$contentid = $doc->getElementById('jsnrender-component');

			if (is_object($contentid))
			{
				$html .= $this->DOMinnerHTML($contentid);
			}
		}

		$html .= '</div>';

		return $html;
	}
	/**
	* Get Current Menu Id.
	*
	* @return  integer
	*/
	public function getCurrentItemid()
	{
		$doc = new DOMDocument;

		if (@$doc->loadhtml($this->_contents))
		{
			$doc->preserveWhiteSpace = false;
			$component = $doc->getElementById('tableshow');

			if (is_object($component))
			{
				return $component->getAttribute('itemid');
			}
		}

		return 0;
	}
	/**
	 * Set URL for get front-end content. Correct URL
	 *
	 * @param   string  $url  Link
	 *
	 * @return  void
	 */
	public function setRenderUrl($url = '')
	{
		$uri = new JURI($url);

		if ($uri->getScheme() == '')
		{
			$scheme = 'http';

			if (@$_SERVER['HTTPS'])
			{
				$scheme = 'https';
			}
			$uri->setScheme($scheme);
		}

		@list($host, $port) = explode(':', $_SERVER['HTTP_HOST']);

		if ($uri->getHost() == '')
		{
			$uri->setHost($host);
		}

		if ($uri->getPort() == '')
		{
			$uri->setPort($port);
		}

		if (JString::strtolower($uri->getHost()) != JString::strtolower($host))
		{
			$this->_isExternal = true;
		}
		else
		{
			if ( ! $uri->hasVar('poweradmin'))
			{
				$uri->setVar('poweradmin', '1');
			}

			if ($uri->hasVar('Itemid') AND $uri->getVar('Itemid') == '')
			{
				$uri->delVar('Itemid');
			}

			$this->_renderUrl = $uri->toString();
		}
	}

	/**
	 * Get current render URL.
	 *
	 * @return  string
	 */
	public function getRenderUrl()
	{
		return $this->_renderUrl;
	}

	/**
	 * Render site content.
	 *
	 * @param   string  $url       Link
	 * @param   string  $viewMode  View mode
	 *
	 * @return  void
	 */
	public function renderPage( $url = '', $viewMode = 'jsnrender' )
	{
		$this->setRenderUrl($url);

		if ( ! $this->isExternal())
		{
			$this->getContents($viewMode);
		}
	}
}
