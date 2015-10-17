<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2014 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 *
 *
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

class AEPostprocWebdav extends AEAbstractPostproc
{
	/** @var string Username  */
    protected $username;
    /** @var string Password */
    protected $password;
    /** @var int The retry count of this file (allow up to 2 retries after the first upload failure) */
    private $tryCount = 0;

	/** @var AEUtilDavclient  WebDAV client*/
	private $webdav;

	/** @var string The currently configured directory */
	private $directory;

	public function __construct()
	{
		parent::__construct();

		$this->can_download_to_browser = false;
		$this->can_delete = true;
		$this->can_download_to_file = true;
	}

    public function processPart($absolute_filename, $upload_as = null)
	{
		$settings = $this->_getSettings();

		if($settings === false) return false;

		$directory = $this->directory;

		$basename = empty($upload_as) ? basename($absolute_filename) : $upload_as;

		try
		{
			$result = $this->putFile($absolute_filename, $directory, $basename);
		}
		catch(Exception $e)
		{
			$result = false;
			$this->setWarning($e->getMessage());
		}

		if($result === false)
        {
			// Let's retry
			$this->tryCount++;
			// However, if we've already retried twice, we stop retrying and call it a failure
			if($this->tryCount > 2) return false;
			return -1;
		}
        else
        {
			// Upload complete. Reset the retry counter.
			$this->tryCount = 0;
			return true;
		}
	}

	public function downloadToFile($remotePath, $localFile, $fromOffset = null, $length = null)
	{
		// Get settings
		$settings = $this->_getSettings();
		if($settings === false) return false;

		if(!is_null($fromOffset))
        {
			// Ranges are not supported
			return -1;
		}

		// Download the file
		$done = false;
		try
        {
            $handle = fopen($localFile, 'w+');
			$this->webdav->request('GET', $remotePath, $handle);
			$done = true;

            fclose($handle);
		}
        catch(Exception $e)
        {
            fclose($handle);
		}

		if(!$done)
        {
			try
            {
                $handle = fopen($localFile, 'w+');
                $this->webdav->request('GET', $remotePath, $handle);
                fclose($handle);
			}
            catch(Exception $e)
            {
                fclose($handle);
				$this->setWarning($e->getMessage());

				return false;
			}
		}

		return true;
	}

	public function delete($path)
	{
		// Get settings
		$settings = $this->_getSettings();

		if($settings === false)
        {
            return false;
        }

        // Remove starting slash, or CloudMe will read it as an absolute path
        $path = '/' . ltrim($path, '/');

		$done = false;
		try
        {
			$this->webdav->request('DELETE', $path);
			$done = true;
		}
        catch(Exception $e)
        {
            //Do nothing
		}

		if(!$done)
        {
			try
            {
                $this->webdav->request('DELETE', $path);
			}
            catch(Exception $e)
            {
				$this->setWarning($e->getMessage());

				return false;
			}
		}

		return true;
	}

	protected function _getSettings()
	{
		// Retrieve engine configuration data
		$config = AEFactory::getConfiguration();

        $username	= trim( $config->get('engine.postproc.webdav.username', '') );
		$password	= trim( $config->get('engine.postproc.webdav.password', '') );
		$url    	= trim( $config->get('engine.postproc.webdav.url', '') );

		$this->directory  = $config->get('volatile.postproc.directory', null);

		if(empty($this->directory)) {
			$this->directory = $config->get('engine.postproc.webdav.directory', '');
		}

		// Sanity checks
		if(empty($username) || empty($password))
		{
			$this->setError('You have not linked Akeeba Backup with your CloudMe account');
			return false;
		}

		// Fix the directory name, if required
		if(!empty($this->directory))
		{
			$this->directory = trim($this->directory);
			$this->directory = ltrim( AEUtilFilesystem::TranslateWinPath( $this->directory ) ,'/');
		}
		else
		{
			$this->directory = '';
		}

		// Parse tags
		$this->directory = AEUtilFilesystem::replace_archive_name_variables($this->directory);
		$config->set('volatile.postproc.directory', $this->directory);

        $settings = array(
            'baseUri'  => $url,
            'userName' => $username,
            'password' => $password,
        );

        $this->webdav = new AEUtilDavclient($settings);
		$this->webdav->addTrustedCertificates(AKEEBA_CACERT_PEM);

		return true;
	}

    private function putFile($absolute_filename, $directory, $basename)
    {
        // Store the absolute remote path in the class property
        // Let's remove the starting slash, otherwise it read as an absolute path
        $this->remote_path = trim(trim($directory, '/'). '/' . $basename, '/');

        // A directory is supplied, let's check if it really exists or not
        if($directory)
        {
            $checkPath = '';
            $parts     = explode('/', $directory);

            foreach($parts as $part)
            {
                if(!$part)
                {
                    continue;
                }

                $checkPath .= $part.'/';

                try
                {
                    $this->webdav->propFind($checkPath, array('{DAV:}resourcetype'));
                }
                catch(Exception $e)
                {
                    // Folder doesn't exist, let's create it
                    try
                    {
                        $this->webdav->request('MKCOL', $checkPath);
                    }
                    catch(Exception $e)
                    {
                        return false;
                    }
                }

            }
        }

		$this->remote_path = '/' . ltrim($this->remote_path, '/');

        $result = $this->webdav->request('PUT', $this->remote_path, file_get_contents($absolute_filename));

        return $result;
    }
}