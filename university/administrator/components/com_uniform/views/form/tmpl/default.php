<?php

/**
 * @version     $Id: default.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Form
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
$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "free";
?>

<div class="jsn-page-settings jsn-bootstrap">
<form name="adminForm" method="post" id="adminForm" class="hide">
<?php echo $this->_form->getInput('form_id') ?>
<div class="jsn-tabs">
<ul>
	<li class="active">
		<a href="#detail"><i class="icon-home"></i> <?php echo JText::_('JSN_UNIFORM_GLOBAL_GENERAL'); ?></a>
	</li>
	<li><a href="#form-design"><i class="icon-list-alt"></i> <?php echo JText::_('JSN_UNIFORM_FORM_DESIGN'); ?></a>
	</li>
	<li><a href="#form-action"><i class="icon-magic"></i> <?php echo JText::_('JSN_UNIFORM_FORM_ACTION'); ?></a>
	</li>
</ul>
<div class="tab-pane active" id="detail">
	<div class="row-fluid form-horizontal">
		<div class="span6">
			<fieldset>
				<legend><?php echo JText::_('JSN_UNIFORM_DETAILS'); ?></legend>
				<div class="control-group">
					<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_SET_THE_FORM_TITLE'); ?>"><?php echo JText::_('JSN_UNIFORM_FORM_TITLE'); ?></label>

					<div class="controls">
						<?php echo $this->_form->getInput('form_title') ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_SET_THE_FORM_DES'); ?>"><?php echo JText::_('JSN_UNIFORM_FORM_DESC'); ?></label>

					<div class="controls">
						<?php echo $this->_form->getInput('form_description') ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_SELECT_THE_FORM_STATUS_TO_INDICATE'); ?>"><?php echo JText::_('JSTATUS'); ?></label>

					<div class="controls">
						<?php echo $this->_form->getInput('form_state') ?>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="span6">
			<fieldset>
				<legend><?php echo JText::_('JSN_UNIFORM_OPTIONS'); ?></legend>
				<div class="control-group">
					<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_STATE_EDIT_SUBMISSION_DES'); ?>"><?php echo JText::_('JSN_UNIFORM_STATE_EDIT_SUBMISSION'); ?></label>

					<div class="controls">
						<?php echo $this->_form->getInput('form_edit_submission') ?>
					</div>
				</div>
				<div id="jsn-select-user-group" class="control-group hide">
					<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_SELECT_GROUP_EDIT_SUBMISSION_DES'); ?>"><?php echo JText::_('JSN_UNIFORM_SELECT_GROUP_EDIT_SUBMISSION'); ?></label>

					<div class="controls">
						<?php echo $this->_form->getInput('form_access') ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_SELECT_IF_YOU_WANT_TO_SHOW_RECAPTCHA'); ?>"><?php echo JText::_('JSN_UNIFORM_FORM_ENABLE_CAPTCHA'); ?></label>

					<div class="controls">
						<?php echo $this->_form->getInput('form_captcha') ?>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</div>
<div id="style_inline">
	<style class="formstyle">
		<?php
		echo JSNUniformHelper::generateStylePages($this->formStyle, '.jsn-master #form-design-content .jsn-element-container .jsn-element', '.jsn-master #form-design-content .jsn-element-container .jsn-element.ui-state-edit', '.jsn-master #form-design-content .jsn-element-container .jsn-element .control-label', '', '', ".jsn-master #form-design-content .jsn-element-container .jsn-element .controls input,.jsn-master #form-design-content .jsn-element-container .jsn-element .controls select,.jsn-master #form-design-content .jsn-element-container .jsn-element .controls textarea");
		?>
	</style>
	<style class="formstylecustom">
		<?php
		echo !empty($this->formStyle->custom_css)?$this->formStyle->custom_css:"";
		?>
	</style>
</div>
<div id="form-design">
<div class="jsn-form-bar">
<div class="control-group ">
	<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_SELECT_TO_SHOW_FORM_FIELD_IN_SINGLE_PAGE'); ?>"><?php echo JText::_('JSN_UNIFORM_TYPE'); ?></label>

	<div class="controls">
		<?php echo $this->_form->getInput('form_type') ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label jsn-captcha inline" original-title="<?php echo JText::_('JSN_UNIFORM_SELECT_TO_SHOW_FORM_FIELD_TITLE_AND_ELEMENT'); ?>"><?php echo JText::_('JSN_UNIFORM_FORM_LAYOUT'); ?></label>

	<div class="controls">
		<select id="jform_form_style" name="form_style[layout]" class="jsn-input-fluid">
			<option <?php echo !empty($this->formStyle->layout) && $this->formStyle->layout == "Vertical" ? "selected" : "";?> value="Vertical">
				Vertical
			</option>
			<option <?php echo !empty($this->formStyle->layout) && $this->formStyle->layout == "form-horizontal" ? "selected" : "";?> value="form-horizontal">
				Horizontal
			</option>
		</select>
	</div>
</div>
<div class="pull-right">
<button id="select_form_style" class="btn" onclick="return false;">
	<i class="icon-pencil"></i><?php echo JText::_('JSN_UNIFORM_FORM_STYLE'); ?></button>
<div id="container-select-style" class="jsn-bootstrap">
<div class="popover bottom">
<div class="arrow"></div>
<h3 class="popover-title"><?php echo JText::_('JSN_UNIFORM_FORM_STYLE'); ?></h3>

<div class="popover-content">
<div class="jsn-form-bar">
	<div class="jsn-padding-medium jsn-rounded-medium jsn-box-shadow-small jsn-bgpattern pattern-sidebar">
		<div class="control-group">
			<label class="control-label label-color-scheme" original-title="<?php echo JText::_('JSN_UNIFORM_COLOR_SCHEME'); ?>"><?php echo JText::_('JSN_UNIFORM_COLOR_SCHEME'); ?></label>

			<div class="controls">
				<div id="theme_select">
					<div id="form-select">
						<?php
						$optionTheme = "";
						?>
						<select id="jform_form_theme" data-default="<?php echo !empty($this->formStyle->theme)?$this->formStyle->theme:"";?>" style="width: 200px" name="form_style[theme]">
							<?php
							$themes = !empty($this->formStyle->themes) ? $this->formStyle->themes : array('light', 'dark');
							if (!empty($themes))
							{
								foreach ($themes as $theme)
								{
									$dataValue = "";
									if (!empty($this->formStyle->themes_style))
									{
										$themeStyle = $this->formStyle->themes_style;

										$dataValue = !empty($themeStyle->$theme) ? $themeStyle->$theme : "";
									}
									$checked = !empty($this->formStyle->theme) && $this->formStyle->theme == "jsn-style-" . $theme ? "selected" : "";
									echo "<option {$checked} value='jsn-style-{$theme}'>{$theme}</option>";
									$optionTheme .= "<input type='hidden' class='jsn-style-{$theme}' value='{$dataValue}' name='form_style[themes_style][{$theme}]'/><input type='hidden' value='{$theme}' name='form_style[themes][]'/>";
								}
							}
							?>
						</select>
					</div>
					<div id="add-theme-select" class="hide">
						<div class="control-group">
							<input type="text" id="input_new_theme" class="input-medium" name="new_theme">

							<div class="control-group">
								<button title="Save" id="btn_add_theme" onclick="return false;" class="btn btn-icon">
									<i class="icon-ok"></i></button>
								<button title="Cancel" id="btn_cancel_theme" onclick="return false;" class="btn btn-icon">
									<i class="icon-remove"></i></button>
							</div>
						</div>
					</div>
					<div id="option_themes" class="hide">
						<?php echo $optionTheme;?>
					</div>
					<div id="theme_action" class="pull-right">
						<button class="btn btn-icon" id="theme_action_refresh" onclick="return false;">
							<i class="icon-refresh"></i></button>
						<button class="btn btn-icon" id="theme_action_delete" onclick="return false;">
							<i class="icon-trash"></i></button>
						<button class="btn btn-icon btn-success pull-right" id="theme_action_add" onclick="return false;">
							<i class="icon-plus"></i></button>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
<div id="style_accordion_content" class="jsn-tabs form-horizontal">
<ul>
	<li class="active"><a href="#formStyleContainer"><?php echo JText::_('JSN_UNIFORM_CONTAINER'); ?></a></li>
	<li><a href="#formStyleTitle"><?php echo JText::_('JSN_UNIFORM_TITLE'); ?></a></li>
	<li><a href="#formStyleField"><?php echo JText::_('JSN_UNIFORM_FIELD'); ?></a></li>
	<li><a href="#formStyleMessageError"><?php echo JText::_('JSN_UNIFORM_MESSAGE_ERRORS'); ?></a></li>
	<li><a href="#formStyleButtons"><?php echo JText::_('JSN_UNIFORM_BUTTONS'); ?></a></li>
	<li><a href="#formCustomCss"><?php echo JText::_('JSN_UNIFORM_CUSTOM_CSS'); ?></a></li>
</ul>
<div id="formStyleContainer">
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_BACKGROUND_COLOR'); ?></label>

		<div class="controls">
			<input type="text" data-value="background-color" data-type="jsn-element" value="<?php echo $this->formStyle->background_color;?>" class="jsn-input-fluid" name="form_style[background_color]" id="style_background_color" />

			<div class="jsn-select-color">
				<div></div>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_BACKGROUND_ACTIVE_COLOR'); ?></label>

		<div class="controls">
			<input type="text" data-value="background-color" data-type="ui-state-edit" value="<?php echo $this->formStyle->background_active_color;?>" class="jsn-input-fluid" name="form_style[background_active_color]" id="style_background_active_color" />

			<div class="jsn-select-color">
				<div></div>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_BORDER_THICKNESS'); ?></label>

		<div class="controls">
			<div class="input-append">
				<input type="number" data-value="border" data-type="jsn-element" value="<?php echo !empty($this->formStyle->border_thickness) ? $this->formStyle->border_thickness : 0;?>" class="jsn-input-number input-mini" name="form_style[border_thickness]" id="style_border_thickness" /><span class="add-on">px</span>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_BORDER_COLOR'); ?></label>

		<div class="controls">
			<input type="text" data-value="border-color" data-type="jsn-element" value="<?php echo $this->formStyle->border_color;?>" class="jsn-input-fluid" name="form_style[border_color]" id="style_border_color" />

			<div class="jsn-select-color">
				<div></div>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_BORDER_ACTIVE_COLOR'); ?></label>

		<div class="controls">
			<input type="text" data-value="border-color" data-type="ui-state-edit" value="<?php echo $this->formStyle->border_active_color;?>" class="jsn-input-fluid" name="form_style[border_active_color]" id="style_border_active_color" />

			<div class="jsn-select-color">
				<div></div>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_ROUNDED_CORNER_RADIUS'); ?></label>

		<div class="controls">
			<div class="input-append">
				<input type="number" data-value="border-radius,-moz-border-radius,-webkit-border-radius" data-type="jsn-element" value="<?php echo !empty($this->formStyle->rounded_corner_radius) ? $this->formStyle->rounded_corner_radius : 0;?>" class="input-mini jsn-input-number" name="form_style[rounded_corner_radius]" id="style_rounded_corner_radius" /><span class="add-on">px</span>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_PADDING_SPACE'); ?></label>

		<div class="controls">
			<div class="input-append">
				<input type="number" data-value="padding" data-type="jsn-element" value="<?php echo !empty($this->formStyle->padding_space) ? $this->formStyle->padding_space : 0;?>" class="input-mini jsn-input-number" name="form_style[padding_space]" id="style_padding_space" /><span class="add-on">px</span>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_MARGIN_SPACE'); ?></label>

		<div class="controls">
			<div class="input-append">
				<input type="number" data-value="margin" data-type="jsn-element" value="<?php echo !empty($this->formStyle->margin_space) ? $this->formStyle->margin_space : 0;?>" class="input-mini jsn-input-number" name="form_style[margin_space]" id="style_margin_space" /><span class="add-on">px</span>
			</div>
		</div>
	</div>
</div>
<div id="formStyleTitle">
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_TEXT_COLOR'); ?></label>

		<div class="controls">
			<input type="text" data-value="color" data-type="control-label" value="<?php echo $this->formStyle->text_color;?>" class="jsn-input-fluid" name="form_style[text_color]" id="style_text_color" />

			<div class="jsn-select-color">
				<div></div>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_FONT_TYPE'); ?></label>

		<div class="controls">
			<select data-value="font-family" data-type="control-label" name="form_style[font_type]" id="style_font_type">
				<?php
				foreach ($this->_listFontType as $fontType)
				{
					$selected = "";
					if ($fontType == $this->formStyle->font_type)
					{
						$selected = "selected";
					}
					echo "<option {$selected} value='{$fontType}'>{$fontType}</option>";
				}
				?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_FONT_SIZE'); ?></label>

		<div class="controls">
			<div class="input-append">
				<input type="number" data-value="font-size" data-type="control-label" default-value="14px" value="<?php echo !empty($this->formStyle->font_size) ? $this->formStyle->font_size : 0;?>" class="input-mini jsn-input-number" name="form_style[font_size]" id="style_font_size" /><span class="add-on">px</span>
			</div>
		</div>
	</div>
</div>
<div id="formStyleField">
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_BACKGROUND_COLOR'); ?></label>

		<div class="controls">
			<input type="text" data-value="background-color" data-type="field" value="<?php echo $this->formStyle->field_background_color;?>" class="jsn-input-fluid" name="form_style[field_background_color]" id="style_field_background_color" />

			<div class="jsn-select-color">
				<div></div>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_BORDER_COLOR'); ?></label>

		<div class="controls">
			<input type="text" data-value="border-color" data-type="field" value="<?php echo $this->formStyle->field_border_color;?>" class="jsn-input-fluid" name="form_style[field_border_color]" id="style_field_border_color" />

			<div class="jsn-select-color">
				<div></div>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_SHADOW_COLOR'); ?></label>

		<div class="controls">
			<input type="text" data-value="box-shadow" data-type="field" value="<?php echo $this->formStyle->field_shadow_color;?>" class="jsn-input-fluid" name="form_style[field_shadow_color]" id="style_field_shadow_color" />

			<div class="jsn-select-color">
				<div></div>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_TEXT_COLOR'); ?></label>

		<div class="controls">
			<input type="text" data-value="color" data-type="field" value="<?php echo $this->formStyle->field_text_color;?>" class="jsn-input-fluid" name="form_style[field_text_color]" id="style_field_text_color" />

			<div class="jsn-select-color">
				<div></div>
			</div>
		</div>
	</div>

</div>
<div id="formStyleMessageError">
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_BACKGROUND_COLOR'); ?></label>

		<div class="controls">
			<input type="text" value="<?php echo $this->formStyle->message_error_background_color;?>" class="jsn-input-fluid" name="form_style[message_error_background_color]" id="style_message_error_background_color" />

			<div class="jsn-select-color">
				<div></div>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_TEXT_COLOR'); ?></label>

		<div class="controls">
			<input type="text" value="<?php echo $this->formStyle->message_error_text_color;?>" class="jsn-input-fluid" name="form_style[message_error_text_color]" id="style_message_error_text_color" />

			<div class="jsn-select-color">
				<div></div>
			</div>
		</div>
	</div>
</div>
<div id="formStyleButtons">
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('JSN_UNIFORM_BUTTON_POSITION'); ?></label>

		<div class="controls">
			<select class="input-large" name="form_style[button_position]" id="button_position">
				<?php
				$buttonPosition = !empty($this->formStyle->button_position) ? $this->formStyle->button_position : "btn-toolbar";
				echo JSNUniformHelper::renderOptionsButtonPosition($buttonPosition);
				?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo $this->_item->form_btn_submit_text ? JText::_($this->_item->form_btn_submit_text) : JText::_('SUBMIT'); ?></label>

		<div class="controls">
			<select class="input-large jsn-select2" name="form_style[button_submit_color]" id="button_submit_color">
				<?php
				$buttonSubmitColor = !empty($this->formStyle->button_submit_color) ? $this->formStyle->button_submit_color : "btn btn-primary";
				echo JSNUniformHelper::renderOptionsButtonStyle($buttonSubmitColor);
				?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo $this->_item->form_btn_reset_text ? JText::_($this->_item->form_btn_reset_text) : JText::_('RESET'); ?></label>

		<div class="controls">
			<select class="input-large jsn-select2" name="form_style[button_reset_color]" id="button_reset_color">
				<?php
				$buttonResetColor = !empty($this->formStyle->button_reset_color) ? $this->formStyle->button_reset_color : "btn";
				echo JSNUniformHelper::renderOptionsButtonStyle($buttonResetColor);
				?>
			</select>
		</div>
	</div>
	<?php
	if (strtolower($edition) != "free")
	{
		?>
		<div class="control-group">
			<label class="control-label"><?php echo $this->_item->form_btn_prev_text ? JText::_($this->_item->form_btn_prev_text) : JText::_('PREV'); ?></label>

			<div class="controls">
				<select class="input-large jsn-select2" name="form_style[button_prev_color]" id="button_prev_color">
					<?php
					$buttonPrevColor = !empty($this->formStyle->button_prev_color) ? $this->formStyle->button_prev_color : "btn";
					echo JSNUniformHelper::renderOptionsButtonStyle($buttonPrevColor);
					?>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php echo $this->_item->form_btn_next_text ? JText::_($this->_item->form_btn_next_text) : JText::_('NEXT'); ?></label>

			<div class="controls">

				<select class="input-large jsn-select2" name="form_style[button_next_color]" id="button_next_color">
					<?php
					$buttonNextColor = !empty($this->formStyle->button_next_color) ? $this->formStyle->button_next_color : "btn btn-primary";
					echo JSNUniformHelper::renderOptionsButtonStyle($buttonNextColor);
					?>
				</select>
			</div>
		</div>
		<?php }?>
</div>
<div id="formCustomCss">
	<textarea id="style_custom_css" name="form_style[custom_css]"><?php echo $this->formStyle->custom_css;?></textarea>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<hr />
<div class="jsn-page">
	<div class="jsn-pane jsn-bgpattern pattern-sidebar">
		<?php echo $this->_listPage; ?>
		<div id="form-design-content" class="jsn-section-content <?php echo !empty($this->_item->form_theme) ? $this->_item->form_theme : "jsn-style-light"; ?>">
			<div id="form-container" class="jsn-layout">
				<div id="page-loading" class="jsn-bgloading">
					<i class="jsn-icon32 jsn-icon-loading"></i></div>
				<a class="jsn-add-more" id="jsn-add-container" href="javascript:void(0);"><i class="icon-plus"></i><?php echo JText::_("JSN_UNIFORM_ADD_CONTAINER");?>
				</a>

				<div class="ui-sortable jsn-sortable-disable">
					<div class="form-actions ui-state-default jsn-iconbar-trigger">
						<div class="<?php echo $buttonPosition;?>">
							<?php
							$formSettings = !empty($this->_item->form_settings) ? json_decode($this->_item->form_settings) : "";
							$stateBtnReset = "hide";
							if (!empty($formSettings->form_state_btn_reset_text) && $formSettings->form_state_btn_reset_text == "Yes")
							{
								$stateBtnReset = "";
							}
							if (strtolower($edition) == "free")
							{
								?>
								<button class="<?php echo $buttonResetColor." ".$stateBtnReset;?> jsn-form-reset" onclick="return false;"><?php echo $formSettings->form_btn_reset_text ? JText::_($formSettings->form_btn_reset_text) : JText::_('RESET'); ?></button>
								<button class="<?php echo $buttonSubmitColor;?> jsn-form-submit" onclick="return false;"><?php echo $formSettings->form_btn_submit_text ? JText::_($formSettings->form_btn_submit_text) : JText::_('SUBMIT'); ?></button>
								<?php
							}
							else
							{
								?>
								<button onclick="return false;" class="<?php echo $buttonPrevColor;?> jsn-form-prev hide"><?php echo $formSettings->form_btn_prev_text ? JText::_($formSettings->form_btn_prev_text) : JText::_('PREV'); ?></button>
								<button onclick="return false;" class="<?php echo $buttonNextColor;?> jsn-form-next hide"><?php echo $formSettings->form_btn_next_text ? JText::_($formSettings->form_btn_next_text) : JText::_('NEXT'); ?></button>
								<button class="<?php echo $buttonResetColor;?> jsn-form-reset hide" onclick="return false;"><?php echo $formSettings->form_btn_reset_text ? JText::_($formSettings->form_btn_reset_text) : JText::_('RESET'); ?></button>
								<button class="<?php echo $buttonSubmitColor;?> jsn-form-submit hide" onclick="return false;"><?php echo $formSettings->form_btn_submit_text ? JText::_($formSettings->form_btn_submit_text) : JText::_('SUBMIT'); ?></button>
								<?php
							}
							?>
							<input type="hidden" id="jform_form_btn_next_text" name="jsn_form_button[form_btn_next_text]" value="<?php echo !empty($formSettings->form_btn_next_text) ? $formSettings->form_btn_next_text : JText::_('NEXT');?>">
							<input type="hidden" id="jform_form_btn_prev_text" name="jsn_form_button[form_btn_prev_text]" value="<?php echo !empty($formSettings->form_btn_prev_text) ? $formSettings->form_btn_prev_text : JText::_('PREV');?>">
							<input type="hidden" id="jform_form_btn_submit_text" name="jsn_form_button[form_btn_submit_text]" value="<?php echo !empty($formSettings->form_btn_submit_text) ? $formSettings->form_btn_submit_text : JText::_('SUBMIT');?>">
							<input type="hidden" id="jform_form_btn_reset_text" name="jsn_form_button[form_btn_reset_text]" value="<?php echo !empty($formSettings->form_btn_reset_text) ? $formSettings->form_btn_reset_text : JText::_('RESET');?>">
							<input type="hidden" id="jform_form_state_btn_reset_text" name="jsn_form_button[form_state_btn_reset_text]" value="<?php echo !empty($formSettings->form_state_btn_reset_text) ? $formSettings->form_state_btn_reset_text : "No";?>">
						</div>
						<div class="jsn-iconbar">
							<a class="element-edit" title="Edit Button Action" onclick="return false;" href="#"><i class="icon-pencil"></i></a>
						</div>
					</div>
					<?php
					if (strtolower($edition) == "free")
					{
						?>
						<div class="settings-footer ui-state-default jsn-iconbar-trigger">
							<div class="jsn-text-center">
								<a target="_blank" href="http://www.joomlashine.com/joomla-extensions/jsn-uniform.html">Joomla
									forms builder</a> by
								<a target="_blank" href="http://www.joomlashine.com">JoomlaShine</a>
							</div>
							<div class="jsn-iconbar">
								<a class="element-delete" title="Delete footer coppyright" onclick="return false;" href="#"><i class="icon-trash"></i></a>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<input type="hidden" value="<?php echo htmlentities($this->form_page) ?>" id="jform_form_content" name="jform[form_content]">
		</div>
	</div>
</div>
</div>
<div id="form-action" class="form-horizontal">
	<div class="row-fluid">
		<div class="span6">
			<fieldset id="email">
				<legend>
					<?php echo JText::_('JSN_UNIFORM_FORM_EMAIL_NOTIFICATION'); ?>
				</legend>
				<?php echo JSNUniformHelper::getListEmailNotification($this->_fromEmail); ?>
				<div class="control-group jsn-items-list-container">
					<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_SELECT_EMAIL_FORM_FIELD'); ?>">
						<?php echo JText::_('JSN_UNIFORM_SEND_TO_SUBMITTER'); ?>
					</label>

					<div class="controls">
						<button class="btn btn-icon pull-right " id="btn_email_submit" original-title="<?php echo JText::_('JSN_UNIFORM_EMAIL_CONTENT'); ?>" onclick="return false;" title="<?php echo JText::_('JSN_UNIFORM_EMAIL_CONTENT'); ?>">
							<i class="icon-envelope"></i></button>
						<div class="email-submitters">
							<ul id="emailSubmitters" class="jsn-items-list ui-sortable"></ul>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="span6">
			<fieldset id="postaction">
				<legend>
					<?php echo JText::_('JSN_UNIFORM_POST_SUBMISSION_ACTION'); ?>
				</legend>
				<div class="control-group">
					<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_SAVE_SUBMISSIONS_DES'); ?>"><?php echo JText::_('JSN_UNIFORM_SAVE_SUBMISSIONS'); ?></label>

					<div class="controls">
						<?php
						$actionSaveSubmissions["Yes"] = 'checked="checked"';
						$actionSaveSubmissions["No"] = '';
						if (!empty($formSettings->action_save_submissions) && $formSettings->action_save_submissions == "No")
						{
							$actionSaveSubmissions["No"] = 'checked="checked"';
							$actionSaveSubmissions["Yes"] = '';
						}
						?>
						<label class="radio inline">
							<input type="radio" name="jsn_uniform_settings[action_save_submissions]" <?php echo $actionSaveSubmissions["No"];?> value="No"> No
						</label>
						<label class="radio inline">
							<input type="radio" name="jsn_uniform_settings[action_save_submissions]" <?php echo $actionSaveSubmissions["Yes"];?> value="Yes"> Yes
						</label>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_SELECT_THE_ACTION_TO_TAKE_AFTER'); ?>"><?php echo JText::_('JSN_UNIFORM_ALERT_FORM_SUBMITSSION'); ?></label>

					<div class="controls">
						<?php echo $this->_form->getInput('form_post_action') ?>
					</div>
				</div>
				<div class="control-group hide" id="form1">
					<label class="control-label"><?php echo JText::_('JSN_UNIFORM_URL'); ?></label>

					<div class="controls">
						<input type="text" placeholder="<?php echo JText::_('JSN_UNIFORM_PLACEHOLDER_REDIRECT_TO_URL'); ?>" class="jsn-input-xlarge-fluid" value="<?php echo $this->actionForm['redirect_to_url']; ?>" id="form_post_action_data1" name="form_post_action_data1">
					</div>
				</div>
				<div class="control-group hide" id="form2">
					<label class="control-label"><?php echo JText::_('JSN_UNIFORM_MENU_ITEM'); ?></label>

					<div class="controls">
						<div class="row-fluid input-append">
							<input type="hidden" class="jsn-input-large-fluid" name="form_post_action_data2" value="<?php echo $this->actionForm['menu_item']; ?>" id="form_post_action_data2">
							<input type="text" placeholder="<?php echo JText::_('JSN_UNIFORM_PLACEHOLDER_REDIRECT_TO_TO_MENU_ITEM'); ?>" disabled="disabled" class="jsn-input-large-fluid" value="<?php echo $this->actionForm['menu_item_title']; ?>" name="fr2_form_action_data_title" id="fr2_form_action_data_title">
							<button class="btn" id="list-menuit" onclick="return false;"><?php echo JText::_('JSN_UNIFORM_SELECTED'); ?></button>
						</div>
					</div>
				</div>
				<div class="control-group hide" id="form3">
					<label class="control-label"><?php echo JText::_('JSN_UNIFORM_ARTICLE'); ?></label>

					<div class="controls ">
						<div class="row-fluid input-append">
							<input type="hidden" class="jsn-input-large-fluid" name="form_post_action_data3" value="<?php echo $this->actionForm['article']; ?>" id="form_post_action_data3">
							<input type="text" placeholder="<?php echo JText::_('JSN_UNIFORM_PLACEHOLDER_SHOW_ARTICLE'); ?>" class="jsn-input-large-fluid" disabled="disabled" value="<?php echo $this->actionForm['article_title']; ?>" name="fr3_form_action_data_title" id="fr3_form_action_data_title">
							<button class="btn" id="list-article" onclick="return false;"><?php echo JText::_('JSN_UNIFORM_SELECTED'); ?></button>
						</div>
					</div>
				</div>
				<div class="control-group hide" id="form4">
					<label class="control-label"><?php echo JText::_('JSN_UNIFORM_CUSTOM_MESSAGE'); ?></label>

					<div class="controls">
						<textarea id="form_post_action_data4" placeholder="<?php echo JText::_('JSN_UNIFORM_PLACEHOLDER_SHOW_CUSTOM_MESSAGE'); ?>" name="form_post_action_data4" class="jsn-input-xlarge-fluid" rows="10"><?php echo $this->actionForm['message']; ?></textarea>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</div>
</div>
<div id="dialog-plugin">
	<div class="ui-dialog-content-inner jsn-bootstrap">
		<p><?php echo JText::_('JSN_UNIFORM_LAUNCHPAD_PLUGIN_SYNTAX_DES'); ?></p>

		<div id="jsn-clipboard">
		<span class="jsn-clipboard-input">
			<input type="text" value="" name="plugin" class="input-xlarge" id="syntax-plugin">
			<span class="jsn-clipboard-checkicon icon-ok"></span>
		</span>
		<span id="jsn-clipboard-container">
			<button class="btn" id="jsn-clipboard-button">Copy to clipboard</button>
		</span>
		</div>
	</div>
</div>
<?php echo $this->_form->getInput('form_layout') ?>
<input type="hidden" name="option" value="com_uniform" />
<input type="hidden" name="redirect_url" id="redirectUrl" value="" />
<input type="hidden" name="redirect_url_form" id="redirectUrlForm" value="" />
<input type="hidden" name="open_article" id="open-article" value="" />
<input type="hidden" name="task" id="jsn-task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>
</div>
<?php
if (JFactory::getApplication()->input->getVar('tmpl', '') != 'component')
{
	JSNHtmlGenerate::footer();
}
