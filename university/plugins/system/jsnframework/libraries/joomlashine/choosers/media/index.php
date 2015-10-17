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

// Set Joomla execution flag
define('_JEXEC', 1);

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Detect Joomla root
$jRoot = '';

if (strpos(str_replace('/', DIRECTORY_SEPARATOR, __FILE__), str_replace('/', DIRECTORY_SEPARATOR, $_SERVER['DOCUMENT_ROOT'])) === false)
{
	$jRoot = ( ! empty($_SERVER['PHP_SELF']) AND ! empty($_SERVER['REQUEST_URI']))
		? $_SERVER['REQUEST_URI']
		: $_SERVER['SCRIPT_NAME'];

	$jRoot = ($limit = strpos($jRoot, '?')) !== false
		? substr($jRoot, 0, $limit)
		: $jRoot;

	$jRoot = $_SERVER['DOCUMENT_ROOT'] . substr($jRoot, 0, strpos($jRoot, '/plugins/'));
}

if ( ! file_exists($jRoot . '/libraries/joomla/factory.php'))
{
	$jRoot = str_replace(basename(__FILE__), '..', __FILE__);
	$level = 0;

	while ( ! file_exists($jRoot . '/libraries/joomla/factory.php') AND $level < 10)
	{
		// Increase parent level
		$level++;

		// Continue go up directory path
		$jRoot .= DIRECTORY_SEPARATOR . '..';
	}
}

if ( ! file_exists($jRoot . '/libraries/joomla/factory.php'))
{
	die('Fail to initialize application because the Joomla root directory cannot be detected!');
}

// Define base directory
define('JPATH_BASE', realpath($jRoot . '/administrator'));

// Initialize Joomla framework
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

// Instantiate the application
$app = JFactory::getApplication('administrator');

// Access check
if ( ! JFactory::getUser()->authorise('core.manage', $app->input->getCmd('component')))
{
	jexit('Please login to administration panel first!');
}

// Import necessary Joomla libraries
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

// Get Joomla version
$JVersion = new JVersion;

// Initialize JSN Framework
require_once JPATH_ROOT . '/plugins/system/jsnframework/jsnframework.php';

$dispatcher		= version_compare($JVersion->RELEASE, '3.0', '<') ? JDispatcher::getInstance() : JEventDispatcher::getInstance();
$jsnframework	= new PlgSystemJSNFramework($dispatcher);

$jsnframework->onAfterInitialise();

// Initialize variables
$root		= '/' . trim(str_replace('\\', '/', $app->input->getVar('root')), '/');
$handler	= $app->input->getVar('handler');
$element	= $app->input->getVar('element');
$selected	= trim(str_replace('\\', '/', $app->input->getVar('current')), '/');

// Check if root is outside document root or Joomla directory
if ($root != '/' AND strpos(realpath(dirname(JPATH_BASE)), realpath(JPATH_ROOT . $root)) !== false)
{
	// Hacking attemp, die immediately
	jexit('Invalid root directory!');
}

// Get allowed file extensions from com_media's configuration
$params = JComponentHelper::getParams('com_media');
$regEx  = '^[a-zA-Z0-9\-_]+\.(' . str_replace(',', '|', $params->get('upload_extensions')) . ')$';

// Execute requested task
switch ($task = $app->input->getCmd('task'))
{
	case 'post.upload':
		// Check if uploaded file is image?
		if (JSNVersion::isJoomlaCompatible('2.5'))
		{
			// Load com_media's helper class
			require_once JPATH_ROOT . '/administrator/components/com_media/helpers/media.php';

			if ( ! @MediaHelper::canUpload($_FILES['file'], $err))
			{
				jexit(JText::_('JSN_EXTFW_GENERAL_UPLOADED_FILE_TYPE_NOT_SUPPORTED'));
			}
		}
		elseif ( ! @JHelperMedia::canUpload($_FILES['file']))
		{
			jexit(JText::_('JSN_EXTFW_GENERAL_UPLOADED_FILE_TYPE_NOT_SUPPORTED'));
		}

		// Move uploaded file to target directory
		if ( ! JFile::upload($_FILES['file']['tmp_name'], JPATH_ROOT . $root . '/' . $_FILES['file']['name']))
		{
			jexit(JText::_('JSN_EXTFW_GENERAL_MOVE_UPLOAD_FILE_FAIL'));
		}

		exit;
	break;

	case 'get.directory':
		// Get directory list
		$list = JFolder::folders(JPATH_ROOT . $root);

		// Initialize return value
		foreach ($list AS $k => $v)
		{
			$id = $root . '/' . str_replace(array('/', '\\'), '-DS-', trim($v, '/\\'));
			$list[$k] = array('attr' => array('rel' => 'folder', 'id' => $id), 'data' => $v, 'state' => 'closed');
		}

		// Set necessary header
		header('Content-type: application/json; charset=utf-8');
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Pragma: no-cache");

		// Send response back
		jexit(json_encode($list));
	break;

	case 'get.media':
	default:
		if ($selected && ! file_exists(JPATH_ROOT . "{$root}/{$selected}"))
		{
			$selected = null;
		}

		if ($selected)
		{
			// Initialize current directory
			if ($current = str_replace('/', '-DS-', trim(str_replace($root, '', dirname($selected)), '/')))
			{
				for ($i = 0, $n = count($tmp = explode('-DS-', $current)); $i < $n; $i++)
				{
					is_array($current) OR $current = array();

					for ($j = 0; $j <= $i; $j++)
					{
						$current[$i][] = $tmp[$j];
					}

					$current[$i] = implode('-DS-', $current[$i]);
				}
			}
		}

		if (empty($selected) OR ! isset($current))
		{
			// Get media list
			$media = JFolder::files(JPATH_ROOT . $root, $regEx);

			// Initialize image URI
			foreach ($media AS $k => $v)
			{
				$media[$k] = str_replace(
					'\\',
					'/',
					str_replace('/plugins/system/jsnframework/libraries/joomlashine/choosers/media', '', trim(JURI::root(), '/')) . $root . '/' . $v
				);
			}
		}
	break;
}

if ($task != 'get.media')
{
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo JText::_('JSN_EXTFW_CHOOSERS_MEDIA'); ?></title>
	<meta name="author" content="JoomlaShine Team">
	<?php
	if (JSNVersion::isJoomlaCompatible('3.0'))
	{
	?>
	<link href="../../../../../../../media/jui/css/bootstrap.min.css" rel="stylesheet" />
	<link href="../../../../assets/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.9.0.custom.css" rel="stylesheet" />
	<?php
	}
	else
	{
	?>
	<link href="../../../../assets/3rd-party/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="../../../../assets/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
	<?php
	}
	?>
	<link href="../../../../assets/3rd-party/jquery-layout/css/layout-default-latest.css" rel="stylesheet" />
	<link href="../../../../assets/joomlashine/css/jsn-gui.css" rel="stylesheet" />
	<!-- Load HTML5 elements support for IE6-8 -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<?php
	if (JSNVersion::isJoomlaCompatible('3.0'))
	{
	?>
	<script src="../../../../<?php echo JSNVersion::isJoomlaCompatible('3.2') ? 'assets/3rd-party/jquery/jquery.min.js' : '../../../media/jui/js/jquery.js'; ?>"></script>
	<script src="../../../../assets/3rd-party/jquery-ui/js/jquery-ui-1.9.0.custom.min.js"></script>
	<?php
	}
	else
	{
	?>
	<script src="../../../../assets/3rd-party/jquery/jquery-1.7.1.min.js"></script>
	<script src="../../../../assets/3rd-party/jquery-ui/js/jquery-ui-1.8.16.custom.min.js"></script>
	<?php
	}
	?>
	<script src="../../../../assets/3rd-party/jquery-layout/js/jquery.layout-latest.js"></script>
	<script src="../../../../assets/3rd-party/jquery-jstree/jquery.jstree.js"></script>
	<script src="../../../../assets/3rd-party/ajax-upload/ajaxupload.js"></script>
	<style type="text/css">
		body {
			border: 1px solid #bbb;
		}
		.ui-layout-pane-west,
		.ui-layout-pane.outer-center,
		.ui-layout-pane.inner-center,
		.ui-layout-pane.ui-layout-south {
			border: 0;
			padding: 0;
		}
		.ui-layout-pane-west > div,
		.ui-layout-pane.inner-center > div,
		.ui-layout-pane.ui-layout-south > div {
			padding: 10px;
		}
		.ui-layout-pane.inner-center {
			text-align: center;
		}
		.jsn-bootstrap ul.thumbnails {
			margin: 0;
		}
		.jsn-master .jsn-bootstrap ul.thumbnails li {
			margin: 0 10px 10px 0;
		}
		.jsn-bootstrap ul.thumbnails li a.thumbnail {
			width: 128px;
			height: 96px;
			overflow: auto;
		}
		.jsn-bootstrap ul.thumbnails li a.thumbnail .icon-file {
			display: block;
			margin: 24px auto;
		}
		.jsn-bootstrap ul.thumbnails li a.thumbnail:hover .icon-file {
			text-decoration: none;
		}
		#jsn-media-upload-form {
			margin: 4px 0;
			text-align: center;
		}
		#jsn-media-upload-status {
			display: none;
		}
		#jsn-media-upload-status-message {
			margin: 0;
		}
	</style>
	<script type="text/javascript">
		$(document).ready(function() {
			var self = {};

			// Get necessary elements
			self.$tree = $('#jsn-media-directory-tree');
			self.$media = $('#jsn-media-image-list');
			self.$mask = $('#jsn-loading-mask');

			self.$uForm = $('#jsn-media-upload-form');
			self.$uStat = $('#jsn-media-upload-status');
			self.$uMsg = $('#jsn-media-upload-status-message');

			// Initialize layout
			$(document.body).bind('initialized', function() {
				$(document.body).children('.panelize')
				.css({
					width: ($(window).innerWidth() - 2) + 'px',
					height: ($(window).innerHeight() - 2) + 'px'
				})
				.layout({
					center__paneSelector: '.outer-center',
					center__childOptions: {
						center__paneSelector: '.inner-center',
						south: {
							size: 58
						}
					},
					west: {
						size: 'auto',
						minSize: 250
					}
				});

				// Move mask elements to language editor form
				self.$mask.appendTo(self.$media.parent()).children().hide();

				// Handle window resize event
				$(window).resize(function() {
					// Mask window for reloading
					self.$mask.appendTo(document.body).children().eq(0).show().next().css({
						top: '50%',
						left: '50%'
					}).show();

					// Update variables
					document.JSNMediaReloadForm.current.value = document.JSNMediaReloadForm.root.value + '/something';
					document.JSNMediaReloadForm.root.value = root;

					// Reload the media selector window to re-initialize layout
					document.JSNMediaReloadForm.submit();
				});

				self.initialized = true;
			});

			// Initialize necessary variables for browsing and selecting image
			var	server = '<?php echo trim(JURI::root(), '/'); ?>/index.php',
				root = '<?php echo trim($root, '/'); ?>',

				getActive = function(n) {
					var deep = n.length ? n.attr('id').split('-DS-') : [];
					deep.length || root == '/' || deep.unshift(root);
					return deep.join('/');
				},

				registerEvent = function() {
					// Register event handler for selecting image
					self.$media.find('a.thumbnail').unbind('click').bind(
						'click',
						function() {
							// Generate path to selected image
							var selected = getActive(self.$tree.find('.jstree-clicked').parent()) + '/' + $(this).find('img, span.jsn-file-thumb').attr('alt');

							// Call handler to update
							if (window.parent && typeof window.parent['<?php echo $handler; ?>'] == 'function') {
								window.parent<?php echo '[\'' . $handler . '\'](selected, \'#' . $element . '\')'; ?>;
							}
						}
					);
				},

				getList = function(active) {
					// Mask media list
					self.initialized && self.$mask.children().eq(0).show().next().css({
						top: ((self.$media.parent().outerHeight() / 2) + self.$media.parent().offset().top) + 'px',
						left: ((self.$media.parent().outerWidth() / 2) + self.$media.parent().offset().left) + 'px'
					}).show();

					self.$media.load(
						server,
						'task=get.media&root=' + getActive(active) + '&handler=' + document.JSNMediaReloadForm.handler.value + '&element=' + document.JSNMediaReloadForm.element.value,
						function() {
							registerEvent();

							// Un-mask language file editor form
							self.initialized && self.$mask.children().hide();
						}
					);
				};

			// Initialize directory tree
			self.$tree.jstree({
				core: {
					initially_open: [<?php echo @is_array($current) ? "'" . implode("', '", $current) . "'" : ''; ?>]
				},
				plugins: ['json_data', 'themes', 'ui'],
				json_data: {
					ajax: {
						url: server,
						data: function(n) {
							return {
								task: 'get.directory',
								root: getActive(n)
							};
						},
						error: function() {
							// Trigger initialized event
							self.initialized || $(document.body).trigger('initialized');
						},
						success: function() {
							// Trigger initialized event
							self.initialized || $(document.body).trigger('initialized');
						}
					}
				},
				themes: {
					theme: 'classic',
					url: '<?php echo str_replace('/libraries/joomlashine/choosers/media', '', trim(JURI::root(), '/')); ?>/assets/3rd-party/jquery-jstree/themes/classic/style.css'
				},
				ui: {
					initially_select: [<?php echo @is_array($current) ? "'" . array_pop($current) . "'" : ''; ?>]
				}
			}).bind("select_node.jstree", function(event, data) {
				// Load image files insides a directory
				getList(data.rslt.obj);
			});

			// Initialize Ajax Upload
			window.JSNMediaUpload = new AjaxUpload($('#jsn-media-upload-button'), {
				action: '<?php echo trim(JURI::root(), '/') . '/index.php'; ?>?task=post.upload',
				name: 'file',
				autoSubmit: false,
				onSubmit: function(file, ext) {
					// Hide upload form, show upload status message
					self.$uForm.hide();
					self.$uStat.show();

					// Only allow uploading image file
					if (ext && /<?php echo $regEx; ?>/i.test(file)) {
						self.$uMsg.html(
							'<i class="jsn-icon16 jsn-icon-loading"></i> ' + '<?php echo JText::_('JSN_EXTFW_GENERAL_UPLOADING'); ?>' + ' ' + file
						);

						// Disable upload button
						this.disable();

						// Set additional data to post
						this.setData({
							root: getActive(self.$tree.find('.jstree-clicked').parent())
						});
					} else {
						// Show upload form
						self.$uForm.show();

						// Enable upload button
						this.enable();

						self.$uMsg.html(
							'<i class="jsn-icon16 jsn-icon-remove"></i> ' + '<?php echo JText::_('JSN_EXTFW_GENERAL_UPLOADED_FILE_TYPE_NOT_SUPPORTED'); ?>'
						);

						return false;
					}
				},
				onComplete: function(file, response) {
					// Show upload form
					self.$uForm.show();

					// Enable upload button
					this.enable();

					if (response == '') {
						// Add file to the list
						self.$uMsg.html('<i class="jsn-icon16 jsn-icon-ok"></i> ' + file);

						// Update image list
						getList(self.$tree.find('.jstree-clicked').parent());
					} else {
						// Add error message to the list
						self.$uMsg.html('<i class="jsn-icon16 jsn-icon-remove"></i> ' + response);
					}
				}
			});

			// Register event handle to select media file
			registerEvent();
		});
	</script>
</head>

<body class="jsn-master">
	<div class="panelize">
		<div class="outer-center">
			<div class="inner-center jsn-bootstrap">
				<div id="jsn-media-image-list" class="content-container">
<?php
}
?>
<?php
if (isset($media) AND count($media))
{
?>
					<ul class="thumbnails">
<?php
	foreach ($media AS $file)
	{
		$img = '<span class="jsn-file-thumb icon-file" alt="' . basename($file) . '"></span><span class="caption">' . basename($file) . '</span>';

		if ($dimension = getimagesize(str_replace(JUri::root(), JPATH_ROOT, $file)))
		{
			// Prepare image dimension
			if (($dimension[0] / $dimension[1]) > (128 / 96))
			{
				$width = 128;
				$height = (int) 128 / ($dimension[0] / $dimension[1]);
			}
			elseif (($dimension[0] / $dimension[1]) < (128 / 96))
			{
				$height = 96;
				$width = (int) 96 * ($dimension[0] / $dimension[1]);
			}
			else
			{
				$width = 128;
				$height = 96;
			}

			// Generate image tag
			$img = '<img src="' . $file . '" alt="' . basename($file) . '" width="' . $width . '" height="' . $height . '" />';
		}
?>
						<li><a class="thumbnail" href="javascript:void(0)"><?php echo $img; ?></a>
<?php
	}
?>
					</ul>
<?php
}
else
{
?>
					<div class="alert"><?php echo JText::_('JSN_EXTFW_CHOOSERS_MEDIA_NO_ITEM'); ?></div>
<?php
}
?>
					<form name="JSNMediaReloadForm" action="<?php echo trim(JURI::root(), '/'); ?>/index.php" method="GET">
						<input type="hidden" name="root" value="<?php echo $root; ?>" />
						<input type="hidden" name="handler" value="<?php echo $handler; ?>" />
						<input type="hidden" name="element" value="<?php echo $element; ?>" />
						<input type="hidden" name="current" value="<?php echo $selected; ?>" />
					</form>
<?php
if ($task != 'get.media')
{
?>
				</div>
			</div>
			<div class="ui-layout-south">
				<div>
					<div class="jsn-bootstrap">
						<div id="jsn-media-upload-status" class="alert">
							<a class="close" title="<?php echo JText::_('JSN_EXTFW_GENERAL_CLOSE'); ?>" href="javascript:void(0);" onclick="$(this).parent().hide();">×</a>
							<p id="jsn-media-upload-status-message"></p>
						</div>
					</div>
					<form id="jsn-media-upload-form" action="#" method="POST" onsubmit="<?php echo 'return false;'; ?>">
						<input id="jsn-media-upload-button" type="file" size="35" />
						<button class="btn" onclick="JSNMediaUpload.submit();"><?php echo JText::_('JSN_EXTFW_GENERAL_UPLOAD'); ?></button>
					</form>
				</div>
			</div>
		</div>
		<div class="ui-layout-west">
			<div id="jsn-media-directory-tree" class="content-container"><?php echo JText::_('JSN_EXTFW_GENERAL_LOADING'); ?></div>
		</div>
	</div>
	<div id="jsn-loading-mask">
		<div class="jsn-modal-overlay" style="display: block; z-index: 1000;"></div>
		<div class="jsn-modal-indicator" style="display: block;"></div>
	</div>
</body>
</html>
<?php
}
