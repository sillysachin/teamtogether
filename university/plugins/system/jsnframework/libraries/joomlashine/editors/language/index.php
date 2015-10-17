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
$task	= $app->input->getCmd('task');
$com	= $app->input->getCmd('component');
$client	= $app->input->getCmd('client', $task == 'post.save' ? null : 'admin');
$lang	= $app->input->getCmd('lang', $task == 'post.save' ? null : 'en-GB');
$file	= $app->input->getCmd('file');
$config	= JFactory::getConfig();

// Validate variables
! empty($com) OR die(JText::_('JSN_EXTFW_EDITORS_LANG_MISSING_COMPONENT'));

// Execute requested task
switch ($task)
{
	case 'post.save':
		// Validate variables
		foreach (array('com', 'client', 'lang', 'file') AS $var)
		{
			if ( ! ${$var})
			{
				jexit('FAIL:' . JText::sprintf('JSN_EXTFW_EDITORS_LANG_MISSING_VARIABLE', $var));
			}
		}

		// Get posted text translation
		if ( ! isset($_POST['translation']) OR ! is_array($_POST['translation']) OR ! count($_POST['translation']))
		{
			jexit('FAIL:' . JText::_('JSN_EXTFW_EDITORS_LANG_MISSING_TRANSLATION'));
		}

		// Generate new language file content
		$text = array();

		foreach ($_POST['translation'] AS $k => $v)
		{
			$text[] = $k . '="' . $v . '"';
		}

		$text = implode("\n", $text);

		// Write new content to language file
		$file = JPATH_ROOT . ($client == 'admin' ? '/administrator' : '') . "/language/{$lang}/{$file}";

		if ( ! @JFile::write($file, $text))
		{
			jexit('FAIL:' . JText::sprintf('JSN_EXTFW_EDITORS_LANG_SAVE_FAIL', str_replace(JPATH_ROOT, 'JOOMLA_ROOT', $file)));
		}

		jexit(JText::sprintf('JSN_EXTFW_EDITORS_LANG_FILE_SAVED', basename($file)));
	break;

	case 'post.revert':
		// Validate variables
		foreach (array('com', 'client', 'lang') AS $var)
		{
			if ( ! ${$var})
			{
				jexit('FAIL:' . JText::sprintf('JSN_EXTFW_EDITORS_LANG_MISSING_VARIABLE', $var));
			}
		}

		// Generate list of file to override
		if ($file)
		{
			$files[] = JPATH_ROOT . "/administrator/components/{$com}/language/{$client}/{$lang}/{$file}";
		}
		else
		{
			$files = glob(JPATH_ROOT . "/administrator/components/{$com}/language/{$client}/{$lang}/{$lang}.*.ini");
		}

		// Override all file in Joomla's language folder with file from component folder
		foreach ($files AS $file)
		{
			// Generate path to destination file
			$to = JPATH_ROOT . ($client == 'admin' ? '/administrator' : '') . "/language/{$lang}/" . basename($file);

			// Overwrite user edited language file with the original one
			if ( ! @JFile::copy($file, $to))
			{
				$fail[] = str_replace(JPATH_ROOT, 'JOOMLA_ROOT', $to);
			}
			else
			{
				// Set file modification time to same as original file so we can track change when needed
				touch($to, filemtime(preg_replace('/(\r|\n)/', '', $file)));
			}
		}

		if (isset($fail))
		{
			jexit('FAIL:' . JText::sprintf('JSN_EXTFW_EDITORS_LANG_REVERT_FAIL', '<ul style="margin-bottom:0"><li>' . implode('</li><li>', $fail) . '</li></ul>'));
		}

		jexit('SUCCESS');
	break;

	case 'get.file':
	default:
		if (empty($task))
		{
			// Get list of language files
			$files['admin']	= glob(JPATH_ROOT . "/administrator/components/{$com}/language/admin/{$lang}/{$lang}.*.ini");
			$files['site']	= glob(JPATH_ROOT . "/administrator/components/{$com}/language/site/{$lang}/{$lang}.*.ini");
		}

		// Read content of selected language file to array
		if ($file AND is_readable($file = JPATH_ROOT . ($client == 'admin' ? '/administrator' : '') . "/language/{$lang}/{$file}"))
		{
			if ($task == 'get.file')
			{
				// Read language file content
				$lines = file(preg_replace('/(\r|\n)/', '', $file));
			}
			else
			{
				// Generate full path for the selected file in component directory
				$file = JPATH_ROOT . "/administrator/components/{$com}/language/{$client}/{$lang}/" . basename($file);
			}
		}
		elseif ( ! $file)
		{
			$file = count($files[$client]) ? $files[$client][0] : null;
		}
	break;
}

if ($task != 'get.file')
{
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo JText::_('JSN_EXTFW_EDITORS_LANG'); ?></title>
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
		body {
			border: 1px solid #bbb;
		}
		.ui-layout-pane-west,
		.ui-layout-pane.outer-center,
		.ui-layout-pane.inner-center,
		.ui-layout-pane.ui-layout-north {
			border: 0;
			padding: 0;
		}
		.ui-layout-pane-west > div,
		.ui-layout-pane.inner-center > div,
		.ui-layout-pane.ui-layout-north > div {
			padding: 10px;
		}
		.ui-layout-pane.inner-center {
			text-align: center;
		}
		.ui-layout-pane.ui-layout-north {
			overflow: hidden;
			text-align: center;
		}
		#jsn-language-file-list ul > li > ul > li > a > ins {
			display: none;
		}
		#jsn-language-file-list .jstree-classic .jstree-clicked {
			background: #E7F4F9;
			border: 1px solid #E7F4F9;
			padding: 0 2px 0 1px;
		}
		#jsn-language-file-editor-filter {
			position: relative;
		}
		.jsn-bootstrap #jsn-language-file-editor-filter input[type="text"] {
			margin-bottom: 0;
		}
		#jsn-language-file-editor-filter a {
			position: absolute;
			display: none;
			top: 50%;
			margin-right: 9px;
		}
		#jsn-language-file-editor-message {
			display: none;
			position: fixed;
			top: 82px;
			left: 50%;
		}
		#jsn-language-file-editor-message span.message {
			white-space: nowrap;
		}
	</style>
	<script type="text/javascript">
		$(document).ready(function() {
			var	self = {initialized: false},
				filterTranslation = function(filter) {
					// Check if user really change the filter text
					self.lastFilter = self.lastFilter || null;

					if (self.lastFilter != filter) {
						// Cancel scheduled filtering action
						!self.timer || clearTimeout(self.timer);

						// Show loading indicator
						self.$filter
						.next().css({
							display: 'block',
							right: ((self.$filter.parent().outerWidth() - self.$filter.outerWidth()) / 2) + 'px',
							'margin-top': '-' + (self.$filter.next().outerWidth() / 2) + 'px'
						})
						.next().css('display', 'none');

						self.timer = setTimeout(function() {
							var field, regex = new RegExp(filter, 'i');

							for (var i in document.languageEditor) {
								if ((field = document.languageEditor[i]) && field.nodeName == 'INPUT' && field.type == 'text') {
									if (filter != '') {
										if (field.value.match(regex)) {
											$(field).parent().css('display') == 'block' || $(field).parent().show();
										} else {
											$(field).parent().css('display') == 'none' || $(field).parent().hide();
										}
									} else {
										$(field).parent().css('display') == 'block' || $(field).parent().show();
									}
								}
							}

							// Hide loading indicator
							self.$filter.next().css('display', 'none');

							// Show/hide clear field icon
							if (filter != '') {
								self.$filter.next().next().css({
									display: 'block',
									right: ((self.$filter.parent().outerWidth() - self.$filter.outerWidth()) / 2) + 'px',
									'margin-top': '-' + (self.$filter.next().next().outerWidth() / 2) + 'px'
								});
							} else {
								self.$filter.next().next().css('display', 'none');
							}

							// Store last filter text
							self.lastFilter = filter;
						}, 500);
					}
				};

			// Get necessary elements
			self.$list = $("#jsn-language-file-list");
			self.$editor = $('#jsn-language-file-editor');
			self.$form = $(document.languageEditor);
			self.$filter = $('#jsn-language-file-editor-filter input');
			self.$mask = $('#jsn-loading-mask');
			self.$msg = self.$editor.find('#jsn-language-file-editor-message');

			// Initialize layout
			$(document.body).bind('initialized', function() {
				$(document.body).children('.panelize')
				.css({
					width: ($(window).innerWidth() - 2) + 'px',
					height: ($(window).innerHeight() - 2) + 'px'
				})
				.layout({
					enableCursorHotkey: false,
					center__paneSelector: '.outer-center',
					center__childOptions: {
						center__paneSelector: '.inner-center',
						closable: false,
						resizable: false,
						north: {
							size: 50
						}
					},
					west: {
						size: 'auto'
					}
				});

				// Move mask elements to language editor form
				self.$mask.appendTo(self.$editor).children().hide();

				// Handle window resize event
				$(window).resize(function() {
					// Mask window for reloading
					self.$mask.appendTo(document.body).children().eq(0).show().next().css({
						top: '50%',
						left: '50%'
					}).show();

					// Remove all attached handler for submit event of language editor form
					self.$form.unbind('submit').unbind('formSubmitted');

					// Disable all input fields
					self.$form.find('input[type="text"]').attr('disabled', 'disabled');

					// Reload the language editor window to re-initialize layout
					document.languageEditor.method = 'GET';
					document.languageEditor.submit();
				});

				self.initialized = true;
			});

			// Initialize language file list
			self.$list.jstree({
				core: {
					initially_open: ['client-<?php echo $client; ?>']
				},
				plugins: ['html_data', 'themes', 'ui'],
				themes: {
					theme: 'classic',
					url: '<?php echo str_replace('/libraries/joomlashine/editors/language', '', trim(JURI::root(), '/')); ?>/assets/3rd-party/jquery-jstree/themes/classic/style.css'
				},
				ui: {
					initially_select: [<?php echo $file ? '"' . md5(str_replace('\\', '/', $file)) . '"' : ''; ?>]
				}
			}).bind("select_node.jstree", function(event, data) {
				if (data.rslt.obj.attr('id').indexOf('client-') > -1) return;

				// Generate necessary variables for requesting language file content
				var	client = data.rslt.obj.parent().parent().attr('id').replace('client-', ''),
					file = $.trim(data.rslt.obj.children('a').text()),
					link = window.location.href.replace(/[\?&](client|file|task)=[^&]+/, '');

				// Show loading indicator
				data.rslt.obj.find('i.jsn-icon16').addClass('jsn-icon-loading');

				// Mask language file editor form
				self.initialized && self.$mask.children().eq(0).show().next().css({
					top: ((self.$editor.parent().outerHeight() / 2) + self.$editor.parent().offset().top) + 'px',
					left: ((self.$editor.parent().outerWidth() / 2) + self.$editor.parent().offset().left) + 'px'
				}).show();

				// Check if user has changed the current language file in editor form
				if (self.editing && self.editing != self.$form.serialize()) {
					// Ask user whether they want to save the change or not?
					if (confirm("<?php echo JText::_('JSN_EXTFW_EDITORS_LANG_SAVE_CHANGE_CONFIRM'); ?>".replace(/%s/, $.trim(self.$last.children('a').text())))) {
						self.$form.trigger('submit');
					}
				}

				self.$form.load(
					link + (link.indexOf('?') < 0 ? '?' : '&') + 'client=' + client + '&file=' + file + '&task=get.file',
					function() {
						self.editing = self.$form.serialize();
						self.$last = data.rslt.obj;

						// Reset text translation filter
						self.$filter.attr('value', '').trigger('blur');

						// Hide loading indicator
						data.rslt.obj.find('i.jsn-icon16').removeClass('jsn-icon-loading');

						// Un-mask language file editor form
						self.initialized && self.$mask.children().hide();

						// Trigger initialized event
						self.initialized || $(document.body).trigger('initialized');
					}
				);
			});

			// Setup event handler for text translation filter field
			self.$filter
			.bind('keyup', function(event) {
				filterTranslation(this.value);
			})
			.bind('change', function() {
				filterTranslation(this.value);
			})
			.bind('focus', function() {
				this.value != '<?php echo JText::_('JSN_EXTFW_EDITORS_LANG_FILTER_TRANSLATION'); ?>' || (this.value = '');
			})
			.bind('blur', function() {
				this.value != '' || (this.value ='<?php echo JText::_('JSN_EXTFW_EDITORS_LANG_FILTER_TRANSLATION'); ?>');
			});

			// Setup button to clear text translation filter
			self.$filter.next().next().bind('click', function() {
				self.$filter.attr('value', '').trigger('change').trigger('blur');
			});

			// Setup handler for submit event of language editor form
			self.$form.bind('submit', function(event) {
				event.preventDefault();

				$.ajax({
					url: document.languageEditor.action + '?task=post.save',
					data: self.$form.serialize(),
					type: document.languageEditor.method,
					complete: function(jqXHR, textStatus) {
						if (jqXHR.responseText.match(/^FAIL:/)) {
							self.$msg.removeClass('alert-success').addClass('alert-error');
						} else {
							self.$msg.removeClass('alert-error').addClass('alert-success');
						}

						// Set returned message then show alert
						self.$msg.children('span.message').text(jqXHR.responseText.replace(/^(SUCCESS|FAIL):/, '')).parent().show().css({
							left: ((self.$editor.outerWidth() / 2) + self.$editor.offset().left) + 'px',
							'margin-left': '-' + (self.$msg.outerWidth() / 2) + 'px'
						});

						// If save success, schedule the alert to auto-hide
						self.$msg.hasClass('alert-error') || setTimeout(function() { self.$msg.fadeOut('slow'); }, 1500);

						// Get name of saved language file
						var file = jqXHR.responseText.match(/[a-z][a-z]-[A-Z][A-Z]\.[^\.]+(\.sys)?\.ini/);

						if (file[0] == $.trim(self.$last.children('a').text())) {
							// Update form data serialization
							self.editing = self.$form.serialize();
						}

						// Refresh the language manager page
						if (self.$modal) {
							var $body = self.$modal.parent();
							while ($body[0].nodeName != 'BODY') {
								$body = $body.parent();
							}
							$body.find('#jsn-config-menu #linklangs').trigger('click');
						}

						// Fire custom form submitted event
						self.$form.trigger('formSubmitted');
					}
				});

				return false;
			});

			// Check if page is rendered inside a modal
			if (window.parent) {
				// Search the iframe element containing this view
				var $iframes = window.parent.jQuery('iframe');
				for (var i = $iframes.length - 1; i >= 0; i--) {
					if (window.location.href.indexOf($iframes[i].src) > -1) {
						self.$modal = $iframes.eq(i).parent();
						break;
					}
				}

				// Setup save action
				if (self.$modal) {
					var $oldBtn = self.$modal.next().find('button.btn-save');
					$oldBtn.length && $oldBtn.remove();

					$('<button class="btn-save ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><?php echo JText::_('JSN_EXTFW_GENERAL_SAVE'); ?></button>')
					.click(function(event) {
						var $target = $(event.target);

						// Hide buttons
						$target.removeClass('btn-save ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only').addClass('jsn-loading').next().hide();

						// Handle custom form submitted event
						self.$form.unbind('formSubmitted').bind('formSubmitted', $.proxy(function() {
							// Restore buttons
							$target.removeClass('jsn-loading').addClass('btn-save ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only').next().show();
						}, this));

						// Submit the language editor form
						self.$form.trigger('submit');
					})
					.prependTo(self.$modal.next().children());
				}
			}
		});
	</script>
</head>

<body class="jsn-master">
	<div class="panelize">
		<div class="outer-center jsn-bootstrap">
			<div class="inner-center">
				<div id="jsn-language-file-editor" class="content-container">
					<div id="jsn-language-file-editor-message" class="alert jsn-box-shadow-medium">
						<a class="close" title="<?php echo JText::_('JSN_EXTFW_GENERAL_CLOSE'); ?>" href="javascript:void(0);" onclick="$(this).parent().hide();">×</a>
						<span class="message"></span>
					</div>
					<form name="languageEditor" action="<?php echo trim(JURI::root(), '/'); ?>/index.php" method="POST">
<?php
}

if (@is_array($lines) AND count($lines))
{
	// Prevent duplication of text translation
	$processed = array();
	$fields = array();

	foreach (array_reverse($lines) AS $line)
	{
		if ( ! empty($line) AND ! preg_match('/^\s*;/', $line) AND preg_match('/^\s*([^=]+)="([^\r\n]+)"\s*$/', $line, $match) AND ! in_array($match[1], $processed))
		{
			$fields[] = '
						<div class="control-group">
							<input name="translation[' . $match[1] . ']" class="jsn-input-xxlarge-fluid" type="text" value="' . htmlspecialchars($match[2], ENT_QUOTES, 'UTF-8', false) . '" />
						</div>';

			$processed[] = $match[1];
		}
	}

	echo implode(array_reverse($fields));
}
elseif (isset($lines))
{
?>
						<div class="alert"><?php echo JText::sprintf('JSN_EXTFW_EDITORS_LANG_FILE_EMPTY', str_replace(JPATH_ROOT, 'JOOMLA_ROOT', $file)); ?></div>
<?php
}
else
{
?>
						<div class="alert alert-error"><?php echo JText::sprintf('JSN_EXTFW_EDITORS_LANG_FILE_NOT_FOUND', str_replace(JPATH_ROOT, 'JOOMLA_ROOT', $file)); ?></div>
<?php
}
?>
						<input type="hidden" name="component" value="<?php echo $com; ?>" />
						<input type="hidden" name="client" value="<?php echo $client; ?>" />
						<input type="hidden" name="lang" value="<?php echo $lang; ?>" />
						<input type="hidden" name="file" value="<?php echo basename($file); ?>" />
<?php
if ($task != 'get.file')
{
?>
					</form>
				</div>
			</div>
			<div class="ui-layout-north">
				<div class="jsn-bgpattern pattern-sidebar">
					<form id="jsn-language-file-editor-filter" onsubmit="return false;">
						<input class="jsn-input-large-fluid" type="text" value="<?php echo JText::_('JSN_EXTFW_EDITORS_LANG_FILTER_TRANSLATION'); ?>" />
						<a href="javascript:void(0)" class="jsn-icon16 jsn-icon-loading"></a>
						<a href="javascript:void(0)" class="icon16 icon-remove"></a>
					</form>
				</div>
			</div>
		</div>
		<div class="ui-layout-west">
			<div id="jsn-language-file-list" class="content-container">
				<ul>
<?php
foreach ($files AS $client => $list)
{
?>
					<li id="client-<?php echo $client; ?>">
						<a href="javascript:void(0)"><?php echo $client == 'admin' ? JText::_('JADMINISTRATOR') : JText::_('JSITE'); ?></a>
						<ul>
<?php
	foreach ($list AS $file)
	{
		// Check if language file in Joomla's language directory is writable?
		$realFile	= JPATH_ROOT . ($client == 'admin' ? '/administrator' : '') . "/language/{$lang}/" . basename($file);
		$isWritable	= file_exists($realFile)
					? is_writable($realFile)
					: is_writable(dirname($realFile));

		// If file is not writable, generate a warning icon
		if ( ! $config->get('ftp_enable') AND ! $isWritable)
		{
			$isWritable	= '<a href="javascript:void(0)" title="'
						. JText::sprintf('JSN_EXTFW_EDITORS_LANG_FILE_NOT_WRITABLE', str_replace(JPATH_ROOT, 'JOOMLA_ROOT', $realFile))
						. '"><i class="jsn-icon16 jsn-icon-warning-sign"></i> ' . basename($file) . '</a>';
		}
		else
		{
			$isWritable	= '<a href="javascript:void(0)" title="'
						. JText::_('JSN_EXTFW_EDITORS_LANG_CLICK_TO_EDIT')
						. '"><i class="jsn-icon16 jsn-icon-file"></i> ' . basename($file) . '</a>';
		}
?>
							<li id="<?php echo md5(str_replace('\\', '/', $file)); ?>"><?php echo $isWritable; ?></li>
<?php
	}
?>
						</ul>
					</li>
<?php
}
?>
				</ul>
			</div>
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
