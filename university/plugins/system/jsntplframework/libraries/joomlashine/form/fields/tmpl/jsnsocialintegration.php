<?php
/**
 * @version     $Id$
 * @package     JSNTPLFW
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<div class="jsn-sortable-list jsn-social-integration pull-left<?php echo count($this->options['status']) ? '' : ' hide'; ?>" id="<?php echo $this->id; ?>">
	<ul class="jsn-items-list ui-sortable">
<?php
foreach ($this->options['status'] AS $name)
{
	if (isset($this->options[$name]))
	{
?>
		<li class="jsn-item ui-state-default">
			<input id="<?php echo "{$this->id}_{$name}_status"; ?>" type="hidden" name="<?php echo $this->name; ?>[status][]" value="<?php echo $name ?>" />
			<?php echo JText::_($this->options[$name]['title']); ?>
		</li>
<?php
	}
}
?>
	</ul>
</div>
<a href="javascript:void(0)" class="btn pull-left jsn-social-integration-button<?php echo $this->disabled ? ' disabled' : ''; ?>">...</a>
<p class="pull-left<?php echo count($this->options['status']) ? ' hide' : ''; ?>" id="<?php echo $this->id; ?>_message">
	&nbsp;&nbsp;<?php echo JText::_('JSN_TPLFW_SOCIAL_ICONS_NOT_CONFIGURED'); ?>
</p>
<?php
if ( ! $this->disabled )
{
?>
<div class="jsn-bootstrap" id="<?php echo $this->id; ?>_modal" title="<?php echo JText::_('JSN_TPLFW_SOCIAL_NETWORK_INTEGRATION'); ?>">
	<div class="form-horizontal">
<?php
	foreach ($this->options AS $name => $data)
	{
		if ($name != 'status')
		{
?>
		<div class="control-group">
			<label for="#<?php echo "{$this->id}_{$name}"; ?>" class="control-label">
				<?php echo JText::_($data['title']); ?>
			</label>
			<div class="controls">
				<input name="<?php echo $this->name; ?>[<?php echo $name ?>][title]" type="hidden" value="<?php echo $data['title']; ?>" />
				<input class="jsn-input-xlarge-fluid" id="<?php echo "{$this->id}_{$name}"; ?>" name="<?php echo $this->name; ?>[<?php echo $name ?>][link]" type="text" value="<?php echo $data['link']; ?>" placeholder="<?php echo $data['placeholder']; ?>" data-target="<?php echo "{$this->id}_{$name}_status"; ?>" data-name="<?php echo $this->name; ?>[status][]" data-value="<?php echo $name ?>" <?php echo $this->disabled ? 'disabled="disabled"' : ''; ?> />
			</div>
		</div>
<?php
		}
	}
?>
	</div>
</div>
<script type="text/javascript">
	(function($) {
		$(document).ready(function() {
			new $.JSNSocialIntegration({
				id: '<?php echo $this->id; ?>',
				language: {
					JSN_TPLFW_SAVE: '<?php echo JText::_('JSN_TPLFW_SAVE'); ?>',
					JSN_TPLFW_CLOSE: '<?php echo JText::_('JSN_TPLFW_CLOSE'); ?>'
				}
			});
		});

		// Override submit button function
		$.JSNSocialIntegration.JSubmitButton = Joomla.submitbutton;

		Joomla.submitbutton = function(task)
		{
			// Append social channels configuration fields to form
			$('#<?php echo $this->id; ?>_modal').addClass('hide').appendTo($('form#style-form'));

			// Trigger submit button function
			typeof $.JSNFontCustomizer.JSubmitButton == 'undefined' || $.JSNFontCustomizer.JSubmitButton(task);
		};
	})(jQuery);
</script>
<?php
}
