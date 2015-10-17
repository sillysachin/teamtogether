<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  TPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get all fieldset in XML
$fieldSets = $adminFormXml->fields->fieldset;

// Set appropriate wrapper class
$wrapperClass = 'jsn-joomla' . JSNTplHelper::getJoomlaVersion(1, false);

// Get template details
$template = JSNTplTemplateRecognization::detect($this->data->template);

// Prepare template name for link generation
$templateName = 'jsn-' . strtolower($template->name);

// Generate template introduction link
$templateLink = "http://www.joomlashine.com/joomla-templates/{$templateName}.html";

// Process template edition
$edition = $this->templateEdition->getEdition();

if ($edition == 'FREE')
{
	$editionClass = 'jsn-free-edition';
}
else
{
	$editionClass = 'jsn-pro-edition';
}

// Get next template edition
$nextEdition = str_replace('PRO ', '', $this->templateEdition->getNextEdition());

// Get installed template version
$version = JSNTplHelper::getTemplateVersion($this->data->template);

// Get list of JoomlaShine template for cross promotion
$promotion = $nextEdition == 'STANDARD' ? true : false;

if ($promotion)
{
	$config = JFactory::getConfig();
	$cache  = $config->get('tmp_path') . '/jsn_templates.json';

	if ( ! @is_file($cache) || ! @is_readable($cache) || time() - filemtime($cache) > 86400)
	{
		try
		{
			$jsn_templates = JSNTplHttpRequest::get(
				'http://demo.joomlashine.com/products-intro/assets/json/showlist-templates-' . ($nextEdition == 'STANDARD' ? 'free' : 'pro') . '.json',
				$cache
			);

			$jsn_templates = $jsn_templates['body'];
		}
		catch (Exception $e)
		{
			// Disable cross promotion
			$promotion = false;
		}
	}
	else
	{
		$jsn_templates = JFile::read($cache);
	}

	if ($jsn_templates = json_decode($jsn_templates))
	{
		$jsn_templates = $jsn_templates->showlist->images->image;
	}
	else
	{
		$promotion = false;
	}
}
?>
<div class="jsn-master"><div id="jsn-template-config" class="jsn-bootstrap <?php echo $wrapperClass ?> <?php echo $editionClass ?>">
	<form action="" method="POST" name="adminForm" id="style-form">
		<input type="hidden" name="task" />
		<input type="hidden" name="customized" value="<?php echo @count($this->data->params) ? 'yes' : 'no'; ?>" />
		<?php echo JHtml::_('form.token'); ?>

		<div id="jsn-template-toolbar">
			<label for="jform_title pull-left"><?php echo JText::_('JSN_TPLFW_FIELD_TITLE_LABEL') ?></label>
			<?php echo $this->templateForm->getInput('title') ?>

			<label for="jform_template pull-left"><?php echo JText::_('COM_TEMPLATES_FIELD_TEMPLATE_LABEL') ?></label>
			<?php echo $this->templateForm->getInput('template') ?>

			<label for="jform_home pull-left"><?php echo JText::_('COM_TEMPLATES_FIELD_HOME_LABEL') ?></label>
			<?php echo $this->templateForm->getInput('home') ?>

			<?php echo $this->templateForm->getInput('client_id') ?>
			<div class="clearfix"></div>
		</div>

		<div id="jsn-template-config-tabs" class="form-horizontal">
			<ul id="jsn-main-nav">
				<li>
					<a href="#getting-started">
						<i class="icon-home icon-black"></i>
						<?php echo JText::_('JSN_TPLFW_GETTING_STARTED') ?>
					</a>
				</li>
				<?php foreach ($fieldSets as $fieldSet): ?>
					<?php $class = isset($fieldSet['pro']) && $fieldSet['pro'] == 'true' ? 'jsn-pro-tab' : '' ?>
					<li class="<?php echo $class ?>">
						<a href="#<?php echo $fieldSet['name'] ?>">
							<?php if (isset($fieldSet['icon'])): ?>
								<i class="<?php echo $fieldSet['icon'] ?>"></i>
							<?php endif ?>

							<?php echo JText::_($fieldSet['label']) ?>
							<?php if (isset($fieldSet['pro']) && $fieldSet['pro'] == 'true'): ?>
								<span class="label label-important label-pro">PRO</span>
							<?php endif ?>
						</a>
					</li>
				<?php endforeach ?>
				<li><a href="#menu-assignment"><i class="icon-checkbox"></i> <?php echo JText::_('JSN_TPLFW_MENU_ASSIGNMENT') ?></a></li>
			</ul>

			<div id="jsn-template-maintenance" class="btn-group pull-right">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
					<?php echo JText::_('JSN_TPLFW_MAINTENANCE'); ?>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					<li><a id="jsn-template-maintenance-backup-params" href="<?php echo JRoute::_('index.php?widget=maintenance&action=backup&template=' . $this->data->template) . '&styleId=' . $this->data->id; ?>"><?php echo JText::_('JSN_TPLFW_MAINTENANCE_BACKUP'); ?></a></li>
					<li><a id="jsn-template-maintenance-restore-params" href="<?php echo JRoute::_('index.php?widget=maintenance&action=restore&template=' . $this->data->template) . '&styleId=' . $this->data->id; ?>"><?php echo JText::_('JSN_TPLFW_MAINTENANCE_RESTORE'); ?></a></li>
				</ul>
			</div>

			<div class="row-fluid" id="getting-started">
				<?php include JSN_PATH_TPLFRAMEWORK_LIBRARIES . '/template/tmpl/default_home.php' ?>
			</div>

			<?php foreach ($fieldSets as $xmlFieldSet): ?>
				<div id="<?php echo $xmlFieldSet['name'] ?>">
					<?php if (isset($xmlFieldSet['pro']) && $xmlFieldSet['pro'] == 'true' && $this->templateEdition->getEdition() == 'FREE'): ?>
					<div class="jsn-section-pro alert alert-block">
						<p class="pull-left"><?php echo JText::_('JSN_TPLFW_FEATURES_AVAILABLE_IN_PRO') ?></p>
						<a href="javascript:void(0)" class="jsn-upgrade-link btn pull-right"><?php echo JText::_('JSN_TPLFW_UPGRADE_NOW') ?></a>
						<div class="clearfix"></div>
					</div>
					<?php endif ?>

					<?php if (isset($xmlFieldSet['twoColumns']) && $xmlFieldSet['twoColumns'] == 'true'): ?>
						<?php include JSN_PATH_TPLFRAMEWORK_LIBRARIES . '/template/tmpl/default_layout.php' ?>
					<?php else: ?>
						<?php foreach ($xmlFieldSet->children() as $field): ?>
							<?php $nodeName = strtolower($field->getName()) ?>

							<?php if ($nodeName == 'field'): ?>
								<?php $input = $this->adminForm->getField($field['name'], 'jsn') ?>
								<?php if (trim($field['label']) != '') : ?>
								<div class="control-group">
									<div class="control-label">
										<label for="<?php echo $input->id ?>" rel="tipsy" title="<?php echo JText::_($field['label'] . '_DESC') ?>">
											<?php echo JText::_($field['label']) ?>
										</label>
									</div>
									<div class="controls">
										<?php echo str_replace('%TEMPLATE%', $this->data->template, $input->input) ?>
									</div>
								</div>
								<?php else : ?>
								<div>
									<?php echo str_replace('%TEMPLATE%', $this->data->template, $input->input) ?>
								</div>
								<?php endif; ?>
							<?php elseif ($nodeName == 'fieldset'): ?>
								<fieldset class="<?php echo $field['name'] ?>">
									<legend><?php echo JText::_($field['label']) ?></legend>
									<?php foreach ($field->children() as $innerField): ?>
										<?php $input = $this->adminForm->getField($innerField['name'], 'jsn') ?>
										<?php if (trim($innerField['label']) != '') : ?>
										<div class="control-group">
											<div class="control-label">
												<label for="<?php echo $input->id ?>" rel="tipsy" title="<?php echo JText::_($innerField['label'] . '_DESC') ?>">
													<?php echo JText::_($innerField['label']) ?>
												</label>
											</div>
											<div class="controls">
												<?php echo str_replace('%TEMPLATE%', $this->data->template, $input->input) ?>
											</div>
										</div>
										<?php else : ?>
										<div>
											<?php echo str_replace('%TEMPLATE%', $this->data->template, $input->input) ?>
										</div>
										<?php endif; ?>
										<?php endforeach ?>
								</fieldset>
							<?php endif ?>
						<?php endforeach ?>
					<?php endif ?>
				</div>
			<?php endforeach ?>

			<div id="menu-assignment">
				<?php include JSN_PATH_TPLFRAMEWORK_LIBRARIES . '/template/tmpl/default_assignment.php' ?>
			</div>
		</div>
	</form>

	<?php if ($promotion) : ?>
	<div id="see-other-products">
		<h2 class="jsn-section-header">
			<?php echo JText::_('JSN_TPLFW_SEE_OTHER_PRODUCTS') ?>
			<ul class="jsn-list-horizontal pull-right">
				<li>
					<a
						class="jsn-icon24 jsn-icon-social jsn-icon-facebook"
						href="http://www.facebook.com/joomlashine"
						title="<?php echo JText::_('JSN_TPLFW_CONNECT_WITH_US_ON_FACEBOOK') ?>"
						target="_blank">
					</a>
				</li>
				<li>
					<a
						class="jsn-icon24 jsn-icon-social jsn-icon-twitter"
						href="http://www.twitter.com/joomlashine"
						title="<?php echo JText::_('JSN_TPLFW_FOLLOW_US_ON_TWITTER') ?>"
						target="_blank">
					</a>
				</li>
				<li>
					<a
						class="jsn-icon24 jsn-icon-social jsn-icon-youtube"
						href="http://www.youtube.com/joomlashine"
						title="<?php echo JText::_('JSN_TPLFW_WATCH_US_ON_YOUTUBE') ?>"
						target="_blank">
					</a>
				</li>
			</ul>
		</h2>
		<div class="clearbreak"></div>
		<div class="bxslider">
			<?php foreach ($jsn_templates as $item) :
				$download = 'javascript:void(0)';
				$title    = 'Joomla Templates by JoomlaShine.com';

				if (preg_match('#/jsn-([a-z0-9]+)\.#i', $item->image, $m))
				{
					$download = 'http://www.joomlashine.com/joomla-templates/jsn-' . $m[1] . '-download.html';
					$title    = 'JSN ' . ucfirst($m[1]);
				} ?>
			<a <?php echo 'href="http://demo.joomlashine.com/products-intro/' . $item->image . '" title="' . $title . '" data-demo="' . $item->link . '" data-download="' . $download . '" rel="cross-promo"'; ?>>
				<img src="http://demo.joomlashine.com/products-intro/<?php echo $item->thumbnail; ?>" />
			</a>
			<?php endforeach; ?>
		</div>
		<input type="hidden" name="visited" value="" />
		<script type="text/javascript">
			(function($) {
				$(document).ready(function() {
					var promotion = $('.bxslider'); slicesPerView = parseInt(promotion.innerWidth() / 250);

					promotion.bxSlider({
						hideControlOnEnd: true,
						adaptiveHeight: true,
						pager: false,
						slideWidth: 250,
						slideMargin: 10,
						minSlides: slicesPerView,
						maxSlides: slicesPerView,
					});

					$('.bxslider > a').colorbox({
						rel: 'cross-promo',
						onComplete: function(event) {
							// Set title
							$('#colorbox #cboxCurrent').html($(event.el).attr('title'));

							// Set buttons
							$('#colorbox #cboxTitle').html(
								'<div class="jsn-master"><div class="jsn-bootstrap">'
								+
								'<a href="' + $(event.el).attr('data-demo') + '" class="btn btn-primary" target="_blank"><?php echo JText::_('JSN_TPLFW_DEMO'); ?></a>'
								+
								'<a href="' + $(event.el).attr('data-download') + '" class="btn btn-success" target="_blank"><?php echo JText::_('JSN_TPLFW_DOWNLOAD'); ?></a>'
								+
								'</div></div>'
							);
						},
					});
				});
			})(jQuery);
		</script>
	</div>
	<?php endif; ?>

	<div class="modal hide" id="jsn_pro_edition_only_modal">
		<div class="modal-body">
			<p><?php echo JText::_('JSN_TPLFW_PRO_EDITION_ONLY'); ?></p>
		</div>
		<div class="modal-footer">
			<a class="btn btn-primary jsn-upgrade-link" href="javascript:void(0)" onclick="jQuery(this).parent().parent().addClass('hide');"><?php echo JText::_('JSN_TPLFW_UPGRADE_NOW'); ?></a>
			<a class="btn" href="javascript:void(0)" onclick="jQuery(this).parent().parent().addClass('hide');"><?php echo JText::_('JSN_TPLFW_CLOSE'); ?></a>
		</div>
	</div>

	<div class="jsn-form-validation-failed jsn-box-shadow-medium alert alert-error hide">
		<span></span>
		<a href="javascript:void(0);" title="<?php echo JText::_('JSN_TPLFW_CLOSE'); ?>" class="close" onclick="jQuery(this).parent().addClass('hide');">×</a>
	</div>
</div></div>

<div class="jsn-master">
	<div class="jsn-page-footer jsn-bootstrap" id="jsn-footer">
		<div class="pull-left">
			<ul class="jsn-footer-menu">
				<li class="first">
					<a target="_blank" href="http://www.joomlashine.com/joomla-templates/<?php echo $templateName ?>-docs.zip"><?php echo JText::_('JSN_TPLFW_DOCUMENTATION'); ?></a>
				</li>
				<li>
					<a target="_blank" href="http://www.joomlashine.com/contact-us/get-support.html"><?php echo JText::_('JSN_TPLFW_SUPPORT'); ?></a>
				</li>
				<li class="jsn-iconbar">
					<strong>Keep in touch:</strong>
					<a href="http://www.facebook.com/joomlashine" target="_blank" title="Find us on Facebook"><i class="jsn-icon16 jsn-icon-social jsn-icon-facebook"></i></a><a href="http://www.twitter.com/joomlashine" target="_blank" title="Follow us on Twitter"><i "="" class="jsn-icon16 jsn-icon-social jsn-icon-twitter"></i></a><a href="http://www.youtube.com/joomlashine" target="_blank" title="Watch us on YouTube"><i "="" class="jsn-icon16 jsn-icon-social jsn-icon-youtube"></i></a>
				</li>
			</ul>

			<ul class="jsn-footer-menu">
				<li class="first">
					<a target="_blank" href="<?php echo $templateLink ?>"><?php echo JText::_($template->name) ?> <?php echo $edition ?> v<?php echo $version ?></a> by <a target="_blank" href="http://www.joomlashine.com">JoomlaShine.com</a>
					<?php if ($nextEdition) : ?>
					&nbsp;<a class="label label-important jsn-upgrade-link" href="javascript:void()"><strong class="jsn-text-attention"><?php echo JText::_($nextEdition == 'STANDARD' ? 'JSN_TPLFW_UPGRADE_TO_PRO' : 'JSN_TPLFW_UPGRADE_TO_PRO_UNLIMITED'); ?></strong></a>
					<?php endif; ?>
				</li>
				<li class="jsn-outdated-version" id="jsn-global-check-version-result" style="display:none">
					<span class="jsn-global-outdated-version"><?php echo JText::_('JSN_TPLFW_UPDATE_AVAILABLE'); ?></span>
					&nbsp;<a class="label label-important jsn-update-link" data-target="template" href="javascript:void(0)"><?php echo JText::_('JSN_TPLFW_UPDATE_NOW'); ?></a>
				</li>
			</ul>
		</div>

		<div class="pull-right">
			<ul class="jsn-footer-menu">
				<li class="jsn-iconbar first">
					<a href="http://www.joomlashine.com/joomla-extensions/jsn-poweradmin.html" target="_blank" title="JSN PowerAdmin - Manage Joomla websites with ease and joy">
						<i class="jsn-icon32 jsn-icon-products jsn-icon-poweradmin"></i>
					</a>
					<a href="http://www.joomlashine.com/joomla-extensions/jsn-imageshow.html" target="_blank" title="JSN ImageShow - One Joomla gallery extension for all image presentation needs">
						<i class="jsn-icon32 jsn-icon-products jsn-icon-imageshow"></i>
					</a>
					<a href="http://www.joomlashine.com/joomla-extensions/jsn-uniform.html" target="_blank" title="JSN UniForm - The most easy, yet sophisticated Joomla form builder extension">
						<i class="jsn-icon32 jsn-icon-products jsn-icon-uniform"></i>
					</a>
					<a href="http://www.joomlashine.com/joomla-extensions/jsn-mobilize.html" target="_blank" title="JSN Mobilize - Painless mobile site creator">
						<i class="jsn-icon32 jsn-icon-products jsn-icon-mobilize"></i>
					</a>
				</li>
			</ul>
		</div>

		<div class="clearbreak"></div>
	</div>
</div>

<!-- Hidden form for saving/restoring template parameters -->
<form id="jsn-template-maintenance-restore-params-form" method="post" enctype="multipart/form-data" target="jsn-silent-save" class="hide">
	<input type="file" name="backup-upload" />
</form>
<iframe id="jsn-silent-save" name="jsn-silent-save" class="hide" src="about:blank"></iframe>

<script type="text/javascript">
	(function($) {
		// Setup tabs
		$('#jsn-template-config-tabs').tabs();

		// Setup form validation
		new $.JSNFormValidation({
			lang: {
				JSN_TPLFW_INVALID_VALUE_TYPE: '<?php echo JText::_('JSN_TPLFW_INVALID_VALUE_TYPE'); ?>',
				JSN_TPLFW_ERROR_FORM_VALIDATION_FAILED: '<?php echo JText::_('JSN_TPLFW_ERROR_FORM_VALIDATION_FAILED'); ?>',
				JSN_TPLFW_SYSTEM_CUSTOM_ASSETS_INVALID: '<?php echo JText::_('JSN_TPLFW_SYSTEM_CUSTOM_ASSETS_INVALID'); ?>'
			}
		});
	})(jQuery);
</script>
