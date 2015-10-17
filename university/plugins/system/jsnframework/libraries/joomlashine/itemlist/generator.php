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

/**
 * Helper class for Item lists Generate
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNItemlistGenerator
{
	/**
	 * JModelList
	 * @var  $_model
	 */
	protected $_model;

	/**
	 * The model state
	 * @var  object
	 */
	protected $_state;

	/**
	 *  The items to list
	 * @var array
	 */
	protected $_items;

	/**
	 * The pagination object
	 * @var object
	 */
	protected $_pagination;

	/**
	 * The ordering filtering fields white list
	 * @var string
	 */
	protected $_listOrder;

	/**
	 * An optional direction (asc|desc).
	 * @var string
	 */
	protected $_listDirn;

	/**
	 * The component name
	 * @var string
	 */
	protected $_component;

	/**
	 * The view name
	 * @var string
	 */
	protected $_view;

	/**
	 * The list options
	 * @var array
	 */
	protected $_options;

	/**
	 * The list column
	 * @var array
	 */
	protected $_column;

	/**
	* add JModelList
	*
	* @param   JModelList  $model  Model class for handling lists of items
	*/
	public function JSNItemlistGenerator(JModelList $model)
	{
		// Get input object
		$input = JFactory::getApplication()->input;

		$this->_model = $model;
		$this->_state = $this->_model->getState();
		$this->_items = $this->_model->getItems();
		$this->_pagination = $this->_model->getPagination();
		$this->_listOrder = $this->_state->get('list.ordering');
		$this->_listDirn = $this->_state->get('list.direction');
		$this->_component = $input->getCmd('option');
		$this->_view = $input->getCmd('view');
	}

	/**
	 * Add column to item lists
	 *
	 * @param   type  $title    Title header column
	 * @param   type  $field    filed name
	 * @param   type  $type     type column
	 * @param   type  $options  options column
	 *
	 * @return  void
	 */
	public function addColumn($title, $field, $type, $options = null)
	{
		$this->_column[] = array(
		'title' => $title,
		'field'   => $field,
		'type'	=> $type,
		'options' => $options
		);
	}

	/**
	 *  Generate html code for a filter
	 *
	 * 	@return  html code filter
	 */
	public function generateFilter()
	{
		$this->_filterForm = new JForm('Item Filter');
		$this->_filterForm->loadFile(JPATH_COMPONENT_ADMINISTRATOR . '/views/' . $this->_view . '/filter.xml', true, '/filter');

		$fieldSet	  = $this->_filterForm->getFieldSet('filters');
		$positionRight = '';
		$positionLeft  = '';

		foreach ($fieldSet AS $object)
		{
			if ($this->_filterForm->getFieldAttribute($object->name, 'position') == "right")
			{
				$positionRight .= $object->input;
			}
			else
			{
				$positionLeft .= $object->input;
			}
		}
		return "<div class=\"jsn-fieldset-filter\"><fieldset>
					<div class=\"pull-left jsn-fieldset-search\">{$positionLeft}</div>
					<div class=\"pull-right jsn-fieldset-select\">{$positionRight}</div>
					<div class=\"clearbreak\"></div>
				  </fieldset></div>";
	}

	/**
	 * Generate html code for a table which includes all the required column
	 *
	 * @return  html code
	 */
	public function generate()
	{
		$thead = '';
		$tbody = '';
		$thead .= "<th width=\"2%\">#</th>";

		if (is_array($this->_column) && count($this->_column) > 0)
		{
			foreach ($this->_column as $column)
			{
				$thead .= $this->getColumnHeader($column);
			}
		}

		if ( ! empty($this->_items) && count($this->_items) > 0)
		{
			foreach ($this->_items as $index => $items)
			{
				$tbody .= $this->getColumnItems($items, $index);
			}
		}

		$countColumn = count($this->_column) + 2;
		$html		= "<table class=\"table table-bordered table-striped jsn-table-centered\">
							<thead>{$thead}</thead>
							<tbody>{$tbody}</tbody>
							<tfoot><tr><td colspan=\"{$countColumn}\">{$this->_pagination->getListFooter()}</td></tr></tfoot>
						  </table>
						  <input type=\"hidden\" name=\"option\" value=\"{$this->_component}\" />
						  <input type=\"hidden\" name=\"task\" value=\"\" />
						  <input type=\"hidden\" name=\"view\" value=\"{$this->_view}\" />
						  <input type=\"hidden\" name=\"boxchecked\" value=\"0\" />
						  <input type=\"hidden\" name=\"filter_order\" value=\"{$this->_listOrder}\" />
						  <input type=\"hidden\" name=\"filter_order_Dir\" value=\"{$this->_listDirn}\" />";

		return $html;
	}

	/**
	 * Generate html code for a columns header
	 *
	 * @param   array  $column  options column
	 *
	 * @return  html code
	 */
	public function getColumnHeader($column)
	{

		$customSort   = isset($column['options']['sortTable']) ? $column['options']['sortTable'] : '';
		$type		 = isset($column['type']) ? $column['type'] : '';
		$fieldTitle   = isset($column['title']) ? $column['title'] : '';
		$checAll	  = isset($column['options']['checkall']) ? $column['options']['checkall'] : '';
		$classDefault = !empty($type) ? 'header-' . $type . ' ' : '';
		$class		= isset($column['options']['classHeader']) ? ' class="' . $classDefault . $column['options']['classHeader'] . '"' : 'class="' . $classDefault . '"';

		$html = "<th nowrap=\"nowrap\"{$class}>";

		if ( ! empty($customSort))
		{
			if ($customSort && $type == 'ordering')
			{
				$html .= JHtml::_('grid.sort', strtoupper($fieldTitle), $customSort, $this->_listDirn, $this->_listOrder);
				$html .= JHtml::_('grid.order', $this->_items, 'filesave.png', $this->_view . '.saveorder');
			}
			else
			{
				$html .= JHtml::_('grid.sort', strtoupper($fieldTitle), $customSort, $this->_listDirn, $this->_listOrder);
			}
		}
		else
		{
			if ($type == 'checkbox' && $checAll)
			{
				$countItems = count($this->_items);
				$html .= "<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"Joomla.checkAll(this);\" />";
			}
			else
			{
				$html .= JText::_(strtoupper($fieldTitle));
			}
		}

		$html .= "</th>";

		return $html;
	}

	/**
	 * Generate html code for a columns items
	 *
	 * @param   array  $items  items to list
	 * @param   int    $index  Index number
	 *
	 * @return  html code
	 */
	public function getColumnItems($items, $index)
	{
		$html = '';
		$html .= "<td>{$this->_pagination->getRowOffset($index)}</td>";

		foreach ($this->_column AS $column)
		{
			$type		= isset($column['type']) ? $column['type'] : '';
			$optionType  = preg_replace('/[^a-zA-Z]/i', '', $type);
			$filed	   = isset($column['field']) ? $column['field'] : '';
			$dataCoulumn = isset($items->$filed) ? $items->$filed : '';
			$method	  = "columnType{$optionType}";
			$options	 = array('option'	 => $column['options'], 'items'	  => $items, 'title'	  => $column['title'], 'index'	  => $index, 'dataColumn' => $dataCoulumn);
			$class	   = isset($column['options']['class']) ? ' class="' . $column['options']['class'] . '"' : '';

			if (method_exists('JSNItemlistGenerator', $method))
			{
				$html .= "<td{$class}>{$this->$method($options)}</td>";
			}
			else
			{
				$html .= "<td{$class}>{$this->columnTypeText($options)}</td>";
			}
		}

		return "<tr>{$html}</tr>";
	}

	/**
	 * Generate html code for colum type "Checkbox"
	 *
	 * @param   type  $options  array('dataColumn'=>(Object Data),'name'=>'name checkbox','index')
	 *
	 * @return  html code
	 */
	public function columnTypeCheckbox($options)
	{
		$value	  = isset($options['dataColumn']) ? $options['dataColumn'] : '';
		$name	   = isset($options['option']['name']) ? $options['option']['name'] : '';
		$eventClick = isset($options['option']['onclick']) ? 'onclick="' . $options['option']['onclick'] . '"' : '';
		$html	   = "<input type=\"checkbox\" title=\"Checkbox for row {$options['index']}\" {$eventClick} value=\"{$value}\" name=\"{$name}\" id=\"cb{$options['index']}\">";

		return $html;
	}

	/**
	 * Generate html code for colum type "Custom"
	 *
	 * @param   type  $options  $options array('option object','method','items')
	 *
	 * @return  html code
	 */
	public function columnTypeCustom($options)
	{
		if (is_object($options['option']['obj']) && method_exists($options['option']['obj'], $options['option']['method']))
		{
			return call_user_func_array(array($options['option']['obj'], $options['option']['method']), array($options['items']));
		}
		return '';
	}

	/**
	 * Generate html code for colum type "Link"
	 *
	 * @param   type  $options  array('dataColumn'=>(Object Data),'link'=>(href link))
	 *
	 * @return  html code
	 */
	public function columnTypeLink($options)
	{
		$link		= isset($options['option']['link']) ? $options['option']['link'] : '';
		$dataColumn	= isset($options['dataColumn']) ? $options['dataColumn'] : '';

		if (preg_match_all('/\{\$([^\}]+)\}/', $link, $matches, PREG_SET_ORDER))
		{
			foreach ($matches AS $match)
			{
				$link = str_replace($match[0], @$options["items"]->{$match[1]}, $link);
			}
		}

		return "<a href=\"{$link}\">{$dataColumn}</a>";
	}

	/**
	 * Generate html code for colum type "Images"
	 *
	 * @param   type  $options  array('dataColumn'=>(Object Data),'srcRoot'=>(path root images))
	 *
	 * @return  html code
	 */
	public function columnTypeImages($options)
	{
		$srcRoot = isset($options['option']['srcRoot']) ? $options['option']['srcRoot'] : '';
		$images  = isset($options['dataColumn']) ? "<img src=\"{$srcRoot}{$options['dataColumn']}\" />" : '';

		return $images;
	}

	/**
	 * Generate html code for colum type "Text"
	 *
	 * @param   type  $options  array('dataColumn'=>(Object Data))
	 *
	 * @return  html code
	 */
	public function columnTypeText($options)
	{
		$dataColumn = isset($options['dataColumn']) ? $options['dataColumn'] : '';

		return $dataColumn;
	}

	/**
	 * Generate html code for colum type "Published"
	 *
	 * @param   type  $options  array('dataColumn'=>(Object Data),'index')
	 *
	 * @return  html code
	 */
	public function columnTypePublished($options)
	{
		$dataColumn = isset($options['dataColumn']) ? $options['dataColumn'] : '';
		$published  = JHtml::_('jgrid.published', $dataColumn, $options['index'], $this->_view . '.');

		return $published;
	}

	/**
	 * Generate html code for colum type "Ordering"
	 *
	 * @param   type  $options  array('dataColumn'=>(Object Data),'changeOrder'=>(true or false),index)
	 *
	 * @return  html code
	 */
	public function columnTypeOrdering($options)
	{
		$dataColumn = isset($options['dataColumn']) ? $options['dataColumn'] : '';

		if (isset($options['option']['changeOrder']) && $options['option']['changeOrder'] == "disabled")
		{
			$disabled	   = 'disabled="disabled"';
			$changeOrdering = false;
		}
		else
		{
			$disabled	   = '';
			$changeOrdering = true;
		}

		$ordering = "<div class=\"input-prepend\">";
		$ordering .= "<span class=\"add-on\">{$this->_pagination->orderUpIcon($options['index'], true, $this->_view . '.orderup', 'Move Up', $changeOrdering)}</span>";
		$ordering .= "<span class=\"add-on\">{$this->_pagination->orderDownIcon($options['index'], count($this->_items), true, $this->_view . '.orderdown', 'Move Down', $changeOrdering)}</span><input type=\"text\" name=\"order[]\" value=\"{$dataColumn}\" {$disabled} class=\"jsn-input-small-fluid jsn-input-ordering\" />";
		$ordering .= "</div >";

		return $ordering;
	}
}
