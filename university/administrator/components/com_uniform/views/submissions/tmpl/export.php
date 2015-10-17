<?php

/**
 * @version     $Id: $
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
set_time_limit(999999999999);
$formId = $this->_formId;
if ($formId)
{
	$fieldIdentifier = $this->_viewField['fields']['identifier'];
	$listViewField = $this->_viewField['field_view'];
	$fieldTitle = $this->_viewField['fields']['title'];
	$fieldType = $this->_viewField['fields']['type'];
	$arrayField = explode(",", str_replace("&quot;", '', $listViewField));
	$data = array();
	$dataItem = array();
	for ($i = 0; $i < count($fieldIdentifier); $i++)
	{
		if (in_array($fieldIdentifier[$i], $arrayField))
		{
			$dataItem[] = JText::_($fieldTitle[$i]);
		}
	}
	$dataItem[] = JText::_("JGRID_HEADING_ID");
	$data[] = $dataItem;
	if (is_array($arrayField))
	{
		if ($this->_dataExport)
		{
			foreach ($this->_dataExport as $i => $item)
			{
				$dataItem = array();
				foreach ($arrayField as $j => $field)
				{
					$contentField = "";
					if (isset($fieldType[$field]))
					{
						$contentField = JSNUniformHelper::getDataField($fieldType[$field], $item, $field, $formId, false, false, 'export');
						$contentField = $contentField ? $contentField : "";
						if ($field == 'submission_created_by' && !$item->$field)
						{
							$contentField = isset($listUser[$item->$field]) ? $listUser[$item->$field] : "Guest";
						}
						$dataItem[] = $contentField;
					}
				}
				$dataItem[] = $item->submission_id;
				$data[] = $dataItem;
			}
		}
	}
	if (isset($_GET['e']) && $_GET['e'] == "excel")
	{
		include_once JSN_UNIFORM_LIB_PHPEXCEL;
		// generate file (constructor parameters are optional)
		$xls = new Excel_XML('UTF-8', false, 'My Test Sheet');
		$xls->addArray($data);
		$xls->generateXML('jsn-uniform-' . $this->_infoForm->form_title . '-excel-' . date("Y-m-d"));
		exit();
	}
	else if (isset($_GET['e']) && $_GET['e'] == "csv")
	{
		$fileName = 'jsn-uniform-' . $this->_infoForm->form_title . '-csv-' . date("Y-m-d");
		$fileName = preg_replace('/[^aA-zZ0-9\_\-]/', '', $fileName);
		header("Content-type:text/octect-stream; charset=UTF-8");
		header("Content-Disposition:attachment;filename={$fileName}.csv");
		$output = fopen('php://output', 'w');
		foreach ($data as $items)
		{
			fputcsv($output, $items);
		}
		fclose($output);
		exit();
	}
}
