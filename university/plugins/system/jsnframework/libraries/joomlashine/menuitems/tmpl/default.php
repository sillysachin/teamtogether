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

if (JFactory::getApplication() -> isSite())
{
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

// Load asset
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

if (JSNVersion::isJoomlaCompatible('3.0'))
{
	JHtml::_('behavior.tooltip');
}
else
{
	JHtml::_('behavior.framework');
}

$function = JFactory::getApplication() -> input -> getCmd('function', 'jQuery.jsnGetMenuItems');
$listOrder = $this -> escape($this -> state -> get('list.ordering'));
$listDirn = $this -> escape($this -> state -> get('list.direction'));
$actionForm = isset($_SERVER['QUERY_STRING']) ? 'index.php?' . $_SERVER['QUERY_STRING'] : '';
?>
<div class="jsn-page-list">
	<div class="jsn-bootstrap">
		<form class="form-inline form-menu" action="<?php echo JRoute::_($actionForm); ?>" method="post" name="adminForm" id="adminForm">
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
							<select name="menutype" class="inputbox" onchange="this.form.submit()">
								<?php echo JHtml::_('select.options', JHtml::_('menu.menus'), 'value', 'text', $this -> state -> get('filter.menutype')); ?>
							</select>

							<select name="filter_level" class="inputbox" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('COM_MENUS_OPTION_SELECT_LEVEL'); ?></option>
								<?php echo JHtml::_('select.options', $this -> f_levels, 'value', 'text', $this -> state -> get('filter.level')); ?>
							</select>

							<select name="filter_published" class="inputbox" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
								<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array ('archived' => false)), 'value', 'text', $this -> state -> get('filter.published'), true); ?>
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
						<th class="title">
							<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JText::_('JGRID_HEADING_MENU_ITEM_TYPE'); ?>
						</th>
						<th width="5%">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<?php // Grid layout      ?>
				<tbody>
					<?php
					$originalOrders = array ();
					foreach ($this -> items as $i => $item) :
						?>
						<tr onclick="<?php echo 'if (window.parent && window.parent.' . $this -> escape($function) . ') window.parent.' . $this -> escape($function) . '(' . $item -> id . ', \'' . $item -> title . '\', null, \'' . $this -> escape($item -> link) . '&amp;Itemid=' . $item -> id . '\');'; ?>">
							<td>
								<?php echo $this -> escape($item -> title); ?>
							</td>
							<td>
								<?php echo $this -> escape($item -> access_level); ?>
							</td>
							<td class="nowrap">
								<span title="<?php echo isset($item -> item_type_desc) ? htmlspecialchars($this -> escape($item -> item_type_desc), ENT_COMPAT, 'UTF-8') : ''; ?>">
									<?php echo $this -> escape($item -> item_type); ?></span>
							</td>
							<td>
								<?php
								if ($item -> language == '')
								{
									?>
									<?php echo JText::_('JDEFAULT'); ?>
									<?php
								} elseif ($item -> language == '*')
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
							<td class="nowrap">
								<span title="<?php echo sprintf('%d-%d', $item -> lft, $item -> rgt); ?>">
	<?php echo (int) $item -> id; ?></span>
							</td>
						</tr>
								<?php endforeach; ?>
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
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>
