<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPL
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Initiasile related data.
require_once JPATH_ADMINISTRATOR.'/components/com_menus/helpers/menus.php';

$menuTypes = MenusHelper::getMenuLinks();
$user = JFactory::getUser();
$styleId = JFactory::getApplication()->input->getInt('id');
$JVersion = new JVersion();
$isJoomla30 = version_compare($JVersion->getShortVersion(), '3.0', '>=');
?>
<div class="control-group">
	<div class="control-label">
		<label id="jform_menuselect-lbl" for="jform_menuselect"><?php echo JText::_('JGLOBAL_MENU_SELECTION'); ?></label>
	</div>
	<div class="controls">
		<div class="row-fluid">
			<div class="span12 btn-toolbar">
				<button class="btn btn-primary" type="button" id="jsn-toggle-menu">
					<?php echo JText::_('JGLOBAL_SELECTION_INVERT'); ?>
				</button>
			</div>
		</div>
		<div id="menu-links" class="row-fluid">
			<?php foreach (MenusHelper::getMenuLinks() as $menuType): ?>
			<div class="span3 box">
				<ul id="menu-type-<?php echo $menuType->menutype ?>">
					<li class="menu-type-header">
						<label class="checkbox menu-type">
							<input type="checkbox" name="checkAll" />
							<?php echo !empty($menuType->title) ? $menuType->title : $menuType->menutype; ?>
						</label>
						<hr />
					</li>
					<?php foreach ($menuType->links as $link): ?>
					<li>
						<?php $checked = $link->template_style_id == $styleId ? 'checked' : '' ?>
						<?php $disabled = !empty($link->checked_out) && $link->checked_out != $user->id ? 'disabled' : '' ?>
						<?php $prefix = $isJoomla30 ? str_repeat('- ', $link->level) : '' ?>
						<label class="checkbox">
							<input type="checkbox" name="jform[assigned][]"
								value="<?php echo (int) $link->value ?>"
								class="menu-item"
								<?php echo $checked ?>
								<?php echo $disabled ?>
							/>
							<?php echo $prefix, $link->text ?>
						</label>
					</li>
					<?php endforeach ?>
				</ul>
			</div>
			<?php endforeach ?>

			<div class="clearfix"></div>
		</div>
	</div>
</div>