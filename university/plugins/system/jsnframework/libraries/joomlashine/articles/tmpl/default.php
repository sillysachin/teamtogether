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

if (JFactory::getApplication()->isSite())
{
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

require_once JPATH_ROOT . '/components/com_content/helpers/route.php';

// Load assets
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.framework');

$function = JFactory::getApplication()->input->getCmd('function', 'jQuery.jSelectArticle');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$actionForm = isset($_SERVER['QUERY_STRING']) ? 'index.php?' . $_SERVER['QUERY_STRING'] : '';
?>
<div class="jsn-page-list">
	<div class="jsn-bootstrap">
		<form class="form-inline form-articles" action="<?php echo JRoute::_($actionForm); ?>" method="post" name="adminForm" id="adminForm">
			<div class="jsn-fieldset-filter">
				<fieldset>
					<div class="pull-left jsn-fieldset-search">
						<label class="control-label" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
						<input type="text" name="filter_search" class="input-xlarge" id="filter_search" size="30" <?php echo 'value="' . $this->escape($this->state->get('filter.search')) . '" title="' . JText::_('Search title or alias. Prefix with ID: to search for an article ID.') . '"'; ?> />
						<button class="btn btn-icon" type="submit"><i class="icon-search"></i></button>
						<button class="btn btn-icon" id="btn_reset" type="button" onclick="document.getElementById('filter_search').value = '';
								this.form.submit();"><i class="icon-remove"></i></button>
					</div>
					<div class="pull-right">
						<button class="btn btn-icon filter-select" title="<?php echo JText::_('JSN_EXTFW_ITEMLIST_SELECT_FILTER'); ?>" onclick="return false;"><?php echo JText::_('JSN_EXTFW_ITEMLIST_SELECT_FILTER'); ?></button>
					</div>
					<div class="jsn-fieldset-filter-select popover hide">
						<div class="arrow"></div>
						<h3 class="popover-title"><?php echo JText::_('JSN_EXTFW_ITEMLIST_SELECT_FILTER'); ?></h3>
						<div class="popover-content">
						<select name="filter_published" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
							<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true); ?>
						</select>
						<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY'); ?></option>
							<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', $this->state->get('filter.category_id')); ?>
						</select>
						<select name="filter_level" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_MAX_LEVELS'); ?></option>
							<?php echo JHtml::_('select.options', $this->f_levels, 'value', 'text', $this->state->get('filter.level')); ?>
						</select>
						<select name="filter_access" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS'); ?></option>
							<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access')); ?>
						</select>
						<select name="filter_author_id" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_AUTHOR'); ?></option>
							<?php echo JHtml::_('select.options', $this->authors, 'value', 'text', $this->state->get('filter.author_id')); ?>
						</select>
						<select name="filter_language" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE'); ?></option>
							<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language')); ?>
						</select>
					</div>
						</div>
					<div class="clearbreak"></div>
				</fieldset>
			</div>
			<table class="table table-bordered table-striped  table-popup">
				<thead>
					<tr>
						<th class="title">
							<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="15%">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
						</th>
						<th width="15%">
							<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'a.catid', $listDirn, $listOrder); ?>
						</th>
						<th width="5%">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
						</th>
						<th width="5%">
							<?php echo JHtml::_('grid.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<tr onclick="<?php echo 'if (window.parent && window.parent.' . $this->escape($function) . ') window.parent.' . $this->escape($function) . '(' . $item->id . ', \'' . $this->escape(addslashes($item->title)) . '\', ' . $this->escape($item->catid) . ', null, \'' . $this->escape(ContentHelperRoute::getArticleRoute($item->id)) . '\');'; ?>">
							<td>
								<?php echo $this->escape($item->title); ?>
							</td>
							<td>
								<?php echo $this->escape($item->access_level); ?>
							</td>
							<td>
								<?php echo $this->escape($item->category_title); ?>
							</td>
							<td>
								<?php
								if ($item->language == '*')
								{
									?>
									<?php echo JText::alt('JALL', 'language'); ?>
									<?php
								}
								else
								{
									?>
									<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
									<?php
								}
								?>
							</td>
							<td class="nowrap">
								<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
							</td>
							<td>
								<?php echo (int) $item->id; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td class="jsn-pagination" colspan="15">
							<?php echo str_replace('Joomla.submitform();', 'document.adminForm.submit();', $this->pagination->getListFooter()); ?>
						</td>
					</tr>
				</tfoot>
			</table>
			<div>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>
