<?php
/**
 * @version    $Id:$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');


$extension = $this -> escape($this -> state -> get('filter.extension'));
$listOrder = $this -> escape($this -> state -> get('list.ordering'));
$listDirn = $this -> escape($this -> state -> get('list.direction'));

$actionForm = isset($_SERVER['QUERY_STRING']) ? 'index.php?' . $_SERVER['QUERY_STRING'] : '';
?>
<div class="jsn-page-list">
    <div class="jsn-bootstrap">
		<form class="form-inline form-categories" action="<?php echo JRoute::_($actionForm); ?>" method="post" name="adminForm" id="adminForm">
			<div class="jsn-fieldset-filter">
				<fieldset>
					<div class="pull-left jsn-fieldset-search">
						<label class="control-label" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
						<input type="text" name="filter_search" class="input-xlarge" id="filter_search" value="<?php echo $this -> escape($this -> state -> get('filter.search')); ?>" title="" />
						<button class="btn btn-icon" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" type="submit"><i class="icon-search"></i></button>
						<button class="btn btn-icon" type="button" onclick="document.id('filter_search').value = '';
								this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
					</div>
					<div class="pull-right">
						<button class="btn btn-icon filter-select" title="<?php echo JText::_('JSN_EXTFW_ITEMLIST_SELECT_FILTER'); ?>" onclick="return false;"><?php echo JText::_('JSN_EXTFW_ITEMLIST_SELECT_FILTER'); ?></button>
					</div>
					<div class="jsn-fieldset-filter-select popover hide">
						<div class="arrow"></div>
						<h3 class="popover-title"><?php echo JText::_('JSN_EXTFW_ITEMLIST_SELECT_FILTER'); ?></h3>
						<div class="popover-content">
							<select name="filter_level" class="inputbox" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('JOPTION_SELECT_MAX_LEVELS'); ?></option>
								<?php echo JHtml::_('select.options', $this -> f_levels, 'value', 'text', $this -> state -> get('filter.level')); ?>
							</select>

							<select name="filter_published" class="inputbox" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
								<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this -> state -> get('filter.published'), true); ?>
							</select>

							<select name="filter_access" class="inputbox" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS'); ?></option>
								<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this -> state -> get('filter.access')); ?>
							</select>

							<select name="filter_language" class="inputbox" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE'); ?></option>
								<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this -> state -> get('filter.language')); ?>
							</select>
						</div>
					</div>
				</fieldset>
			</div>
			<table class="table table-bordered table-striped table-popup">
				<thead>
					<tr>
						<th width="1%">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="5%" class="nowrap">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $this -> state -> get('list.direction'), $this -> state -> get('list.ordering')); ?>
						</th>
						<th width="1%" class="nowrap">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="15">
							<?php echo $this -> pagination -> getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php
					foreach ($this -> items as $i => $item)
					{
						?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="checkbox-items">
								<input type="checkbox" class="useremail" name="useremail[]" <?php echo 'id="cb' . $i . '" value=\'' . json_encode(array ('title' => $item -> title, 'id' => $item -> id)) . '\''; ?>>
							</td>
							<td>
								<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item -> level - 1) ?>
								<?php
								if ($item -> checked_out)
								{
									?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item -> editor, $item -> checked_out_time, 'categories.', $canCheckin); ?>
									<?php
								}
								?>
								<?php echo $this -> escape($item -> title); ?>
								<p class="smallsub" title="<?php echo $this -> escape($item -> path); ?>">
									<?php echo str_repeat('<span class="gtr">|&mdash;</span>', $item -> level - 1) ?>
									<?php
									if (empty($item -> note))
									{
										?>
										<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this -> escape($item -> alias)); ?>
										<?php
									} else
									{
										?>
										<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this -> escape($item -> alias), $this -> escape($item -> note)); ?>
										<?php
									}
									?></p>
							</td>
							<td class="center">
								<?php echo $this -> escape($item -> access_level); ?>
							</td>
							<td class="center nowrap">
								<?php
								if ($item -> language == '*')
								{
									?>
									<?php echo JText::alt('JALL', 'language'); ?>
									<?php
								} else
								{
									?>
									<?php echo $item -> language_title ? $this -> escape($item -> language_title) : JText::_('JUNDEFINED'); ?>
									<?php
								}
								?>
							</td>
							<td class="center">
								<span title="<?php echo sprintf('%d-%d', $item -> lft, $item -> rgt); ?>">
	<?php echo (int) $item -> id; ?></span>
							</td>
						</tr>
	<?php
}
?>
				</tbody>
			</table>
<?php //Load the batch processing form.        ?>
			<div>
				<input type="hidden" name="extension" value="<?php echo $extension; ?>" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
    </div>
</div>
