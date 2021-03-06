<?php
/**
 *  @package AkeebaBackup
 *  @copyright Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  --
 *
 *  Command-line script to schedule the File Alteration Monitor check
 */
// Define ourselves as a parent file
define('_JEXEC', 1);

// Enable Akeeba Engine
define('AKEEBAENGINE', 1);

$minphp = '5.3.1';
if (version_compare(PHP_VERSION, $minphp, 'lt'))
{
	$curversion = PHP_VERSION;
	$bindir = PHP_BINDIR;
	echo <<< ENDWARNING
================================================================================
WARNING! Incompatible PHP version $curversion
================================================================================

This CRON script must be run using PHP version $minphp or later. Your server is
currently using a much older version which would cause this script to crash. As
a result we have aborted execution of the script. Please contact your host and
ask them for the correct path to the PHP CLI binary for PHP $minphp or later,
then edit your CRON job and replace your current path to PHP with the one your
host gave you.

For your information, the current PHP version information is as follows.

PATH:    $bindir
VERSION: $curversion

Further clarifications:

1. There is absolutely no possible way that you are receiving this warning in
   error. We are using the PHP_VERSION constant to detect the PHP version you
   are currently using. This is what PHP itself reports as its own version. It
   simply cannot lie.

2. Even though your *site* may be running in a higher PHP version that the one
   reported above, your CRON scripts will most likely not be running under it.
   This has to do with the fact that your site DOES NOT run under the command
   line and there are different executable files (binaries) for the web and
   command line versions of PHP.

3. Please note that you MUST NOT ask us for support about this error. We can
   not possibly know the correct path to the PHP CLI binary as we have not set
   up your server. Your host must know and give that information.

4. The latest published versions of PHP can be found at http://www.php.net/
   Any older version is considered insecure and must NOT be used on a live
   server. If your server uses a much older version of PHP than that please
   notify them that their servers are insecure and in need of an update.

This script will now terminate. Goodbye.

ENDWARNING;
	die();
}

// Required by the CMS
define('DS', DIRECTORY_SEPARATOR);

// Timezone fix; avoids errors printed out by PHP 5.3.3+ (thanks Yannick!)
if (function_exists('date_default_timezone_get') && function_exists('date_default_timezone_set'))
{
	if (function_exists('error_reporting'))
	{
		$oldLevel = error_reporting(0);
	}
	$serverTimezone	 = @date_default_timezone_get();
	if (empty($serverTimezone) || !is_string($serverTimezone))
		$serverTimezone	 = 'UTC';
	if (function_exists('error_reporting'))
	{
		error_reporting($oldLevel);
	}
	@date_default_timezone_set($serverTimezone);
}

// Load system defines
if (file_exists(__DIR__ . '/defines.php'))
{
	include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	$path = rtrim(__DIR__, DIRECTORY_SEPARATOR);
	$rpos = strrpos($path, DIRECTORY_SEPARATOR);
	$path = substr($path, 0, $rpos);
	define('JPATH_BASE', $path);
	require_once JPATH_BASE . '/includes/defines.php';
}

// Load the rest of the framework include files
if (file_exists(JPATH_LIBRARIES . '/import.legacy.php'))
{
	require_once JPATH_LIBRARIES . '/import.legacy.php';
}
else
{
	require_once JPATH_LIBRARIES . '/import.php';
}
require_once JPATH_LIBRARIES . '/cms.php';

// You can't fix stupid… but you can try working around it
if( (!function_exists('json_encode')) || (!function_exists('json_decode')) )
{
	require_once JPATH_ADMINISTRATOR . '/components/com_akeeba/helpers/jsonlib.php';
}

// Load the JApplicationCli class
JLoader::import('joomla.application.cli');

/**
 * Akeeba Backup alternative check failed application
 */
class AkeebaBackupAltCheckfailed extends JApplicationCli
{
	/**
	 * JApplicationCli didn't want to run on PHP CGI. I have my way of becoming
	 * VERY convincing. Now obey your true master, you petty class!
	 *
	 * @param JInputCli $input
	 * @param JRegistry $config
	 * @param JDispatcher $dispatcher
	 */
	public function __construct(JInputCli $input = null, JRegistry $config = null, JDispatcher $dispatcher = null)
	{
		// Close the application if we are not executed from the command line, Akeeba style (allow for PHP CGI)
		if (array_key_exists('REQUEST_METHOD', $_SERVER))
		{
			die('You are not supposed to access this script from the web. You have to run it from the command line. If you don\'t understand what this means, you must not try to use this file before reading the documentation. Thank you.');
		}

		$cgiMode = false;

		if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
		{
			$cgiMode = true;
		}

		// If a input object is given use it.
		if ($input instanceof JInput)
		{
			$this->input = $input;
		}
		// Create the input based on the application logic.
		else
		{
			if (class_exists('JInput'))
			{
				if ($cgiMode)
				{
					$query = "";
					if (!empty($_GET))
					{
						foreach ($_GET as $k => $v)
						{
							$query .= " $k";
							if ($v != "")
							{
								$query .= "=$v";
							}
						}
					}
					$query	 = ltrim($query);
					$argv	 = explode(' ', $query);
					$argc	 = count($argv);

					$_SERVER['argv'] = $argv;
				}

				$this->input = new JInputCLI();
			}
		}

		// If a config object is given use it.
		if ($config instanceof JRegistry)
		{
			$this->config = $config;
		}
		// Instantiate a new configuration object.
		else
		{
			$this->config = new JRegistry;
		}

		// If a dispatcher object is given use it.
		if ($dispatcher instanceof JDispatcher)
		{
			$this->dispatcher = $dispatcher;
		}
		// Create the dispatcher based on the application logic.
		else
		{
			$this->loadDispatcher();
		}

		// Load the configuration object.
		$this->loadConfiguration($this->fetchConfigurationData());

		// Set the execution datetime and timestamp;
		$this->set('execution.datetime', gmdate('Y-m-d H:i:s'));
		$this->set('execution.timestamp', time());

		// Set the current directory.
		$this->set('cwd', getcwd());
	}

	public function execute()
	{
		$version		 = AKEEBA_VERSION;
		$date			 = AKEEBA_DATE;

		if ($this->input->get('quiet', -1, 'int') == -1)
		{
			$year = gmdate('Y');
			echo <<<ENDBLOCK
Akeeba Backup Alternate CRON Helper Script check for failures version $version ($date)
Copyright (C) 2010-$year Nicholas K. Dionysopoulos
-------------------------------------------------------------------------------
Akeeba Backup is Free Software, distributed under the terms of the GNU General
Public License version 3 or, at your option, any later version.
This program comes with ABSOLUTELY NO WARRANTY as per sections 15 & 16 of the
license. See http://www.gnu.org/licenses/gpl-3.0.html for details.
-------------------------------------------------------------------------------

ENDBLOCK;
		}

		// Log some paths
		if ($this->input->get('quiet', -1, 'int') == -1)
		{
			echo "Site paths determined by this script:\n";
			echo "JPATH_BASE : " . JPATH_BASE . "\n";
			echo "JPATH_ADMINISTRATOR : " . JPATH_ADMINISTRATOR . "\n\n";
		}

		// Load the engine
		$factoryPath = JPATH_ADMINISTRATOR . '/components/com_akeeba/akeeba/factory.php';
		define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_akeeba');
		if (!file_exists($factoryPath))
		{
			echo "ERROR!\n";
			echo "Could not load the backup engine; file does not exist. Technical information:\n";
			echo "Path to " . basename(__FILE__) . ": " . __DIR__ . "\n";
			echo "Path to factory file: $factoryPath\n";
			die("\n");
		}
		else
		{
			try
			{
				require_once $factoryPath;
			}
			catch (Exception $e)
			{
				echo "ERROR!\n";
				echo "Backup engine returned an error. Technical information:\n";
				echo "Error message:\n\n";
				echo $e->getMessage() . "\n\n";
				echo "Path to " . basename(__FILE__) . ":" . __DIR__ . "\n";
				echo "Path to factory file: $factoryPath\n";
				die("\n");
			}
		}

		$startup_check = true;

		// Get the live site's URL
		$url = AEPlatform::getInstance()->get_platform_configuration_option('siteurl', '');
		if (empty($url))
		{
			echo <<<ENDTEXT
ERROR:
	This script could not detect your live site's URL. Please visit Akeeba
	Backup's Control Panel page at least once before running this script, so
	that this information can be stored for use by this script.

ENDTEXT;
			$startup_check = false;
		}

		// Get the front-end backup settings
		$frontend_enabled	 = AEPlatform::getInstance()->get_platform_configuration_option('failure_frontend_enable', '');
		$secret				 = AEPlatform::getInstance()->get_platform_configuration_option('frontend_secret_word', '');

		if (!$frontend_enabled)
		{
			echo <<<ENDTEXT
ERROR:
	Your Akeeba Backup installation's front-end check for failures feature
	is currently disabled. Please log in to your site's back-end as a Super
	Administrator, go to Akeeba Backup's Control Panel, click on the Options
	icon in	the top right corner and enable the front-end backup feature. Do not
	forget to also set a Secret Word!

ENDTEXT;
			$startup_check = false;
		}
		elseif (empty($secret))
		{
			echo <<<ENDTEXT
ERROR:
	You have enabled the front-end check for failure feature, but you forgot
	to set a secret word. Without a valid secret word this script can not continue.
	Please log in to your site's back-end as a Super Administrator, go to
	Akeeba Backup's Control Panel, click on the Options icon in the top
	right corner set a Secret Word.

ENDTEXT;
			$startup_check = false;
		}

		// Detect cURL or fopen URL
		$method = null;
		if (function_exists('curl_init'))
		{
			$method = 'curl';
		}
		elseif (function_exists('fsockopen'))
		{
			$method = 'fsockopen';
		}

		if (empty($method))
		{
			if (function_exists('ini_get'))
			{
				if (ini_get('allow_url_fopen'))
				{
					$method = 'fopen';
				}
			}
		}

		$overridemethod = $this->input->get('method', '', 'cmd');
		if (!empty($overridemethod))
		{
			$method = $overridemethod;
		}

		if (empty($method))
		{
			echo <<<ENDTEXT
ERROR:
	Could not find any supported method for running the front-end backup
	feature of Akeeba Backup. Please check with your host that at least
	one of the following features are supported in your PHP configuration:
	1. The cURL extension
	2. The fsockopen() function
	3. The fopen() URL wrappers, i.e. allow_url_fopen is enabled
	If neither method is available you will not be able to backup your
	site using this CRON helper script.

ENDTEXT;
			$startup_check = false;
		}

		if (!$startup_check)
		{
			echo "\n\nBACKUP FAILURE CHECK ABORTED DUE TO CONFIGURATION ERRORS\n\n";
			$this->close(255);
		}

		// Perform the backup
		$url	 = rtrim($url, '/');
		$secret	 = urlencode($secret);
		$url .= "/index.php?option=com_akeeba&view=check&key={$secret}";


        $timestamp	 = date('Y-m-d H:i:s');

        $result = $this->fetchURL($url, $method);

        if (empty($result) || ($result === false))
        {
            echo "[{$timestamp}] No message received\n";
            echo <<<ENDTEXT
ERROR:
    Your check for failures attempt has timed out, or a fatal PHP error has occurred.

ENDTEXT;
        }
        elseif (strpos($result, '200 ') !== false)
        {
            echo "[{$timestamp}] Checks finalization message received\n";
            echo <<<ENDTEXT

Checks are finished successfully.

ENDTEXT;
        }
        elseif (strpos($result, '500 ') !== false)
        {
            // Backup error
            echo "[{$timestamp}] Error signal received\n";
            echo <<<ENDTEXT
ERROR:
An error has occurred. The server's response was:

$result

Backup failed.

ENDTEXT;
        }
        elseif (strpos($result, '403 ') !== false)
        {
            // This should never happen: invalid authentication or front-end backup disabled
            echo "[{$timestamp}] Connection denied (403) message received\n";
            echo <<<ENDTEXT
ERROR:
The server denied the connection. Please make sure that the front-end
backup feature is enabled and a valid secret word is in place.

Server response: $result

Backup failed.

ENDTEXT;
        }
        else
        {
            // Unknown result?!
            echo "[{$timestamp}] Could not parse the server response.\n";
            echo <<<ENDTEXT
ERROR:
We could not understand the server's response. Most likely an error
has occurred. The server's response was:

$result

If you do not see "200 OK" at the end of this output, checks failed.

ENDTEXT;
        }
    }

	/**
	 * Fetches a remote URL using curl, fsockopen or fopen
	 *
	 * @param  string	$url		The remote URL to fetch
	 * @param  string	$method		The method to use: curl, fsockopen or fopen (optional)
	 * @return string The contents of the URL which was fetched
	 */
	private function fetchURL($url, $method = 'curl')
	{
		switch ($method)
		{
			case 'curl':
				$ch			 = curl_init($url);
				$cacertPath	 = JPATH_ADMINISTRATOR . '/components/com_akeeba/akeeba/assets/cacert.pem';
				if (file_exists($cacertPath))
				{
					@curl_setopt($ch, CURLOPT_CAINFO, $cacertPath);
				}
				@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				@curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
				@curl_setopt($ch, CURLOPT_HEADER, false);
				@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				@curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				@curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 180);
				@curl_setopt($ch, CURLOPT_TIMEOUT, 180);
				$result = curl_exec($ch);
				curl_close($ch);
				return $result;
				break;

			case 'fsockopen':
				$pos		 = strpos($url, '://');
				$protocol	 = strtolower(substr($url, 0, $pos));
				$req		 = substr($url, $pos + 3);
				$pos		 = strpos($req, '/');
				if ($pos === false)
					$pos		 = strlen($req);
				$host		 = substr($req, 0, $pos);

				if (strpos($host, ':') !== false)
				{
					list($host, $port) = explode(':', $host);
				}
				else
				{
					$host	 = $host;
					$port	 = ($protocol == 'https') ? 443 : 80;
				}

				$uri = substr($req, $pos);
				if ($uri == '')
					$uri = '/';

				$crlf	 = "\r\n";
				$req	 = 'GET ' . $uri . ' HTTP/1.0' . $crlf
					. 'Host: ' . $host . $crlf
					. $crlf;

				$fp			 = fsockopen(($protocol == 'https' ? 'ssl://' : '') . $host, $port);
				fwrite($fp, $req);
				$response	 = '';
				while (is_resource($fp) && $fp && !feof($fp))
					$response .= fread($fp, 1024);
				fclose($fp);

				// split header and body
				$pos	 = strpos($response, $crlf . $crlf);
				if ($pos === false)
					return($response);
				$header	 = substr($response, 0, $pos);
				$body	 = substr($response, $pos + 2 * strlen($crlf));

				// parse headers
				$headers											 = array();
				$lines												 = explode($crlf, $header);
				foreach ($lines as $line)
					if (($pos												 = strpos($line, ':')) !== false)
						$headers[strtolower(trim(substr($line, 0, $pos)))]	 = trim(substr($line, $pos + 1));

				//redirection?
				if (isset($headers['location']))
				{
					return $this->fetchURL($headers['location'], $method);
				}
				else
				{
					return($body);
				}

				break;

			case 'fopen':
				$opts = array(
					'http' => array(
						'method' => "GET",
						'header' => "Accept-language: en\r\n"
					)
				);

				$context = stream_context_create($opts);
				$result	 = @file_get_contents($url, false, $context);
				break;
		}

		return $result;
	}

}
// Load the version file
require_once JPATH_ADMINISTRATOR . '/components/com_akeeba/version.php';

// Instanciate and run the application
JApplicationCli::getInstance('AkeebaBackupAltCheckfailed')->execute();