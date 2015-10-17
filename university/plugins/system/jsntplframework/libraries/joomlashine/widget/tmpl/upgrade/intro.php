<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  TPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
?>
<div id="jsn-upgrade-intro">
	<p><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_INTRO_DESC'); ?></p>

	<div class="alert alert-warning">
		<span class="label label-important"><?php echo JText::_('JSN_TPLFW_IMPORTANT_INFORMATION'); ?></span>
		<ul>
			<li><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_INTRO_NOTE_01'); ?></li>
			<li><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_INTRO_NOTE_02'); ?></li>
		</ul>
	</div>

	<?php echo $data; ?>

	<hr />

	<div class="jsn-actions">
		<?php $nextEdition = $template['edition'] == 'FREE' ? '' : 'UNLIMITED'; ?>
		<?php $purchaseLink = 'http://www.joomlashine.com/joomla-templates/jsn-' . trim(substr($template['id'], 4)) . '-download.html'; ?>
		<p>
			<a href="javascript:void(0)" id="btn-start-upgrade" class="btn btn-primary"><?php echo JText::sprintf('JSN_TPLFW_AUTO_UPGRADE_ALREADY_PURCHASED', $nextEdition) ?></a>
		</p>
		<p>
			<a href="<?php echo $purchaseLink ?>" target="_blank" class="jsn-link-action"><?php echo JText::sprintf('JSN_TPLFW_AUTO_UPGRADE_PURCHASE_NOW', $nextEdition) ?></a>
		</p>
	</div>
</div>