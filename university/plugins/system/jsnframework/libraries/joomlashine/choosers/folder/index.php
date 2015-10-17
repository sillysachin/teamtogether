<?php
/**
 * @version    $Id: index.php 18208 2012-11-09 08:15:37Z cuongnm $
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
if (!JFactory::getUser()->authorise('core.manage', $app->input->getCmd('component')))
{
	jexit('Please login to administration panel first!');
}


jimport('joomla.filesystem.folder');

// Get Joomla version
$JVersion = new JVersion;

// Initialize JSN Framework
require_once JPATH_ROOT . '/plugins/system/jsnframework/jsnframework.php';

$dispatcher		= version_compare($JVersion->RELEASE, '3.0', '<') ? JDispatcher::getInstance() : JEventDispatcher::getInstance();
$jsnframework	= new PlgSystemJSNFramework($dispatcher);

$jsnframework->onAfterInitialise();

// Initialize variables
$root		= '/' . trim($app->input->getVar('root'), '/');
$handler	= $app->input->getVar('handler');
$element	= $app->input->getVar('element');
// Check if root is outside document root or Joomla directory
if ($root != '/' AND strpos(realpath(dirname(JPATH_BASE)), realpath(JPATH_ROOT . $root)) !== false)
{
	// Hacking attemp, die immediately
	jexit('Invalid root directory!');
}
// Execute requested task
switch ($task = $app->input->getCmd('task'))
{
	case 'get.directory':
		// Get directory list
		$list = JFolder::folders(JPATH_ROOT . $root);

		// Initialize return value
		foreach ($list AS $k => $v)
		{
			$id = str_replace(array('/', '\\'), '-DS-', trim($root . '/' . $v, '/\\'));
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
		if (empty($task) AND $current = $app->input->getVar('current'))
		{
			// Initialize current directory
			$current = str_replace(array('/', '\\'), '-DS-', trim($current, '/'));

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
	break;
}
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
		<style type="text/css">
			#jsn-media-directory-tree {
				border: 0;
				height: 465px;
				overflow: auto;
			}
			.content-container {
				padding: 6px 9px;
			}
		</style>
	</head>

	<body class="jsn-bootstrap">
		<div id="jsn-media-chooser" class="jsn-master">
			<div class="pane ui-layout-west">
				<div id="jsn-media-directory-tree" class="content-container"></div>
			</div>
		</div>
		<script type="text/javascript">
			(function() {
				// Initialize necessary variables for browsing and selecting image
				var server = '<?php echo trim(JURI::root(), '/'); ?>/index.php',
				root = '<?php echo $root; ?>',
				getActive = function(n) {
					var deep = n.length ? n.attr('id').split('-DS-') : [];
					deep.length || root == '/' || deep.unshift(root);
					return deep.join('/');
				};

				// Initialize directory tree
				$('#jsn-media-directory-tree').jstree({
					core: {
						initially_open: [<?php echo is_array($current) ? "'" . implode("', '", $current) . "'" : ''; ?>]
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
							}
						}
					},
					themes: {
						theme: 'classic',
						url: '<?php echo str_replace('/libraries/joomlashine/choosers/folder', '', trim(JURI::root(), '/')); ?>/assets/3rd-party/jquery-jstree/themes/classic/style.css'
					},
					ui: {
						initially_select: [<?php echo is_array($current) ? "'" . array_pop($current) . "'" : ''; ?>]
					}
				}).bind("select_node.jstree", function(event, data) {
					if ($(data.args[0]).hasClass("jstree-clicked")) {
						var selected = getActive($('#jsn-media-directory-tree').find('.jstree-clicked').parent());
						parent<?php echo '[\'' . $handler . '\'](selected, \'#' . $element . '\')'; ?>;
					}
				});
			})();
		</script>
	</body>
</html>
