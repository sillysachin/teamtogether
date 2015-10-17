<?php
/**
 * @author    JoomlaShine.com http://www.joomlashine.com
 * @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted index access');

// Load template framework
if (!defined('JSN_PATH_TPLFRAMEWORK')) {
	require_once JPATH_ROOT . '/plugins/system/jsntplframework/jsntplframework.defines.php';
	require_once JPATH_ROOT . '/plugins/system/jsntplframework/libraries/joomlashine/loader.php';
}
	
// Preparing template parameters
JSNTplTemplateHelper::prepare(false, false);

// Retrieve document object
$document = JFactory::getDocument();

/* URL where logo image should link to (! without preceding slash !)
   Leave this box empty if you want your logo to be clickable. */
$logoLink = $document->logoLink;
if (strpos($logoLink, "http")=== false && $logoLink != '')
{
	$utils		= JSNTplUtils::getInstance();
	$logoLink	= $utils->trimPreceddingSlash($logoLink);
	$logoLink	= $this->baseurl . '/' . $logoLink;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- <?php echo $document->template ?> <?php echo $document->version ?> -->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<jdoc:include type="head" />
		<link rel="stylesheet" href="<?php echo $this->baseurl.'/templates/'.$this->template ;?>/css/colors/<?php echo $document->templateColor ?>.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl.'/templates/'.$this->template ;?>/css/offline.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/system.css" type="text/css" />
	</head>
	<body id="jsn-master" class="jsn-color-<?php echo $document->templateColor ?>">
		<jdoc:include type="message" />
		<div id="jsn-page">
			<div id="jsn-page_inner">
				<div id="jsn-header">
					<div id="jsn-logo">
						<a href="<?php echo $logoLink ?>" title="<?php echo $document->logoSlogan; ?>">
							<?php
								if ($document->logoFile != "")
									$logo_path = $document->logoFile;
								else
									$logo_path = $this->baseurl . '/templates/' . $this->template . "/images/logo.png";
							?>
							<img src="<?php echo $logo_path; ?>" alt="<?php echo $document->logoSlogan; ?>" />
						</a>
					</div>
				</div>
				<div id="jsn-body" class="clearafter">
					<div id="jsn-error-heading">
						<?php if ($document->app->getCfg('offline_image')) : ?>
							<img src="<?php echo $document->app->getCfg('offline_image'); ?>" alt="<?php echo htmlspecialchars($document->app->getCfg('sitename')); ?>" />
						<?php else : ?>
							<img src="<?php echo $this->baseurl.'/templates/'.$this->template ;?>/images/offline-banner.png" alt="Offline Banner" />
						<?php endif; ?>
					</div>
					<div id="jsn-error-content" class="jsn-offline-page">
						<div id="jsn-error-content_inner">
							<div id="frame" class="outline">
								<h3> <?php echo $document->app->getCfg('offline_message'); ?> </h3>
								<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
									<fieldset class="input">
										<p id="form-login-username">
											<label for="username"><?php echo JText::_('JGLOBAL_USERNAME') ?></label>
											<br />
											<input name="username" id="username" type="text" class="inputbox" alt="<?php echo JText::_('JGLOBAL_USERNAME') ?>" size="18" />
										</p>
										<p id="form-login-password">
											<label for="passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
											<br />
											<input type="password" name="password" class="inputbox" size="18" alt="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" id="passwd" />
										</p>
										<p id="form-login-remember" class="clearafter">
											<label for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>
												<input type="checkbox" name="remember" class="inputbox" value="yes" alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" id="remember" />
											</label>
											<input type="submit" name="Submit" class="button link-button" value="<?php echo JText::_('JLOGIN') ?>" />
										</p>
									</fieldset>
									<input type="hidden" name="option" value="com_users" />
									<input type="hidden" name="task" value="user.login" />
									<input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>" />
									<?php echo JHtml::_('form.token'); ?>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>