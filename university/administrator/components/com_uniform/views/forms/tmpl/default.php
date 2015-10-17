<?php
/**
 * @version     $Id: default.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Forms
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
// Display messages
if (JFactory::getApplication()->input->getInt('ajax') != 1)
{
	echo $this->msgs;
}
?>
<div class="jsn-page-list">
    <div class="jsn-bootstrap">
        <form action="<?php echo JRoute::_('index.php?option=com_uniform&view=forms'); ?>" method="post" class="form-inline" id="adminForm" name="adminForm">
			<?php
			$JSNItemList = new JSNItemlistGenerator($this->getModel());
			$JSNItemList->addColumn('', 'form_id', 'checkbox', array('checkall' => true, 'name' => 'cid[]', 'class' => 'jsn-column-select', 'onclick' => 'Joomla.isChecked(this.checked);'));
			$JSNItemList->addColumn('JGLOBAL_TITLE', 'form_title', 'link', array('class' => 'jsn-column-title', 'sortTable' => 'uf.form_title', 'link' => 'index.php?option=com_uniform&view=form&task=form.edit&form_id={$form_id}'));
			$JSNItemList->addColumn('JSN_UNIFORM_FORM_SUBMISSION', 'form_submission_cout', 'link', array('class' => 'jsn-column-medium', 'sortTable' => 'uf.form_submission_cout', 'link' => 'index.php?option=com_uniform&view=submissions&filter_form_id={$form_id}'));
			$JSNItemList->addColumn('JSN_UNIFORM_FORM_LAST_SUBMITTED_AT', 'form_last_submitted', 'custom', array('class' => 'jsn-column-large', 'obj' => $this, 'method' => 'renderCustomDateLastSubmission'));
			$JSNItemList->addColumn('JSTATUS', 'form_state', 'published', array('class' => 'jsn-column-published', 'sortTable' => 'uf.form_state'));
			//$JSNItemList->addColumn('JGRID_HEADING_ACCESS', 'access_level', '', array('class' => 'jsn-column-access', 'sortTable' => 'uf.form_access'));
			$JSNItemList->addColumn('JGRID_HEADING_ID', 'form_id', '', array('class' => 'jsn-column-id', 'sortTable' => 'uf.form_id'));
			echo $JSNItemList->generateFilter();
			echo $JSNItemList->generate();
			?>
			<?php echo JHtml::_('form.token'); ?>
        </form>

    </div>
</div>
<?php
// Display footer
JSNHtmlGenerate::footer();
