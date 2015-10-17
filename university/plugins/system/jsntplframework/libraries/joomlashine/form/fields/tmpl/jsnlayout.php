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

// Get template data
$data = JSNTplHelper::getEditingTemplate();

// Check for layout ajustment image
$hasHelper = is_readable(JPATH_ROOT . '/templates/' . $data->template . '/template_layout_adjustment.jpg');

if ($hasHelper)
{
?>
<a href="javascript:void(0)" class="btn pull-right jsn-layout-helper">
	<i class="icon-question-sign"></i> <?php echo JText::_('JSN_TPLFW_HELP'); ?>
</a>
<div class="jsn-bootstrap" id="<?php echo $this->id; ?>" title="<?php echo JText::_('JSN_TPLFW_LAYOUT_ADJUSTMENT_HELP'); ?>">
	<p><?php echo JText::_('JSN_TPLFW_LAYOUT_ADJUSTMENT_HELP_DESCRIPTION'); ?></p>
	<ul class="thumbnails row-fluid">
		<li class="span12">
			<a class="thumbnail">
				<img alt="template_layout_ajustment.png" src="<?php echo JUri::root(true); ?>/templates/<?php echo $data->template; ?>/template_layout_adjustment.jpg" />
			</a>
		</li>
	</ul>
</div>
<script type="text/javascript">
	(function($) {
		$(document).ready(function() {
			// Setup helper modal
			$('#<?php echo $this->id; ?>').prev().click(function() {
				$('#<?php echo $this->id; ?>').dialog('open');
			});

			$('#<?php echo $this->id; ?>').dialog({
				modal: true,
				autoOpen: false,
				draggable: false,
				resizable: false,
				width: 630,
				height: 650,
				dialogClass: 'jsn-master',
				buttons: {
					'<?php echo JText::_('JSN_TPLFW_CLOSE'); ?>': function() { $(this).dialog('close'); }
				}
			});
		});
	})(jQuery);
</script>
<?php
}

foreach ($this->options AS $group => $columns)
{
	JFormFieldJSNLayoutRenderer($this, $group, $columns, (string) $this->element['name']);
?>
<script type="text/javascript">
	(function($) {
		$(document).ready(function() {
			new $.JSNLayoutCustomizer({
				id: '<?php echo $this->id . '_' . $group; ?>'
			});
		});
	})(jQuery);
</script>
<?php
}

function JFormFieldJSNLayoutRenderer(&$self, $group, $columns, $rootElementName)
{
	$paramName = explode('_', $group);
	$paramName = array_pop($paramName);
	$paramName = str_replace($rootElementName, $paramName, $self->name);
	$numColumn = count($columns);
?>
<ul class="jsn-layout thumbnails" id="<?php echo $self->id . '_' . $group; ?>">
	<?php $i = 0; foreach ($columns AS $name => $data) : $first = ++$i == 1; $last = $i == $numColumn; ?>
	<li class="jsn-layout-column<?php echo $first ? ' first-column' : ''; ?><?php echo $last ? ' last-column' : ''; ?>">
		<div class="<?php echo $data['value']; ?>">
			<div class="thumbnail">
<?php
	if (isset($data['columns']))
	{
		JFormFieldJSNLayoutRenderer($self, "{$group}_{$name}Columns", $data['columns'], $rootElementName);
	}
	else
	{
?>
				<div class="caption">
					<?php echo JText::_($data['label']); ?>
				</div>
<?php
	}
?>
			</div>
			<input name="<?php echo $paramName; ?>[<?php echo $data['order'] . ':' . $name; ?>]" type="hidden" value="<?php echo $data['value']; ?>" />
		</div>
	</li>
	<?php endforeach; ?>
</ul>
<?php
}
