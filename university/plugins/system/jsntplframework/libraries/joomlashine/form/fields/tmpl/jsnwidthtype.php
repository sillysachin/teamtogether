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

// Get template edition
$edition = JSNTplTemplateEdition::getInstance();
?>
<div class="control-group">
	<div class="control-label">
		<label for="<?php echo "{$this->id}_type"; ?>" rel="tipsy" original-title="<?php echo JText::_('JSN_TPLFW_WIDTH_TYPE_DESC'); ?>"><?php echo JText::_('JSN_TPLFW_WIDTH_TYPE'); ?></label>
	</div>
	<div class="controls">
		<select id="<?php echo "{$this->id}_type"; ?>" name="<?php echo $this->name ?>[type]" autocomplete="off">
			<?php foreach ($this->options AS $type => $data) : ?>
			<option value="<?php echo $type; ?>"<?php echo $this->value['type'] == $type ? ' selected' : ''; ?><?php echo ($data['pro'] AND ! $edition->isPro()) ? ' class="jsn-pro-edition-only"' : ''; ?>>
				<?php echo JText::_($data['label']); ?>
			</option>
			<?php endforeach; ?>
		</select>
	</div>
</div>

<?php foreach ($this->options AS $type => $data) : ?>
	<?php if ( ! $data['pro'] OR $edition->isPro()) : ?>
<div class="jsn-width-type<?php echo $this->value['type'] == $type ? '' : ' hide'; ?>" id="<?php echo "{$this->id}_type_{$type}"; ?>">
	<div class="control-group">
		<div class="control-label">
			<label for="<?php echo "{$this->id}_type_{$type}_value"; ?>" rel="tipsy" original-title="<?php echo JText::_("{$data['label']}_WIDTH_DESC"); ?>"><?php echo JText::_("{$data['label']}_WIDTH"); ?></label>
		</div>
		<div class="controls">
			<div<?php echo empty($data['suffix']) ? '' : ' class="input-append"'; ?>>
				<?php if ($data['type'] == 'number') : ?>
				<input class="input-mini validate-positive-number <?php echo $data['class']; ?>" id="<?php echo "{$this->id}_type_{$type}_value"; ?>" name="<?php echo $this->name ?>[<?php echo $type; ?>]" type="number" value="<?php echo (int) @$this->value[$type]; ?>" />
				<?php if ( ! empty($data['suffix'])) : ?><span class="add-on"><?php echo $data['suffix']; ?></span><?php endif; ?>
				<?php else : ?>
					<?php foreach ($data['options'] AS $option) :
						$checked = '';

						if ($data['type'] == 'checkbox' AND in_array((string) $option['value'], (array) @$this->value[$type]))
						{
							$checked = ' checked="checked"';
						}
						elseif ($data['type'] == 'radio' AND (string) $option['value'] == (string) @$this->value[$type])
						{
							$checked = ' checked="checked"';
						} ?>
				<label class="<?php echo $data['type']; ?> inline">
					<input name="<?php echo $this->name ?>[<?php echo $type; ?>]<?php echo $data['type'] == 'checkbox' ? '[]' : ''; ?>" type="<?php echo $data['type']; ?>" value="<?php echo (string) $option['value']; ?>"<?php echo $checked; ?> />
					<?php echo JText::_((string) $option); ?>
				</label>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
	<?php endif; ?>
<?php endforeach; ?>

<script type="text/javascript">
	(function($) {
		$(document).ready(function() {
			new $.JSNWidthType({
				id: '<?php echo $this->id; ?>',
				lang: {
					JSN_TPLFW_LAYOUT_YOU_MUST_SELECT_AN_OPTION: '<?php echo JText::_('JSN_TPLFW_LAYOUT_YOU_MUST_SELECT_AN_OPTION'); ?>'
				}
			});
		});
	})(jQuery);
</script>
