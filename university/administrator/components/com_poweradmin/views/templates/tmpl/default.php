<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: default.php 15032 2012-08-13 12:32:51Z hiepnv $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

?>
<script type="text/javascript">
var openedTid = <?php echo JRequest::getInt('tid')?>;
</script>
<div id="manage-styles">
	<h3><a href="#"><?php echo JText::_('JSN_POWERADMIN_INSTALLED_TEMPLATES') ?></a></h3>
	<div id="installed-templates" class="jsn-bootstrap">
		<input type="hidden" name="openedSid" id="openedSid" value="<?php echo (int)JRequest::getInt('sid')?>">
        <div >
            <div class="switch-control">
                <select id="client-switch" class="client-switch-box">
                    <option value="0"><?php echo JText::_('JSITE')?></option>
                    <option value="1"><?php echo JText::_('JADMINISTRATOR')?></option>
                </select>
            </div>
            <div class="clearbreak hor"></div>
            <div class="template-list" id="site" >
                <div id="jsn-page-container" class="jsn-template-select-panel">
                    <?php foreach ($this->templates as $template): ?>
                    <?php
                    	// Set template id for current edited style
                    	if (!JRequest::getInt('tid') && $template->id == JRequest::getInt('sid')) {
                    		echo "<script type=\"text/javascript\">
	                    					if (openedTid <= 0) {
												openedTid = " . $template->tid . ";
	                    					}
                    				</script>";
                    	}
                    ?>
                        <?php $class = ($template->home == 1) ? 'template-item default' : 'template-item' ?>
                        <div class="<?php echo $class;?>" id="jTemplate-<?php echo $template->id;?>" clientId="<?php echo $template->client_id?>" sid="<?php echo $template->id;?>" tid="<?php echo $template->tid;?>">
                            <?php if (file_exists(JSN_POWERADMIN_TEMPLATE_PATH.'/'.$template->template.'/template_thumbnail.png')): ?>
                                <a class="template-item-thumb" href="javascript:;" >
                                    <span class="thumbnail">
                                        <img src="<?php echo JURI::root().'templates/'.$template->template.'/template_thumbnail.png'; ?>" alt="<?php echo $template->title;?>" align="center"/>
                                    </span>
                                    <span><?php echo $template->title;?></span>
                                </a>
                            <?php else: ?>
                                <a class="template-item-thumb template-thumbnail-blank" href="index.php?option=com_templates&task=style.edit&id=<?php echo $template->id ?>">
                                    <span class="blank-message"><?php echo JText::_('JSN_POWERADMIN_BLANK_THUMBNAIL') ?></span>
                                    <span><?php echo $template->title;?></span>
                                </a>
                            <?php endif ?>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

            <div class="template-list" id="admin" style="display:none">
                <div id="jsn-page-container" class="jsn-template-select-panel">
                    <?php foreach ($this->adminTemplates as $template): ?>
                        <?php $class = ($template->home == 1) ? 'template-item default' : 'template-item' ?>
                        <div class="<?php echo $class;?>" id="jTemplate-<?php echo $template->id;?>" clientId="<?php echo $template->client_id?>" sid="<?php echo $template->id;?>" tid="<?php echo $template->tid;?>">
                            <?php if (file_exists(JPATH_ROOT.'/'.'administrator/templates/'.$template->template.'/template_thumbnail.png')): ?>
                                <a class="template-item-thumb" href="javascript:;" >
                                    <span class="thumbnail">
                                        <img src="<?php echo JURI::root().'administrator/templates/'.$template->template.'/template_thumbnail.png'; ?>" alt="<?php echo $template->title;?>" align="center"/>
                                    </span>
                                    <span><?php echo $template->title;?></span>
                                </a>
                            <?php else: ?>
                                <a class="template-item-thumb template-thumbnail-blank" href="index.php?option=com_templates&task=style.edit&id=<?php echo $template->id ?>">
                                    <span class="blank-message"><?php echo JText::_('JSN_POWERADMIN_BLANK_THUMBNAIL') ?></span>
                                    <span><?php echo $template->title;?></span>
                                </a>
                            <?php endif ?>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
		</div>
	</div>
	<h3><a href="#"><?php echo JText::_('JSN_POWERADMIN_GET_MORE_TEMPLATES') ?></a></h3>
	<div id="get-more-templates">
		<div id="slideshow">
			<iframe src="http://www.joomlashine.com/free-joomla-templates-promo.html" scrolling="no" frameborder="0" width="640" height="510"></iframe>
		</div>
	</div>
</div>