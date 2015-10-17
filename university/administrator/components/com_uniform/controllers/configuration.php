<?php

/**
 * @version     $Id: configuration.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Controller
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.folder');
/**
 * Configuration controller of JSN Framework Sample component
 *
 * @package     Controllers
 *
 * @subpackage  configuration
 *
 * @since       1.6
 */
class JSNUniformControllerConfiguration extends JSNConfigController
{

		/**
		 * Check folder upload
		 *
		 * Check permission folder upload
		 * Create folder upload and coppy file
		 *
		 * @return json code
		 */
		public function checkFolderUpload()
		{
				// Get request variables
				$input = JFactory::getApplication()->input;
				$folderTmp = $input->getVar('folder_tmp', '', 'post', 'string');
				$folderOld = $input->getVar('folder_old', '', 'post', 'string');
				if (!$folderTmp)
				{
						echo json_encode(array('success' => false, 'message' => JText::_('JSN_UNIFORM_MESSAGE_ERRO_FIELD_EMPTY')));
						jexit();
				}
				$folderTmp = $folderTmp . '/jsnuniform_uploads/';
				$folderOld = $folderOld . '/jsnuniform_uploads/';
				$folderUpload = JPATH_ROOT . '/' . $folderTmp;
				$folderOldUpload = JPATH_ROOT . '/' . $folderOld;
				$folderTmpUpload = JSN_UNIFORM_FOLDER_TMP . DS . md5(date("F j, Y, g:i a") . rand(10000, 999999));

				if (!JFolder::create(JPath::clean($folderTmpUpload), 0777))
				{
						$this->errorFolderUpload = JText::sprintf('JSN_UNIFORM_SAMPLE_DATA_FOLDER_TMP_IS_UNWRITE');
						echo json_encode(array('success' => false, 'message' => $this->errorFolderUpload));
						jexit();
				}
				elseif (JFolder::exists($folderOldUpload) && $folderTmp != $folderOld)
				{
						JFolder::copy(JPath::clean($folderOldUpload), JPath::clean($folderTmpUpload), '', true, true);
				}
				if (!JFolder::exists(JPath::clean($folderUpload)))
				{
						if (!JFolder::create(JPath::clean($folderUpload), 0777))
						{
								$this->errorFolderUpload = JText::sprintf('JSN_UNIFORM_FOLDER_MUST_HAVE_WRITABLE_PERMISSION', JPath::clean($folderUpload));
								echo json_encode(array('success' => false, 'message' => $this->errorFolderUpload));
								jexit();
						}
				}
				elseif (!@is_writable(JPath::clean($folderUpload)))
				{
						$this->errorFolderUpload = JText::sprintf('JSN_UNIFORM_FOLDER_MUST_HAVE_WRITABLE_PERMISSION', JPath::clean($folderUpload));
						echo json_encode(array('success' => false, 'message' => $this->errorFolderUpload));
						jexit();
				}
				if (!empty($folderOld) && JFolder::exists($folderOldUpload) && $folderTmp != $folderOld)
				{
						JFolder::copy(JPath::clean($folderTmpUpload), JPath::clean($folderUpload), '', true, true);
						//JFolder::delete($folderTmpUpload);
				}
				echo json_encode(array('success' => true, 'message' => JText::_('JSN_UNIFORM_FOLDER_IS_DONE')));
				jexit();
		}
}
