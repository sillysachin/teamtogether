<?php
/**
 * @version     $Id$
 * @package     JSN_Framework
 * @subpackage  Config
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Create search coverages checkbox list for PowerAdmin.
 *
 * @since    1.0.0
 *
 */
class JFormFieldJSNPasearchcoverages extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'JSNPasearchcoverages';

    /**
     * Flag to tell the field to always be in multiple values mode.
     *
     * @var    boolean
     * @since  11.1
     */
    protected $forceMultiple = true;

    /**
     * Method to get the field input markup for check boxes.
     *
     * @return  string  The field input markup.
     *
     * @since   11.1
     */

    /**
     * Get the field label markup.
     *
     * @return  string
     */
    protected function getLabel()
    {
        // Preset label
        $label = '';

        if ($this->hidden)
        {
            return $label;
        }

        // Get the label text from the XML element, defaulting to the element name
        $text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
        $text = $this->translateLabel ? JText::_($text) : $text;

        // Build the class for the label
        $class = array ('control-label');
        //$class[] = ! empty($this->description) ? ' hasTip' : '';
        $class[] = $this->required == true ? ' required' : '';
        $class[] = ! empty($this->labelClass) ? ' ' . $this->labelClass : '';
        $class   = implode('', $class);

        // Add the opening label tag and class attribute
        $label .= '<label class="' . $class . '"';

        // If a description is specified, use it to build a tooltip
        if ( ! empty($this->description))
        {
            $label .= ' title="' . htmlspecialchars(trim($text, ':') . '::' . ($this->translateDescription ? JText::_($this->description) : $this->description), ENT_COMPAT, 'UTF-8') . '"';
        }

        // Add the label text and closing tag
        $label .= '>' . $text . ($this->required ? '<span class="star">&#160;*</span>' : '') . '</label>';

        return $label;
    }

    protected function getInput()
    {
        JSNFactory::localimport('helpers.poweradmin');
        $config = JSNConfigHelper::get('com_poweradmin');

        $searchCoverages = PoweradminHelper::getSearchCoverages();

        if(isset($config->search_coverage_order)){
            $searchCoveragesOrder   = explode(",", $config->search_coverage_order) ;
            // Add new coverage if it did not
            // exist in database
            if(count($searchCoverages)) {
            	foreach ($searchCoverages as $coverage) {
            		if(!in_array($coverage, $searchCoveragesOrder)) {
            			array_push($searchCoveragesOrder, $coverage);
            		}
            	}
            }
        }else{
            $searchCoveragesOrder   = $searchCoverages;
        }

        // $selectedCoverages = $this->item->get('search_coverage', $searchCoverages);

        if($this->value){
        	$selectedCoverages = json_decode($this->value);
        }else{
        	$selectedCoverages	= PoweradminHelper::getSearchCoverages();
        }

        $html[] = '<ul class="sortable">';
        foreach ($searchCoveragesOrder as $coverage) {
        	if ($coverage)
        	{
        		if (strpos($coverage, JSN_3RD_EXTENSION_STRING) !== false && !count(JPluginHelper::getPlugin('jsnpoweradmin', str_replace(JSN_3RD_EXTENSION_STRING . '-', '', $coverage))))
        		{
        			continue;
        		}
        		else
        		{
        			$checked = in_array($coverage, $selectedCoverages) ? 'checked' : '';
        			$html[] = '<li class="item" id="' . $coverage . '">
        			<ins class="sortable-handle"></ins>
        			<label class="checkbox">
        			<input type="checkbox" name="' . $this->name . '" value="' . $coverage . '" ' . $checked . ' />
        			' . JText::_('JSN_POWERADMIN_COVERAGE_'. str_ireplace(JSN_3RD_EXTENSION_STRING . '-', '',  strtoupper($coverage))) . '
        			</label>
        			<div class="clearbreak"></div>
        			</li>';
        		}
        	}
        }
        $html[] = '</ul>';
        $html[] = '<input type="hidden" value="' . implode(',', $searchCoveragesOrder) . '" id="params_search_coverage_order" name="jsnconfig[search_coverage_order]" />';

        return implode($html);
    }

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   11.1
     */
    protected function getOptions()
    {
        JSNFactory::localimport('helpers.poweradmin');
        // Initialize variables.
        $options = array();

        //get predefined search coverages
        $options = PoweradminHelper::getSearchCoverages();

        return $options;
    }
}
