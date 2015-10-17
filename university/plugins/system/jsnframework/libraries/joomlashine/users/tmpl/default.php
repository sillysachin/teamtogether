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
defined('_JEXEC') or die('Restricted access');

// Load assets
if (JSNVersion::isJoomlaCompatible('3.0'))
{
	JHtml::_('behavior.tooltip');
	JHtml::_('behavior.modal');
}
else
{
	JHtml::_('behavior.framework');
}

JHtml::_('behavior.multiselect');

$listOrder = $this -> escape($this -> state -> get('list.ordering'));
$listDirn = $this -> escape($this -> state -> get('list.direction'));
$actionForm = isset($_SERVER['QUERY_STRING']) ? 'index.php?' . $_SERVER['QUERY_STRING'] : '';
?>
<div class="jsn-page-list">
	<div class="jsn-bootstrap">

		<form class="form-inline" action="<?php echo JRoute::_($actionForm); ?>" method="post" name="adminForm" id="adminForm">
			<div class="jsn-fieldset-filter">
				<fieldset>
					<div class="pull-left jsn-fieldset-search">
						<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>:&nbsp;</label>
						<input type="text" name="filter_search" class="input-xlarge" id="filter_search" <?php echo 'value="' . $this -> escape($this -> state -> get('filter.search')) . '" title="' . JText::_('COM_USERS_USERS_SEARCH_USERS') . '"'; ?> />
						<button class="btn btn-icon" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" type="submit"><i class="icon-search"></i></button>
						<button class="btn btn-icon" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value = '';
								this.form.submit();"><i class="icon-remove"></i></button>
					</div>
					<div class="pull-right">
						<button class="btn btn-icon filter-select" title="<?php echo JText::_('JSN_EXTFW_ITEMLIST_SELECT_FILTER'); ?>" onclick="return false;"><?php echo JText::_('JSN_EXTFW_ITEMLIST_SELECT_FILTER'); ?></button>
					</div>
					<div class="jsn-fieldset-filter-select popover hide">
						<div class="arrow"></div>
						<h3 class="popover-title"><?php echo JText::_('JSN_EXTFW_ITEMLIST_SELECT_FILTER'); ?></h3>
						<div class="popover-content">
							<select name="filter_state" class="inputbox" onchange="this.form.submit()">
								<option value="*"><?php echo JText::_('COM_USERS_FILTER_STATE'); ?></option>
								<?php echo JHtml::_('select.options', UsersHelper::getStateOptions(), 'value', 'text', $this -> state -> get('filter.state')); ?>
							</select>
							<select name="filter_active" class="inputbox" onchange="this.form.submit()">
								<option value="*"><?php echo JText::_('COM_USERS_FILTER_ACTIVE'); ?></option>
								<?php echo JHtml::_('select.options', UsersHelper::getActiveOptions(), 'value', 'text', $this -> state -> get('filter.active')); ?>
							</select>
							<select name="filter_group_id" class="inputbox" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('COM_USERS_FILTER_USERGROUP'); ?></option>
								<?php echo JHtml::_('select.options', UsersHelper::getGroups(), 'value', 'text', $this -> state -> get('filter.group_id')); ?>
							</select>
							<select name="filter_range" id="filter_range" class="inputbox" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('COM_USERS_OPTION_FILTER_DATE'); ?></option>
								<?php echo JHtml::_('select.options', Usershelper::getRangeOptions(), 'value', 'text', $this -> state -> get('filter.range')); ?>
							</select>
						</div>
					</div>
					<div class="clearbreak"></div>
				</fieldset>
			</div>
			<table class="table table-bordered table-striped table-popup">
				<thead>
					<tr>
						<th width="10">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap" width="10%">
							<?php echo JHtml::_('grid.sort', 'JGLOBAL_USERNAME', 'a.username', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap" width="10%">
							<?php echo JText::_('COM_USERS_HEADING_GROUPS'); ?>
						</th>
						<th class="nowrap" width="15%">
							<?php echo JHtml::_('grid.sort', 'JGLOBAL_EMAIL', 'a.email', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap" width="3%">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($this -> items AS $i => $item)
					{
						?>
						<tr class="items row<?php echo $i % 2; ?>">
							<td class="checkbox-items">
								<input type="checkbox" class="useremail" name="useremail[]" <?php echo 'id="cb' . $i . '" value=\'' . json_encode(array ('username' => $item -> username, 'email' => $item -> email, 'name' => $item -> name, 'id' => $item -> id)) . '\''; ?>>
							</td>
							<td>
								<?php echo $this -> escape($item -> name); ?>
							</td>
							<td>
								<?php echo $this -> escape($item -> username); ?>
							</td>
							<td>
								<?php
								if (substr_count($item -> group_names, "\n") > 1)
								{
									?>
									<span class="hasTip" title="<?php echo JText::_('COM_USERS_HEADING_GROUPS') . '::' . nl2br($item -> group_names); ?>">
										<?php echo JText::_('COM_USERS_USERS_MULTIPLE_GROUPS'); ?></span>
										<?php
									} else
									{
										echo nl2br($item -> group_names);
									}
									?>
							</td>
							<td>
	<?php echo $this -> escape($item -> email); ?>
							</td>
							<td>
	<?php echo (int) $item -> id; ?>
							</td>
						</tr>
	<?php
}
?>
				</tbody>
				<tfoot>
					<tr>
						<td class="jsn-pagination" colspan="15">
<?php echo str_replace('Joomla.submitform();', 'document.adminForm.submit();', $this -> pagination -> getListFooter()); ?>
						</td>
					</tr>
				</tfoot>
			</table>
			<div>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_state" value="*" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>
