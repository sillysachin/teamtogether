<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: edit.php 13922 2012-07-12 04:23:17Z thangbh $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<form action="<?php echo JRoute::_('index.php?option=com_categories&extension=com_content&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
<div id="jsn_tabs">
	<ul>
		<li><a href="#tabs-1"><?php echo JText::_('JSN_ARTICLE_VIEW_CONTENT_TAB');?></a></li>
		<li><a href="#tabs-2"><?php echo JText::_('JSN_ARTICLE_VIEW_OPTIONS_TAB');?></a></li>
		<li><a href="#tabs-3"><?php echo JText::_('JSN_ARTICLE_VIEW_PWERMISSION_TAB');?></a></li>
	</ul>
	<div id="tabs-1" class="tabs">
		<fieldset class="adminform jsn-content-tab">
			<div class="width-100 fltlft">
				<div class="width-50 fltlft">
					<ul class="adminformlist">
						<li><?php echo $this->form->getLabel('title'); ?>
						<?php echo $this->form->getInput('title'); ?></li>

						<li><?php echo $this->form->getLabel('alias'); ?>
						<?php echo $this->form->getInput('alias'); ?></li>

						<li><?php echo $this->form->getLabel('state'); ?>
						<?php echo $this->form->getInput('state'); ?></li>

						<li><?php echo $this->form->getLabel('parent_id'); ?>
						<?php echo $this->form->getInput('parent_id'); ?></li>
						
						<li><?php echo $this->form->getLabel('id'); ?>
						<?php echo $this->form->getInput('id'); ?></li>
					</ul>
				</div>
				<div class="width-50 fltlft">
					<ul class="adminformlist">
						<li><?php echo $this->form->getLabel('extension'); ?>
						<?php echo $this->form->getInput('extension'); ?></li>

						<li><?php echo $this->form->getLabel('access'); ?>
						<?php echo $this->form->getInput('access'); ?></li>

						<li><?php echo $this->form->getLabel('published'); ?>
						<?php echo $this->form->getInput('published'); ?></li>

						<li><?php echo $this->form->getLabel('language'); ?>
						<?php echo $this->form->getInput('language'); ?></li>
					</ul>
				</div>
			</div>
			<div class="clearbreak"></div>
			<?php echo $this->form->getLabel('description'); ?>
			<div class="clearbreak"></div>
			<?php echo $this->form->getInput('description'); ?>
		</fieldset>
	</div>
	<div id="tabs-2" class="tabs">
		<div class="width-100 fltlft">
			<div class="width-50 fltlft">
				<fieldset class="adminform">
					<legend><?php echo JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS');?></legend>
					<ul class="adminformlist">
						<li><?php echo $this->form->getLabel('metadesc'); ?>
						<?php echo $this->form->getInput('metadesc'); ?></li>
					
						<li><?php echo $this->form->getLabel('metakey'); ?>
						<?php echo $this->form->getInput('metakey'); ?></li>
					
						<?php foreach($this->form->getGroup('metadata') as $field): ?>
							<?php if ($field->hidden): ?>
								<li><?php echo $field->input; ?></li>
							<?php else: ?>
								<li><?php echo $field->label; ?>
								<?php echo $field->input; ?></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</fieldset>
			</div>
			<div class="width-50 fltlft">
				<fieldset class="adminform">
					<legend><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING');?></legend>
					<ul class="adminformlist">
						<li><?php echo $this->form->getLabel('created_user_id'); ?>
						<?php echo $this->form->getInput('created_user_id'); ?></li>
						
						<?php if (intval($this->item->created_time)) : ?>
							<li><?php echo $this->form->getLabel('created_time'); ?>
							<?php echo $this->form->getInput('created_time'); ?></li>
						<?php endif; ?>
						
						<?php if ($this->item->modified_user_id) : ?>
							<li><?php echo $this->form->getLabel('modified_user_id'); ?>
							<?php echo $this->form->getInput('modified_user_id'); ?></li>
			
							<li><?php echo $this->form->getLabel('modified_time'); ?>
							<?php echo $this->form->getInput('modified_time'); ?></li>
						<?php endif; ?>
					</ul>
				</fieldset>
			</div>
		</div>
		<div class="clearbreak"></div>
		
	</div>
	<div id="tabs-3" class="tabs">
		<?php if ($this->canDo->get('core.admin')): ?>
			<div  class="width-100 fltlft">	
				<fieldset class="panelform jsn-content-tab">
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>	
			</div>
		<?php endif; ?>
		<div class="clearbreak"></div>
	</div>
<input type="hidden" name="option" value="com_poweradmin" />
<input type="hidden" name="view" value="category" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="id" value="<?php echo $this->item->id;?>" />
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>