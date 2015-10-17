<?php
/**
 * @version     $Id: default.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Upgrade
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Display messagess
if (JFactory::getApplication()->input->getInt('ajax') != 1)
{
	echo $this->msgs;
}
$upgradeBenefitsFree = '<ul>';
$upgradeBenefitsFree .= '<li>' . JText::_('JSN_UNIFORM_UPGRADE_UNLIMITED_NUMBER_OF_FORM_AND_FIELDS_IN_A_FORM') . '</li>';
$upgradeBenefitsFree .= '<li>' . JText::_('JSN_UNIFORM_UPGRADE_UNLIMITED_NUMBER_OF_SUBMISSION_IN_A_FORM') . '</li>';
$upgradeBenefitsFree .= '<li>' . JText::_('JSN_UNIFORM_UPGRADE_ABILITY_TO_PRESENT_FORM_FIELDS_IN_MULTIPLE_PAGES') . '</li>';
$upgradeBenefitsFree .= '<li>' . JText::_('JSN_UNIFORM_UPGRADE_REMOVED_BRANDLINK_FROM_FORM_PRESENTATION') . '</li>';
$upgradeBenefitsFree .= '<li>' . JText::_('JSN_UNIFORM_UPGRADE_PROFESSIONAL_SUPPORT_FOR_01_DOMAIN') . '</li>';
$upgradeBenefitsFree .= '<li>' . JText::_('JSN_UNIFORM_UPGRADE_FREE_PRODUCT_UPDATE_FOR_06_MONTHS') . '</li>';
$upgradeBenefitsFree .= '</ul>';
$upgradeBenefitsPro = '<ul>';
$upgradeBenefitsPro .= '<li>' . JText::_('JSN_UNIFORM_UPGRADE_ALL_BENEFITS_OF_PRO_STADARD_EDITION') . '</li>';
$upgradeBenefitsPro .= '<li>' . JText::_('JSN_UNIFORM_UPGRADE_FASTER_DEDICATED_SUPPORT_FOR_UNLIMITED_DOMAINS') . '</li>';
$upgradeBenefitsPro .= '<li>' . JText::_('JSN_UNIFORM_UPGRADE_FREE_PRODUCT_UPDATE_FOR_01_YEAR') . '</li>';
$upgradeBenefitsPro .= '</ul>';
// Display config form
JSNUpgradeHelper::render($this->product, $upgradeBenefitsFree, $upgradeBenefitsPro);
