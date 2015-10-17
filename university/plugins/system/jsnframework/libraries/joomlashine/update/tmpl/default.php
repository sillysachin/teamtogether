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

// Initialize product info
$name		= preg_replace('/JSN\s*/i', '', JText::_($info->name));
$edition	= JSNUtilsText::getConstant('EDITION');

// Get input object
$input = JFactory::getApplication()->input;
?>
<div class="jsn-page-update">
	<div class="jsn-page-content jsn-rounded-large jsn-box-shadow-large jsn-bootstrap">
		<span id="jsn-update-cancel"><a class="jsn-link-action" href="<?php echo JRoute::_('index.php?option=' . $input->getCmd('option')); ?>">
			<?php echo JText::_('JCANCEL'); ?></a></span>
		<h1><?php echo JText::sprintf('JSN_EXTFW_UPDATE_PAGE_HEAD', $name, $edition); ?></h1>
<?php
if ( ! JSNVersion::isJoomlaCompatible(JSN_FRAMEWORK_REQUIRED_JOOMLA_VER) OR ! JSNVersion::checkCompatibility(JSNUtilsText::getConstant('IDENTIFIED_NAME'), JSNUtilsText::getConstant('VERSION')))
{
	// Show a message indicating user that their JoomlaShine product is no longer compatible with the installed JoomlaShine extension framework
?>
		<div class="alert alert-danger"><?php echo JText::_('JSN_EXTFW_GENERAL_INCOMPATIBLE_ALERT'); ?></div>
<?php
}
?>
		<div id="jsn-update-action">
			<p><?php echo JText::sprintf('JSN_EXTFW_UPDATE_PAGE_DESC', $name); ?></p>
			<div class="alert alert-info">
				<p><span class="label label-info"><?php echo JText::_('JSN_EXTFW_GENERAL_IMPORTANT_NOTE'); ?></span></p>
				<ul><li><?php echo JText::sprintf('JSN_EXTFW_GENERAL_DATA_REMAIN', $name, 'update'); ?></li></ul>
			</div>
<?php
$hasUpdate		= false;
$authentication	= false;

foreach (JSNUpdateHelper::check($products) AS $result)
{
	if ($result)
	{
		$hasUpdate OR $hasUpdate = true;

		// Is authentication required?
		if (isset($result->authentication) AND $result->authentication)
		{
			$authentication = true;
		}
		elseif (isset($result->editions))
		{
			foreach ($result->editions AS $item)
			{
				if (strcasecmp($item->edition, $edition) == 0 AND $item->authentication)
				{
					$authentication = true;
				}
			}
		}

		// Build query string for updating product
		$query = 'id=' . $result->identified_name . '&edition=' . str_replace(' ', '+', trim(isset($result->edition) ? $result->edition : $edition));

		// Generate HTML code
		$html[] = '<li ref="' . $query . '">' . JText::sprintf('JSN_EXTFW_UPDATE_ELEMENT', strpos(strtolower($result->name), 'jsn ') !== false ? $result->name : $result->description, $result->version) . '</li>';
	}
}

if ($hasUpdate)
{
?>
			<p><?php echo JText::sprintf('JSN_EXTFW_UPDATE_ELEMENTS', $name); ?>:</p>
			<ul><?php echo implode($html); ?></ul>
			<div class="form-actions">
				<p>
					<a id="jsn-proceed-button" class="btn btn-primary" href="javascript:void(0)" data-source="<?php echo JRoute::_('index.php?option=' . $input->getCmd('option') . '&view=' . $input->getCmd('view') . '&task=update.download'); ?>">
						<?php echo JText::_('JSN_EXTFW_UPDATE_BUTTON'); ?></a>
				</p>
			</div>
		</div>
<?php
	if ($authentication)
	{
?>
		<div id="jsn-update-login" style="display: none;">
			<form name="JSNUpdateLogin" method="POST" class="form-horizontal" autocomplete="off">
				<h2><?php echo JText::_('JSN_EXTFW_GENERAL_LOGIN_HEAD'); ?></h2>
				<p><?php echo JText::_('JSN_EXTFW_GENERAL_LOGIN_DESC'); ?></p>
				<div class="row-fluid">
					<div class="span6">
						<div class="control-group">
							<label for="username" class="inline"><?php echo JText::_('JGLOBAL_USERNAME'); ?>:</label>
							<input type="text" value="" class="input-xlarge" id="username" name="customer_username" />
						</div>
					</div>
					<div class="span6">
						<div class="control-group">
							<label for="password" class="inline"><?php echo JText::_('JGLOBAL_PASSWORD'); ?>:</label>
							<input type="password" value="" class="input-xlarge" id="password" name="customer_password" />
						</div>
					</div>
				</div>
				<hr />
				<div class="form-actions">
					<button class="btn btn-primary" disabled="disabled"><?php echo JText::_('JNEXT'); ?></button>
				</div>
				<?php echo JHtml::_('form.token'); ?>
			</form>
		</div>
<?php
	}
?>
		<div id="jsn-update-indicator" style="display: none;">
			<p><?php echo JText::_('JSN_EXTFW_UPDATE_START_DESC'); ?></p>
			<ul id="jsn-update-products">
<?php
	foreach ($html AS $li)
	{
		echo str_replace('</li>', '', $li);
?>
				<ul>
					<li class="jsn-update-downloading" style="display: none;">
						<?php echo JText::_('JSN_EXTFW_UPDATE_DOWNLOADING'); ?>
						<span class="jsn-update-downloading-indicator jsn-icon16 jsn-icon-loading"></span>
						<span class="jsn-update-downloading-notice jsn-processing-message"></span>
						<br />
						<p class="jsn-update-downloading-unsuccessful-message jsn-text-important"></p>
					</li>
					<li class="jsn-update-installing" style="display: none;">
						<?php echo JText::_('JSN_EXTFW_UPDATE_INSTALLING'); ?>
						<span class="jsn-update-installing-indicator jsn-icon16 jsn-icon-loading"></span>
						<span class="jsn-update-downloading-notice jsn-processing-message"></span>
						<br />
						<p class="jsn-update-installing-unsuccessful-message jsn-text-important"></p>
						<div class="jsn-update-installing-warnings alert alert-warning">
							<p><span class="label label-important"><?php echo JText::_('JSN_EXTFW_GENERAL_WARNING'); ?></span></p>
						</div>
					</li>
				</ul>
<?php
		echo '</li>';
	}
?>
			</ul>
		</div>
		<div id="jsn-update-successfully" style="display: none;">
			<hr>
			<p><?php echo JText::sprintf('JSN_EXTFW_UPDATE_SUCCESS_MESSAGE', $name); ?></p>
			<div class="form-actions">
				<?php
				 	$_redir	= $redirAfterFinish ? $redirAfterFinish : 'index.php?option=' . $input->getCmd('option');
				?>
				<p>
					<a class="btn btn-primary" href="<?php echo JRoute::_($_redir); ?>">
						<?php echo JText::_('JSN_EXTFW_UPDATE_FINISH'); ?></a>
				</p>
			</div>
		</div>
<?php
}
else
{
?>
			<p>
				<strong><?php echo JText::_('JSN_EXTFW_UPDATE_LATEST'); ?></strong>
			</p>
		</div>
<?php
}
?>
	</div>
</div>
<div class="clr"></div>
<?php
// Add assets
echo JSNHtmlAsset::loadScript(
	'jsn/update',
	array(
		'button'	=> 'jsn-proceed-button',
		'language'	=> array('JSN_EXTFW_GENERAL_STILL_WORKING', 'JSN_EXTFW_GENERAL_PLEASE_WAIT'),
		'redirect'	=> strpos($_SERVER['HTTP_REFERER'], '/administrator/index.php?option=com_installer') !== false ? 1 : 0
	),
	true
);
