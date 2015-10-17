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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get input object
$input = JFactory::getApplication()->input;

// Get keys for first section and group
$key		= array_keys($config);
$fSection	= array_shift($key);

$key		= array_keys($config[$fSection]->groups);
$fGroup		= array_shift($key);

// Get requested section and group keys
$rSection	= $input->getCmd('s', $fSection);
$rGroup		= $input->getCmd('g', $fGroup);

if ($input->getCmd('tmpl') != 'component')
{
?>
<div class="jsn-page-configuration">
	<div class="jsn-bootstrap jsn-bgpattern pattern-sidebar">
		<div>
<?php
	require dirname(__FILE__) . '/' . str_replace('.php', '_menu.php', basename(__FILE__));
?>
			<div id="jsn-config-form" class="jsn-page-content">
				<div>
<?php
}
?>
					<div id="jsn-<?php echo $config[$rSection]->groups[$rGroup]->name; ?>">
						<h2 class="jsn-section-header"><?php echo JText::_($config[$rSection]->groups[$rGroup]->label); ?></h2>
<?php
// Start output buffering
ob_start();

$blocks	= ($hasTabs = isset($config[$rSection]->groups[$rGroup]->tabs))
		? $config[$rSection]->groups[$rGroup]->tabs
		: $config[$rSection]->groups[$rGroup]->fieldsets;

if ($hasTabs)
{
	$tabs = & $blocks;
	require dirname(__FILE__) . '/' . str_replace('.php', '_tabs.php', basename(__FILE__));
}
else
{
	$fieldsets = & $blocks;
	require dirname(__FILE__) . '/' . str_replace('.php', '_form.php', basename(__FILE__));
}

// Get output buffering content
$form = ob_get_clean();

// Generate required hidden input
$hidden = '
							<input type="hidden" name="option" value="' . $input->getCmd('option') . '" />
							<input type="hidden" name="view" value="' . $input->getCmd('view') . '" />
							<input type="hidden" name="s" value="' . $rSection . '" />
							<input type="hidden" name="g" value="' . $rGroup . '" />
							<input type="hidden" name="task" value="" />
							' . JHtml::_('form.token') . '
';

// Wrap content inside <form> and </form> if necessary
if (strpos($form, '<form ') === false AND strpos($form, '</form>') === false)
{
?>
						<form name="JSNConfigForm" autocomplete="off" action="<?php echo JRoute::_('index.php'); ?>" method="POST" class="form-horizontal" onsubmit="return false;">
<?php
	echo $form . $hidden;
?>
						</form>
<?php
}
else
{
	echo str_replace('</form>', $hidden . '</form>', $form);
}
?>
						<div class="jsn-form-validation-failed jsn-box-shadow-medium alert alert-error hide">
							<span></span>
							<a href="javascript:void(0);" title="<?php echo JText::_('JSN_EXTFW_GENERAL_CLOSE'); ?>" class="close" onclick="jQuery(this).parent().addClass('hide');">×</a>
						</div>
					</div>
<?php
// If this is ajax request, exit immediately to prevent loading of extra assets
if ($input->getInt('ajax') == 1)
{
	exit;
}

if ($input->getCmd('tmpl') != 'component')
{
?>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
</div>
<?php
}
