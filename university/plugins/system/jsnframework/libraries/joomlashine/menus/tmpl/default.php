<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_menus
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$action = JFactory::getApplication()->input->getCmd('jsnaction', '');
$actionForm = isset($_SERVER['QUERY_STRING'])?'index.php?' . $_SERVER['QUERY_STRING']:'';
?>
<div class="jsn-page-list">
    <div class="jsn-bootstrap">
	<form class="form-inline form-menu" action="<?php echo JRoute::_($actionForm); ?>" method="post" name="adminForm" id="adminForm">
	    <div class="row-fluid">
		<table class="table table-bordered table-striped table-popup">
		    <thead>
		    <tr>
			    <?php
			    $classHover = "jsnhover";

			    if ($action != "update")
			    {
				    $classHover = "";
				    ?>
				<th width="1%" rowspan="2">
				    <input type="checkbox" name="checkall-toggle" class="checkall" />
				</th>
				    <?php
			    }
			    ?>
			<th rowspan="2">
				<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
			</th>
			<th width="30%" colspan="3">
				<?php echo JText::_('COM_MENUS_HEADING_NUMBER_MENU_ITEMS'); ?>
			</th>
			<th width="20%" rowspan="2">
				<?php echo JText::_('COM_MENUS_HEADING_LINKED_MODULES'); ?>
			</th>
			<th width="1%" class="nowrap" rowspan="2">
				<?php echo JText::_('JGRID_HEADING_ID'); ?>
			</th>
		    </tr>
		    <tr>
			<th width="10%">
				<?php echo JText::_('COM_MENUS_HEADING_PUBLISHED_ITEMS'); ?>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_MENUS_HEADING_UNPUBLISHED_ITEMS'); ?>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_MENUS_HEADING_TRASHED_ITEMS'); ?>
			</th>
		    </tr>
		    </thead>
		    <tfoot>
		    <tr>
			<td colspan="15">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		    </tr>
		    </tfoot>
		    <tbody>
		    <?php
		    foreach ($this->items as $i => $item) :
			    ?>
		    <tr <?php echo 'class="' . $classHover . '" data-title="' . $this -> escape($item -> title) . '" data-id="' . $item -> id . '"'; ?>>
			    <?php
			    if ($action != "update")
			    {
				    ?>
				<td class="checkbox-items">
				    <input type="checkbox" name="cid[]" <?php echo 'value="' . $item->id . '" data-title="' . $this->escape($item->title) . '"'; ?>>
				</td>
				    <?php
			    }
			    ?>
			<td>
				<?php echo $this->escape($item->title); ?>
			    <p class="smallsub">(<span><?php echo JText::_('COM_MENUS_MENU_MENUTYPE_LABEL') ?></span>
					    <?php echo $this->escape($item->menutype) ?>)
			    </p>
			</td>
			<td class="center btns">
				<?php echo $item->count_published; ?>
			</td>
			<td class="center btns">
				<?php echo $item->count_unpublished; ?>
			</td>
			<td class="center btns">
				<?php echo $item->count_trashed; ?>
			</td>
			<td class="left">
				<?php if (isset($this->modules[$item->menutype])) : ?>
			    <ul>
				    <?php foreach ($this->modules[$item->menutype] as &$module) : ?>
				<li>
					<?php echo JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $this->escape($module->title), $this->escape($module->access_title), $this->escape($module->position)); ?>
				</li>
				    <?php endforeach; ?>
			    </ul>
				<?php endif; ?>
			</td>
			<td class="center">
				<?php echo $item->id; ?>
			</td>
		    </tr>
			    <?php endforeach; ?>
		    </tbody>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		    <?php echo JHtml::_('form.token'); ?>
	    </div>
	</form>
    </div>
</div>
