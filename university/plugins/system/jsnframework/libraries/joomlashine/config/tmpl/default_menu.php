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
?>
<div id="jsn-config-menu" class="jsn-page-nav">
<?php
foreach ($config AS $sk => $section) :
?>
	<ul class="nav nav-list">
		<li class="nav-header"><span><?php echo JText::_($section->label); ?></span></li>
<?php
	foreach ($section->groups AS $gk => $group) :
		$link = JRoute::_('index.php?option=' . $input->getCmd('option') . '&view=' . $input->getCmd('view') . '&s=' . $sk . '&g=' . $gk);
?>
		<li<?php echo ($rSection == $sk && $rGroup == $gk) ? ' class="active"' : ''; ?>>
			<a <?php echo 'id="link' . $group->name . '" href="' . $link . '" class="jsn-config-menu-link"' . ($group->ajax ? ' ajax-request="yes"' : ''); ?>>
				<?php echo '<i class="jsn-icon32 jsn-icon-' . $group->icon . '"></i>' . JText::_($group->label) . '</a>'; ?>
		</li>
<?php
	endforeach;
?>
	</ul>
<?php
endforeach;
?>
</div>
