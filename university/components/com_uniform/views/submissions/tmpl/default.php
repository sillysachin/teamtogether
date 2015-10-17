<?php

/**
 * @version     $Id: default.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Submissions
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

// load tooltip behavior

JHtml::_('behavior.tooltip');

$arrayScriptField = "";
$arrayField = array();
$formId = !empty($this->_formId) ? $this->_formId : "";
$listOrder = $this->escape($this->_state->get('list.ordering'));
$listDirn = $this->escape($this->_state->get('list.direction'));
$listPositionField = $this->escape($this->_state->get('filter.position_field' . $formId));
$listViewField = $this->escape($this->_state->get('filter.list_view_field' . $formId));
$dateSubmission = $this->escape($this->_state->get('filter.date_submission' . $formId));


if ($formId)
{
	$fieldTitle = $this->_viewField['fields']['title'];
	$fieldType = $this->_viewField['fields']['type'];
	$fieldIdentifier = $this->_viewField['fields']['identifier'];
	$fieldSort = $this->_viewField['fields']['sort'];
	$styleClass = $this->_viewField['fields']['styleclass'];
	$listViewField = $this->_viewField['field_view'];

	if (!$listPositionField)
	{
		$listPositionField = implode(",", $fieldIdentifier);
	}

	$arrayScriptField = str_replace("&quot;", '"', $listViewField);
	$arrayField = explode(",", str_replace("&quot;", '', $listViewField));
}
$listUser = JSNUniformHelper::getUserNameById();
$app = JFactory::getApplication();
$params = $app->getParams();
$showDataFilter = $params->get("show_data_filter");
$showDateFilter = $params->get("show_date_filter");
$showFieldSelector = $params->get("show_field_selector");

?>
<div id="submissions-list" class="jsn-page-list jsn-master">
	<div class="jsn-bootstrap">
		<form action="<?php echo JRoute::_('index.php?option=com_uniform&view=submissions'); ?>" class="form-inline" method="post" id="adminForm" name="adminForm">
			<fieldset class="jsn-fieldset-filter">
				<?php
				if ($formId && ($showDataFilter == "1" || $showDateFilter == "1"))
				{
					?>
					<div class="pull-left jsn-fieldset-search">
						<label class="filter-search-lbl"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
						<?php
						if ($showDataFilter == "1")
						{
							?>
							<input type="text" class="input-medium" name="filter_search<?php echo $formId;?>" id="filter_search" value="<?php echo $this->escape($this->_state->get('filter.search' . $formId)); ?>" title="<?php echo JText::_('JSN_UNIFORM_FORM_SEARCH_IN_TITLE'); ?>" />
							<?php
						}
						if ($showDateFilter == "1")
						{
							?>
							<input type="text" class="input-medium" placeholder="- <?php echo JText::_('JSN_UNIFORM_PLACEHOLDER_SELECT_DATE'); ?> -" name="filter_date_submission<?php echo $formId;?>" id="filter_date_submission" value="<?php echo $this->escape($this->_state->get('filter.date_submission' . $formId)); ?>" title="<?php echo JText::_('JSN_UNIFORM_FORM_SEARCH_IN_DATE_SUBMISSION'); ?>" />
							<?php
						}
						?>
						<button class="btn btn-icon" type="submit"><i class="icon-search"></i>
						</button>
						<button class="btn btn-icon" type="button" onclick="
							<?php
							if ($showDataFilter == "1")
							{
								?>document.id('filter_search').value = '';
								<?php
							}
							if ($showDateFilter == "1")
							{
								?>
							  document.id('filter_date_submission').value = '';
								<?php
							}
							?>
						  document.id('list_view_field').value = '';
						  document.id('filter_position_field').value = '';
						  this.form.submit();"><i class="icon-remove"></i>
						</button>
					</div>
					<?php
				}
				?>
				<?php
				if ($formId && $showFieldSelector == "1")
				{
					?>
					<div class="pull-right jsn-fieldset-select">
						<button class="select-field btn btn-icon" onclick="return false;" title="<?php echo JText::_('JSN_UNIFORM_SELECT_FIELDS'); ?>" id="select_field">
							<i class="icon-list-alt"></i>
						</button>
					</div>
					<div id="submission-fields-list" class="jsn-master jsn-bootstrap">
						<div class="popover bottom">
							<div class="arrow"></div>
							<h3 class="popover-title"><?php echo JText::_('JSN_UNIFORM_SELECT_FIELDS'); ?></h3>

							<div class="popover-content">
								<?php
								$itemsDisabled = "";
								$items = "";
								for ($i = 0; $i < count($fieldIdentifier); $i++)
								{
									$checked = "";
									if (in_array($fieldIdentifier[$i], $arrayField))
									{
										$checked = 'checked="true"';
									}
									$items .= '<li class="' . $styleClass[$i] . ' jsn-item ui-state-default"><label class="checkbox"><input ' . $checked . ' type="checkbox" title="' . JText::_($fieldTitle[$i]) . '" name="field[]" value="' . $fieldIdentifier[$i] . '">' . JText::_($fieldTitle[$i]) . '</label></li>';
								}
								?>
								<ul class="jsn-items-list ui-sortable">
									<?php echo $items;?>
								</ul>
								<div class="form-actions">
									<input type="button" class="btn btn-primary" id="done" onclick="return false;" name="done" value="Done">
								</div>
							</div>
						</div>

					</div>
					<?php
				}
				?>
			</fieldset>
			<?php
			if ($formId)
			{
				?>
				<table class="table table-bordered table-striped jsn-table-centered">
					<thead>
					<tr>
						<?php
						for ($i = 0; $i < count($fieldIdentifier); $i++)
						{
							if (in_array($fieldIdentifier[$i], $arrayField))
							{
								echo "<th class='" . $fieldIdentifier[$i] . "'>" . JHtml::_('grid.sort', $fieldTitle[$i], $fieldSort[$i], $listDirn, $listOrder) . "</th>";
							}
						}
						?>
					</tr>
					</thead>
					<tbody>
						<?php
						if ($this->_items)
						{
							foreach ($this->_items as $i => $item)
							{
								?>
							<tr class="row<?php echo $i % 2; ?>">
								<?php
								if (is_array($arrayField))
								{

									foreach ($arrayField as $j => $field)
									{
										$contentField = "";
										if (isset($fieldType[$field]))
										{
											$contentField = JSNUniformHelper::getDataField($fieldType[$field], $item, $field, $formId, true, true, 'list');
											$contentField = $contentField ? str_replace("\n", "<br/>", trim($contentField)) : "<span>N/A</span>";
											if ($j < 1)
											{
												$contentField = '<td><a href="' . JRoute::_('index.php?option=com_uniform&view=submission&submission_id=' . (int) $item->submission_id) . '">' . $contentField . '</a> </td>';
											}
											elseif ($field == 'submission_created_by' && !$item->$field)
											{
												$contentField = isset($listUser[$item->$field]) ? $listUser[$item->$field] : "Guest";
												$contentField = "<td>{$contentField}</td>";
											}
											else
											{
												$contentField = "<td>{$contentField}</td>";
											}
											echo $contentField;
										}
									}
								}
								?>
							</tr>
								<?php
							}
						}
						else
						{

							?>
						<tr>
							<td style="text-align: center;" colspan="<?php echo count($arrayField) + 3; ?>">
								<?php echo JText::_('JSN_UNIFORM_NO_DATA'); ?>
							</td>
						</tr>
							<?php
						}
						?>
					</tbody>
				</table>
				<?php
			}
			?>
			<div>
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<input type="hidden" name="list_view_field<?php echo $formId;?>" id="list_view_field" value="<?php echo $listViewField; ?>" />
				<input type="hidden" name="filter_position_field<?php echo $formId;?>" id="filter_position_field" value="<?php echo $listPositionField; ?>" />
				<input type="hidden" name="task" value="" />

				<?php echo JHtml::_('form.token'); ?>
				<?php
				$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "free";
				if (strtolower($edition) == "free")
				{
					echo "<div class=\"jsn-text-center\"><a href=\"http://www.joomlashine.com/joomla-extensions/jsn-uniform.html\" target=\"_blank\">" . JText::_('JSN_UNIFORM_POWERED_BY') . "</a> by <a href=\"http://www.joomlashine.com\" target=\"_blank\">JoomlaShine</a></div>";
				}
				?>
			</div>
		</form>
	</div>
	<div class="pagination">
		<?php echo $this->_pagination->getPagesLinks(); ?>
	</div>
</div>
