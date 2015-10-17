<?php
/**
 * nnHtml
 * extra JHTML functions
 *
 * @package         NoNumber Framework
 * @version         13.4.4
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2012 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * nnHtml
 */
class nnHtml
{
	static function selectlist(&$options, $name, $value, $id, $size = 0, $multiple = 0)
	{
		if (empty($options)) {
			return '<fieldset class="radio">' . JText::_('NN_NO_ITEMS_FOUND') . '</fieldset>';
		}

		if (!is_array($value)) {
			$value = explode(',', $value);
		}

		$count = 0;
		if ($options != -1) {
			foreach ($options as $option) {
				$count++;
				if (isset($option->links)) {
					$count += count($option->links);
				}
				if ($count > 2500) {
					break;
				}
			}
		}

		if ($options == -1 || $count > 2500) {
			if (is_array($value)) {
				$value = implode(',', $value);
			}
			if (!$value) {
				$input = '<textarea name="' . $name . '" id="' . $id . '" cols="40" rows="5" />' . $value . '</textarea>';
			} else {
				$input = '<input type="text" name="' . $name . '" id="' . $id . '" value="' . $value . '" size="60" />';
			}
			return '<fieldset class="radio"><label for="' . $id . '">' . JText::_('NN_ITEM_IDS') . ':</label>' . $input . '</fieldset>';
		}

		$size = $size ? $size : 300;

		if (!$multiple) {
			return JHtml::_('select.genericlist', $options, $name, ' class="inputbox"', 'value', 'text', $value);
		}

		JHtml::stylesheet('nnframework/multiselect.min.css', false, true);
		JHtml::script('nnframework/multiselect.min.js', false, true);

		$html = array();

		$html[] = '<div class="well well-small nn_multiselect" id="' . $id . '">';
		$html[] = '
			<div class="form-inline nn_multiselect-controls">
				<span class="small">' . JText::_('JSELECT') . ':
					<a class="nn_multiselect-checkall" href="javascript://">' . JText::_('JALL') . '</a>,
					<a class="nn_multiselect-uncheckall" href="javascript://">' . JText::_('JNONE') . '</a>,
					<a class="nn_multiselect-toggleall" href="javascript://">' . JText::_('NN_TOGGLE') . '</a>
				</span>
				<span class="width-20">|</span>
				<span class="small">' . JText::_('NN_EXPAND') . ':
					<a class="nn_multiselect-expandall" href="javascript://">' . JText::_('JALL') . '</a>,
					<a class="nn_multiselect-collapseall" href="javascript://">' . JText::_('JNONE') . '</a>
				</span>
				<span class="width-20">|</span>
				<span class="small">' . JText::_('JSHOW') . ':
					<a class="nn_multiselect-showall" href="javascript://">' . JText::_('JALL') . '</a>,
					<a class="nn_multiselect-showselected" href="javascript://">' . JText::_('NN_SELECTED') . '</a>
				</span>
				<span class="nn_multiselect-maxmin">
				<span class="width-20">|</span>
				<span class="small">
					<a class="nn_multiselect-maximize" href="javascript://">' . JText::_('NN_MAXIMIZE') . '</a>
					<a class="nn_multiselect-minimize" style="display:none;" href="javascript://">' . JText::_('NN_MINIMIZE') . '</a>
				</span>
				</span>
				<input type="text" name=""nn_multiselect-filter" class="nn_multiselect-filter input-medium search-query pull-right" size="16"
					autocomplete="off" placeholder="' . JText::_('JSEARCH_FILTER') . '" aria-invalid="false" tabindex="-1">
			</div>

			<div class="clearfix"></div>

			<hr class="hr-condensed" />';

		$o = array();
		foreach ($options as $option) {
			$option->level = isset($option->level) ? $option->level : 0;
			$o[] = $option;
			if (isset($option->links)) {
				foreach ($option->links as $link) {
					$link->level = isset($link->level) ? $option->level + 1 : 1;
					$o[] = $link;
				}
			}
		}

		$html[] = '<ul class="nn_multiselect-ul" style="max-height:' . (int) $size . 'px;overflow-x: hidden;">';
		$prevlevel = 0;

		foreach ($o as $i => $option) {
			if ($prevlevel < $option->level) {
				$html[] = '<ul class="nn_multiselect-sub">';
			} else if ($prevlevel > $option->level) {
				$html[] = str_repeat('</li></ul>', $prevlevel - $option->level);
			} else if ($i) {
				$html[] = '</li>';
			}
			$html[] = '<li>';
			if (isset($option->title)) {
				$html[] = '<div class="nn_multiselect-item pull-left">
					<label class="pull-left nav-header">' . $option->title . '</label>
				</div>';
			} else {
				$selected = in_array($option->value, $value) ? ' checked="checked"' : '';
				$disabled = (isset($option->disable) && $option->disable) ? ' readonly="readonly" style="visibility:hidden"' : '';
				$html[] = '<div class="nn_multiselect-item pull-left">
					<input type="checkbox" class="pull-left" name="' . $name . '[]" id="' . $id . $option->value . '" value="' . $option->value . '"' . $selected . $disabled . ' />
					<label for="' . $id . $option->value . '" class="pull-left">' . $option->text . '</label>
				</div>';
			}

			if (!isset($o[$i + 1])) {
				$html[] = str_repeat('</li></ul>', $option->level);
			}
			$prevlevel = $option->level;
		}
		$html[] = '</ul>';
		$html[] = '
			<div style="display:none;" class="nn_multiselect-menu-block">
				<div class="pull-left nav-hover nn_multiselect-menu">
					<div class="btn-group">
						<a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-micro">
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<li class="nav-header">' . JText::_('COM_MODULES_SUBITEMS') . '</li>
							<li class="divider"></li>
							<li class=""><a class="checkall" href="javascript://"><i class="icon-checkbox"></i> ' . JText::_('JSELECT') . '</a>
							</li>
							<li><a class="uncheckall" href="javascript://"><i class="icon-checkbox-unchecked"></i> ' . JText::_('COM_MODULES_DESELECT') . '</a>
							</li>
							<div class="nn_multiselect-menu-expand">
								<li class="divider"></li>
								<li><a class="expandall" href="javascript://"><i class="icon-plus"></i> ' . JText::_('NN_EXPAND') . '</a></li>
								<li><a class="collapseall" href="javascript://"><i class="icon-minus"></i> ' . JText::_('NN_COLLAPSE') . '</a></li>
							</div>
						</ul>
					</div>
				</div>
			</div>';
		$html[] = '</div>';

		$html = implode('', $html);

		return preg_replace('#>\[\[\:(.*?)\:\]\]#si', ' style="\1">', $html);
	}
}
