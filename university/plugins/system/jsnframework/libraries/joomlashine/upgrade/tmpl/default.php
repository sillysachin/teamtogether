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
$edition	= strtolower(JSNUtilsText::getConstant('EDITION'));
$buyNow		= JSNUtilsText::getConstant('BUY_LINK');

// Get input object
$input = JFactory::getApplication()->input;

// Get upgrade details from JoomlaShine server
$response = null;

try
{
	$response = JSNUtilsHttp::get(JSN_EXT_UPGRADE_DETAILS_URL);
	$response = json_decode($response['body'], true);
}
catch (Exception $e)
{
	// Do nothing
}

if ($response != null)
{
	// Get response belonging to current extension
	if (isset($response[JSNUtilsText::getConstant('IDENTIFIED_NAME')]))
	{
		$response = $response[JSNUtilsText::getConstant('IDENTIFIED_NAME')];
	}
	elseif (isset($response['extension']))
	{
		$response = $response['extension'];
	}
	else
	{
		$response = $response['default'];
	}

	// Get current template edition
	$currentEdition = strcasecmp(JSNUtilsText::getConstant('EDITION'), 'free') == 0 ? 'free' : 'pro';

	// Prepare content
	$content = isset($response['pro']) ? $response['pro'] : '';

	if ($currentEdition == 'free')
	{
		$content = $response['free'] . $content;
	}
}
?>
<div class="jsn-page-upgrade">
	<div class="jsn-page-content jsn-rounded-large jsn-box-shadow-large jsn-bootstrap">
		<span id="jsn-upgrade-cancel"><a class="jsn-link-action" href="<?php echo JRoute::_('index.php?option=' . $input->getCmd('option')); ?>">
			<?php echo JText::_('JCANCEL'); ?></a></span>
		<h1><?php echo JText::sprintf($edition == 'free' ? 'JSN_EXTFW_UPGRADE_PAGE_HEAD_FREE' : 'JSN_EXTFW_UPGRADE_PAGE_HEAD_PRO', $name); ?></h1>
		<div id="jsn-upgrade-action">
			<p><?php echo JText::sprintf($edition == 'free' ? 'JSN_EXTFW_UPGRADE_PAGE_DESC_FREE' : 'JSN_EXTFW_UPGRADE_PAGE_DESC_PRO', $name); ?></p>
			<div class="alert alert-info">
				<p><span class="label label-info"><?php echo JText::_('JSN_EXTFW_GENERAL_IMPORTANT_NOTE'); ?></span></p>
				<ul><li><?php echo JText::sprintf('JSN_EXTFW_GENERAL_DATA_REMAIN', $name, 'upgrade'); ?></li></ul>
			</div>
			<?php echo isset($content) ? $content : $response; ?>
			<div class="form-actions">
				<p>
					<a id="jsn-proceed-button" class="btn btn-primary" href="javascript:void(0)" data-source="<?php echo JRoute::_('index.php?option=' . $input->getCmd('option') . '&view=' . $input->getCmd('view') . '&task=upgrade.download'); ?>">
						<?php echo JText::_($edition == 'free' ? 'JSN_EXTFW_UPGRADE_BUTTON_FREE' : 'JSN_EXTFW_UPGRADE_BUTTON_PRO'); ?></a>
				</p>
				<p>
					<a href="<?php echo $buyNow; ?>" target="_blank" class="jsn-link-action">
						<?php echo JText::_($edition == 'free' ? 'JSN_EXTFW_UPGRADE_LINK_FREE' : 'JSN_EXTFW_UPGRADE_LINK_PRO'); ?></a>
				</p>
			</div>
		</div>
		<div id="jsn-upgrade-login" style="display: none;">
			<form name="JSNUpgradeLogin" method="POST" class="form-horizontal" autocomplete="off">
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

				<div id="jsn-upgrade-message" class="alert alert-error"></div>
				<div id="jsn-upgrade-editions" class="row-fluid">
					<div class="control-group">
						<label for="editions" class="inline"><?php echo JText::_('JSN_EXTFW_UPGRADE_EDITIONS'); ?></label>
						<select name="edition"></select>
					</div>
				</div>

				<div class="form-actions">
					<button class="btn btn-primary" disabled="disabled"><?php echo JText::_('JNEXT'); ?></button>
				</div>
				<?php echo JHtml::_('form.token'); ?>
			</form>
		</div>
		<div id="jsn-upgrade-indicator" style="display: none;">
			<p><?php echo JText::_('JSN_EXTFW_UPDATE_START_DESC'); ?></p>
			<ul>
				<li id="jsn-upgrade-downloading">
					<?php echo JText::_('JSN_EXTFW_UPDATE_DOWNLOADING'); ?>
					<span id="jsn-upgrade-downloading-indicator" class="jsn-icon16 jsn-icon-loading"></span>
					<span id="jsn-upgrade-downloading-notice" class="jsn-processing-message"></span>
					<br />
					<p id="jsn-upgrade-downloading-unsuccessful-message" class="jsn-text-important"></p>
				</li>
				<li id="jsn-upgrade-installing" style="display: none;">
					<?php echo JText::_('JSN_EXTFW_UPDATE_INSTALLING'); ?>
					<span id="jsn-upgrade-installing-indicator" class="jsn-icon16 jsn-icon-loading"></span>
					<span id="jsn-upgrade-downloading-notice" class="jsn-processing-message"></span>
					<br />
					<p id="jsn-upgrade-installing-unsuccessful-message" class="jsn-text-important"></p>
					<div id="jsn-upgrade-installing-warnings" class="alert alert-warning">
						<p><span class="label label-important"><?php echo JText::_('JSN_EXTFW_GENERAL_WARNING'); ?></span></p>
					</div>
				</li>
			</ul>
		</div>
		<div id="jsn-upgrade-successfully" style="display: none;">
			<hr>
			<p><?php echo JText::sprintf('JSN_EXTFW_UPGRADE_SUCCESS_MESSAGE', $name); ?></p>
			<div class="form-actions">
				<p>
					<a class="btn btn-primary" href="<?php echo JRoute::_($redirAfterFinish ? $redirAfterFinish : 'index.php?option=' . $input->getCmd('option')); ?>">
						<?php echo JText::_('JSN_EXTFW_UPDATE_FINISH'); ?></a>
				</p>
			</div>
		</div>
	</div>
</div>
<?php
// Add assets
echo JSNHtmlAsset::loadScript(
	'jsn/upgrade',
	array(
		'button'			=> 'jsn-proceed-button',
		'language'			=> array('JSN_EXTFW_GENERAL_STILL_WORKING', 'JSN_EXTFW_GENERAL_PLEASE_WAIT'),
		'redirect'			=> strpos($_SERVER['HTTP_REFERER'], '/administrator/index.php?option=com_installer') !== false ? 1 : 0,
		'component'			=> JFactory::getApplication()->input->getCmd('option'),
		'identifiedName'	=> JSNUtilsText::getConstant('IDENTIFIED_NAME')
	),
	true
);
