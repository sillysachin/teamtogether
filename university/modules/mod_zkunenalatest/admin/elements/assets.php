<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldAssets extends JFormField {
        protected $type = 'Assets';

        protected function getInput() {
                $doc = JFactory::getDocument();
                $doc->addScript(JURI::root().$this->element['path'].'script.js');
				$doc->addScript(JURI::root().$this->element['path'].'jscolor.js');
                $doc->addStyleSheet(JURI::root().$this->element['path'].'style.css');        
                return null;
        }
}