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
<form action="index.php?option=com_poweradmin&view=article" method="post" name="adminForm" id="item-form" class="form-validate">
<div id="jsn_tabs">
	<ul>
		<li><a href="#tabs-1"><?php echo JText::_('JSN_ARTICLE_VIEW_CONTENT_TAB');?></a></li>
		<li><a href="#tabs-2"><?php echo JText::_('JSN_ARTICLE_VIEW_OPTIONS_TAB');?></a></li>
		<li><a href="#tabs-3"><?php echo JText::_('JSN_ARTICLE_VIEW_PWERMISSION_TAB');?></a></li>
	</ul>
	<div id="tabs-1" class="tabs">
		<div class="width-100">
			<fieldset class="adminform">
				<div class="width-100 fltlft">
					<div class="width-50 fltlft">
						<ul class="adminformlist">
							<li><?php echo $this->form->getLabel('title'); ?>
							<?php echo $this->form->getInput('title'); ?></li>

							<li><?php echo $this->form->getLabel('alias'); ?>
							<?php echo $this->form->getInput('alias'); ?></li>

							<li><?php echo $this->form->getLabel('state'); ?>
							<?php echo $this->form->getInput('state'); ?></li>

							<li><?php echo $this->form->getLabel('id'); ?>
							<?php echo $this->form->getInput('id'); ?></li>
						</ul>
					</div>
					<div class="width-50 fltlft">
						<ul class="adminformlist">
							<li><?php echo $this->form->getLabel('catid'); ?>
							<?php echo $this->form->getInput('catid'); ?></li>

							<li><?php echo $this->form->getLabel('access'); ?>
							<?php echo $this->form->getInput('access'); ?></li>

							<li><?php echo $this->form->getLabel('featured'); ?>
							<?php echo $this->form->getInput('featured'); ?></li>

							<li><?php echo $this->form->getLabel('language'); ?>
							<?php echo $this->form->getInput('language'); ?></li>
						</ul>
					</div>
				</div>
				<div class="clearbreak"></div>
				<?php echo $this->form->getLabel('articletext'); ?>
				<div class="clearbreak"></div>
				<?php echo $this->form->getInput('articletext'); ?>
			</fieldset>
		</div>
	</div>
	<div id="tabs-2" class="tabs">
		<div class="width-100 fltlft">
			<div class="width-50 fltlft">
				<fieldset class="adminform">
					<legend><?php echo JText::_('JSN_ARTICLE_VIEW_METADATA_TITLE');?></legend>
					<ul class="adminformlist">
						<li><?php echo $this->form->getLabel('metadesc'); ?>
						<?php echo $this->form->getInput('metadesc'); ?></li>
					
						<li><?php echo $this->form->getLabel('metakey'); ?>
						<?php echo $this->form->getInput('metakey'); ?></li>
					
					
					<?php foreach($this->form->getGroup('metadata') as $field): ?>
						<li>
							<?php if (!$field->hidden): ?>
								<?php echo $field->label; ?>
							<?php endif; ?>
							<?php echo $field->input; ?>
						</li>
					<?php endforeach; ?>
					</ul>
				</fieldset>
			</div>
			<div class="width-50 fltlft">
				<fieldset class="adminform">
					<legend><?php echo JText::_('JSN_ARTICLE_VIEW_PUBLISHING_TITLE');?></legend>
					<ul class="adminformlist">
						<li><?php echo $this->form->getLabel('created_by'); ?>
						<?php echo $this->form->getInput('created_by'); ?></li>
	
						<li><?php echo $this->form->getLabel('created_by_alias'); ?>
						<?php echo $this->form->getInput('created_by_alias'); ?></li>
	
						<li><?php echo $this->form->getLabel('created'); ?>
						<?php echo $this->form->getInput('created'); ?></li>
	
						<li><?php echo $this->form->getLabel('publish_up'); ?>
						<?php echo $this->form->getInput('publish_up'); ?></li>
	
						<li><?php echo $this->form->getLabel('publish_down'); ?>
						<?php echo $this->form->getInput('publish_down'); ?></li>
	
						<?php if ($this->item->modified_by) : ?>
							<li><?php echo $this->form->getLabel('modified_by'); ?>
							<?php echo $this->form->getInput('modified_by'); ?></li>
	
							<li><?php echo $this->form->getLabel('modified'); ?>
							<?php echo $this->form->getInput('modified'); ?></li>
						<?php endif; ?>
	
						<?php if ($this->item->version) : ?>
							<li><?php echo $this->form->getLabel('version'); ?>
							<?php echo $this->form->getInput('version'); ?></li>
						<?php endif; ?>
	
						<?php if ($this->item->hits) : ?>
							<li><?php echo $this->form->getLabel('hits'); ?>
							<?php echo $this->form->getInput('hits'); ?></li>
						<?php endif; ?>
					</ul>
				</fieldset>
			</div>
		</div>
		<div class="clearbreak"></div>
	</div>
	<div id="tabs-3" class="tabs ">
		<?php if ($this->canDo->get('core.admin')): ?>
			<div class="width-100 fltlft">
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			</div>
		<?php endif; ?>
		<div class="clearbreak"></div>
	</div>
</div>
<input type="hidden" name="option" value="com_poweradmin" />
<input type="hidden" name="view" value="article" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="id" value="<?php echo $this->item->id;?>" />
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>
