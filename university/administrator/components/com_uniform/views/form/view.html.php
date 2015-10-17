<?php

/**
 * @version     $Id: view.html.php 19013 2012-11-28 04:48:47Z thailv $
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

jimport('joomla.application.component.view');
/**
 * View class for a list of Form.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.5
 */
class JSNUniformViewForm extends JSNBaseView
{

	protected $_document;
	protected $_formLayout;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @see     fetch()
	 * @since   11.1
	 */
	public function display($tpl = null)
	{
		// Initialize variables
		$session = JFactory::getSession();
		$seesionQueue = $session->get('application.queue');
		$input = JFactory::getApplication()->input;

		$this->urlAction = $input->getString('tmpl', '');

		$this->_document = JFactory::getDocument();
		$this->_item = $this->get('Item');

		$this->checkSubmitModal = false;
		if ($seesionQueue[0]['type'] != "warning")
		{
			unset($_SESSION["__form-design-" . $this->_item->form_id]);
			unset($_SESSION["__form-design-"]);
			if ($seesionQueue[0])
			{
				if ($this->urlAction == "component")
				{
					$this->checkSubmitModal = true;
				}
			}
		}
		$this->_listFontType = array(" Verdana, Geneva, sans-serif", "\"Times New Roman\", Times, serif", "\"Courier New\", Courier, monospace", "Tahoma, Geneva, sans-serif", "Arial, Helvetica, sans-serif", "\"Trebuchet MS\", Arial, Helvetica, sans-serif", "\"Arial Black\", Gadget, sans-serif", "\"Lucida Sans Unicode\", \"Lucida Grande\", sans-serif", "\"Palatino Linotype\", \"Book Antiqua\", Palatino, serif", "\"Comic Sans MS\", cursive");
		$formContent = $this->_item->form_content;
		$this->_listPage = JSNUniformHelper::getListPage($formContent, $this->_item->form_id);
		$this->_form = $this->get('Form');
		$this->_fromEmail = array();
		if (empty($this->_item->form_id))
		{
			$this->_fromConfig = $this->get('DataConfig');
			$this->formAction = 0;
			$this->formActionData = '';
			foreach ($this->_fromConfig as $formConfig)
			{
				if (isset($formConfig->name) && $formConfig->name == 'email_notification')
				{
					$this->_fromEmail = json_decode($formConfig->value);
				}
				if (isset($formConfig->name) && $formConfig->name == 'form_action')
				{
					$this->formAction = $formConfig->value;
				}
			}
			foreach ($this->_fromConfig as $formConfig)
			{
				if ($this->formAction == 1 && $formConfig->name == 'form_action_url')
				{
					$this->formActionData = $formConfig->value;
				}
				if ($this->formAction == 2 && $formConfig->name == 'form_action_menu')
				{
					$this->formActionData = json_decode($formConfig->value);
				}
				if ($this->formAction == 3 && $formConfig->name == 'form_action_article')
				{
					$this->formActionData = json_decode($formConfig->value);
				}
				if ($this->formAction == 4 && $formConfig->name == 'form_action_message')
				{
					$this->formActionData = $formConfig->value;
				}
			}
		}
		else
		{
			$this->_fromEmail = $this->get('FormEmail');
		}
		$this->form_page = isset($formContent[0]->page_content) ? $formContent[0]->page_content : "";
		$this->actionForm = array('redirect_to_url' => "", 'menu_item' => "", 'menu_item_title' => "", 'article' => "", 'article_title' => "", 'message' => "", 'action' => "1");
		$this->actionForm = JSNUniformHelper::actionFrom($this->_item->form_post_action, $this->_item->form_post_action_data);
		$this->globalFormStyle = JSNUniformHelper::getDataConfig("form_style");
		$this->formStyle = new stdClass;
		if (!empty($this->_item->form_style))
		{
			$this->formStyle = json_decode($this->_item->form_style);
		}
		else
		{
			$this->formStyle->themes_style->light = '{"background_color":"","background_active_color":"#FCF8E3","border_thickness":"0","border_color":"","border_active_color":"#FBEED5","rounded_corner_radius":"0","padding_space":"10","margin_space":"0","text_color":"#333333","font_type":" Verdana, Geneva, sans-serif","font_size":"14","field_background_color":"#ffffff","field_border_color":"","field_shadow_color":"","field_text_color":"#666666","message_error_background_color":"#B94A48","message_error_text_color":"#FFFFFF"}';
			$this->formStyle->themes_style->dark = '{"background_color":"","background_active_color":"#444444","border_thickness":"0","border_color":"","border_active_color":"#666666","rounded_corner_radius":"0","padding_space":"10","margin_space":"0","text_color":"#C6C6C6","font_type":" Verdana, Geneva, sans-serif","font_size":"14","field_background_color":"#000000","field_border_color":"#111111","field_shadow_color":"#000000","field_text_color":"#333333","message_error_background_color":"#B94A48","message_error_text_color":"#FFFFFF"}';
			$this->formStyle->themes = array('light', 'dark');
		}

		if (!empty($this->globalFormStyle))
		{
			$globalFormStyle = json_decode($this->globalFormStyle->value);

			if (!empty($globalFormStyle->themes_style))
			{
				foreach ($globalFormStyle->themes_style as $key => $value)
				{
					$this->formStyle->themes_style->{$key} = $value;
				}
			}
			if (!empty($globalFormStyle->themes))
			{
				foreach ($globalFormStyle->themes as $key => $value)
				{
					$this->formStyle->themes[] = $value;
				}
			}
		}

		if (!empty($this->formStyle->theme) && !empty($this->formStyle->themes_style) && $this->formStyle->theme != "jsn-style-light" && $this->formStyle->theme != "jsn-style-dark")
		{
			$theme = str_replace("jsn-style-", "", $this->formStyle->theme);
			if (!empty($this->formStyle->themes_style->{$theme}))
			{
				$styles = json_decode($this->formStyle->themes_style->{$theme});
			}
			$this->formStyle->background_color = !empty($styles->background_color) ? $styles->background_color : "";
			$this->formStyle->background_active_color = !empty($styles->background_active_color) ? $styles->background_active_color : "";
			$this->formStyle->border_active_color = !empty($styles->border_active_color) ? $styles->border_active_color : "";
			$this->formStyle->border_thickness = !empty($styles->border_thickness) ? $styles->border_thickness : "";
			$this->formStyle->border_color = !empty($styles->border_color) ? $styles->border_color : "";
			$this->formStyle->rounded_corner_radius = !empty($styles->rounded_corner_radius) ? $styles->rounded_corner_radius : "";
			$this->formStyle->padding_space = !empty($styles->padding_space) ? $styles->padding_space : "";
			$this->formStyle->margin_space = !empty($styles->margin_space) ? $styles->margin_space : "";
			$this->formStyle->text_color = !empty($styles->text_color) ? $styles->text_color : "";
			$this->formStyle->font_type = !empty($styles->font_type) ? $styles->font_type : "";
			$this->formStyle->font_size = !empty($styles->font_size) ? $styles->font_size : "";
		}
		else
		{
			$this->formStyle->background_color = !empty($this->formStyle->background_color) ? $this->formStyle->background_color : "";
			$this->formStyle->background_active_color = !empty($this->formStyle->background_active_color) ? $this->formStyle->background_active_color : "";
			$this->formStyle->border_active_color = !empty($this->formStyle->border_active_color) ? $this->formStyle->border_active_color : "";
			$this->formStyle->border_thickness = !empty($this->formStyle->border_thickness) ? $this->formStyle->border_thickness : "";
			$this->formStyle->border_color = !empty($this->formStyle->border_color) ? $this->formStyle->border_color : "";
			$this->formStyle->rounded_corner_radius = !empty($this->formStyle->rounded_corner_radius) ? $this->formStyle->rounded_corner_radius : "";
			$this->formStyle->padding_space = !empty($this->formStyle->padding_space) ? $this->formStyle->padding_space : "";
			$this->formStyle->margin_space = !empty($this->formStyle->margin_space) ? $this->formStyle->margin_space : "";
			$this->formStyle->text_color = !empty($this->formStyle->text_color) ? $this->formStyle->text_color : "";
			$this->formStyle->font_type = !empty($this->formStyle->font_type) ? $this->formStyle->font_type : "";
			$this->formStyle->font_size = !empty($this->formStyle->font_size) ? $this->formStyle->font_size : "";

			if (empty($this->formStyle->background_active_color) && empty($this->formStyle->border_active_color) && empty($this->formStyle->border_thickness) && empty($this->formStyle->border_color) && empty($this->formStyle->rounded_corner_radius) && empty($this->formStyle->background_color) && empty($this->formStyle->font_size) && empty($this->formStyle->text_color) && empty($this->formStyle->margin_space) && empty($this->formStyle->padding_space))
			{
				$this->formStyle->theme = "";
				$this->formStyle->themes_style->light = '{"background_color":"","background_active_color":"#FCF8E3","border_thickness":"0","border_color":"","border_active_color":"#FBEED5","rounded_corner_radius":"0","padding_space":"10","margin_space":"0","text_color":"#333333","font_type":" Verdana, Geneva, sans-serif","font_size":"14","field_background_color":"#ffffff","field_border_color":"","field_shadow_color":"","field_text_color":"#666666","message_error_background_color":"#B94A48","message_error_text_color":"#FFFFFF"}';
				$this->formStyle->themes_style->dark = '{"background_color":"","background_active_color":"#444444","border_thickness":"0","border_color":"","border_active_color":"#666666","rounded_corner_radius":"0","padding_space":"10","margin_space":"0","text_color":"#C6C6C6","font_type":" Verdana, Geneva, sans-serif","font_size":"14","field_background_color":"#000000","field_border_color":"#111111","field_shadow_color":"#000000","field_text_color":"#333333","message_error_background_color":"#B94A48","message_error_text_color":"#FFFFFF"}';
				$this->formStyle->themes = array('light', 'dark');
			}
		}
		// Hide the main menu
		$input->set('hidemainmenu', true);

		// Initialize toolbar
		$this->initToolbar();

		// Get config
		$config = JSNConfigHelper::get();
		$msgs = '';

		if (!$config->get('disable_all_messages'))
		{
			$msgs = JSNUtilsMessage::getList('FORMS');
			$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';
		}
		// Assign variables for rendering
		$this->assignRef('msgs', $msgs);

		// Display the template
		parent::display($tpl);

		// Load assets
		JSNUniformHelper::addAssets();
		$this->addAssets();
	}

	/**
	 * Setup toolbar.
	 *
	 * @return void
	 */
	protected function initToolbar()
	{
		JToolBarHelper::apply('form.apply');
		JToolBarHelper::save('form.save');

		// Create a toolbar button that drop-down a sub-menu when clicked
		JSNMenuHelper::addEntry(
			'toolbar-save', 'JSN_UNIFORM_SAVE_AND_SHOW', '', false, 'jsn-icon16 jsn-icon-file', 'toolbar'
		);

		// Declare 1st-level menu items
		JSNMenuHelper::addEntry(
			'component',
			'JSN_UNIFORM_FORM_VIA_MENU_ITEM_COMPONENT',
			'',
			false,
			'',
			'toolbar-save'
		);

		JSNMenuHelper::addEntry(
			'module',
			'JSN_UNIFORM_FORM_IN_MODULE_POSITION_MODULE',
			'index.php?option=com_uniform&task=launchAdapter&type=module',
			false,
			'',
			'toolbar-save',
			'action-save-show'
		);

		JSNMenuHelper::addEntry(
			'article-content-plugin',
			'JSN_UNIFORM_FORM_INSIDE_ARTICLE_CONTENT_PLUGIN',
			'',
			false,
			'',
			'toolbar-save'
		);

		if (count($optionMenus = JSNUniformHelper::getOptionMenus()))
		{
			foreach ($optionMenus AS $option)
			{
				JSNMenuHelper::addEntry(
					preg_replace('/[^a-z0-9\-_]/', '-', $option->text),
					$option->text,
					'index.php?option=com_uniform&task=launchAdapter&type=menu&menutype=' . $option->value,
					false,
					'',
					'toolbar-save.component',
					'action-save-show'
				);
			}
		}

		JToolBarHelper::cancel('form.cancel', 'JSN_UNIFORM_CLOSE');

		JSNUniformHelper::initToolbar('JSN_UNIFORM_FORM_PAGETITLE', 'uniform-forms', false);
	}

	/**
	 * Add the libraries css and javascript
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addAssets()
	{
		JSNHtmlAsset::addStyle(
			array(
				JURI::base(true) . '/components/com_uniform/assets/js/libs/colorpicker/css/colorpicker.css',
				JSN_URL_ASSETS . '/3rd-party/jquery-jwysiwyg/jquery.wysiwyg-0.9.css',
				JURI::base(true) . '/components/com_uniform/assets/js/libs/select2/select2.css',
				JSN_URL_ASSETS . '/3rd-party/codemirror/lib/codemirror.css',
				JSN_URL_ASSETS . '/3rd-party/jquery-tipsy/tipsy.css'
			)
		);
		$formSubmitter = isset($this->_item->form_submitter) ? json_decode($this->_item->form_submitter) : "";

		$formAction = 0;
		$formActionData = '';
		if (!empty($this->formAction))
		{
			$formAction = $this->formAction;
			$formActionData = isset($this->formActionData) ? $this->formActionData : '';
		}
		$arrayTranslated = array('JSN_UNIFORM_MOVE_UP_CONTAINER', 'JSN_UNIFORM_MOVE_DOWN_CONTAINER', 'JSN_UNIFORM_ADD_CONTAINER_COLUMN', 'JSN_UNIFORM_DELETE_CONTAINER', 'JSN_UNIFORM_DELETE_CONTAINER_COLUMN', 'JSN_UNIFORM_CONFIRM_DELETE_CONTAINER', 'JSN_UNIFORM_CONFIRM_DELETE_CONTAINER_COLUMN', 'JSN_UNIFORM_COLOR_CONFIRM_RESET', 'JSN_UNIFORM_COLOR_CONFIRM_DELETE', 'JSN_UNIFORM_COLOR_CONFIRM_EXISTS', 'JSN_UNIFORM_ALL_FORM_FIELD_ARE_HIDDEN', 'JSN_UNIFORM_ALL_FORM_FIELD_ARE_DISPLAYED', 'TITLES', 'JSN_UNIFORM_DATE_HOUR_TEXT', 'JSN_UNIFORM_DATE_MINUTE_TEXT', 'JSN_UNIFORM_DATE_CLOSE_TEXT', 'JSN_UNIFORM_DATE_PREV_TEXT', 'JSN_UNIFORM_DATE_NEXT_TEXT', 'JSN_UNIFORM_DATE_CURRENT_TEXT', 'JSN_UNIFORM_DATE_MONTH_JANUARY', 'JSN_UNIFORM_DATE_MONTH_FEBRUARY', 'JSN_UNIFORM_DATE_MONTH_MARCH', 'JSN_UNIFORM_DATE_MONTH_APRIL', 'JSN_UNIFORM_DATE_MONTH_MAY', 'JSN_UNIFORM_DATE_MONTH_JUNE', 'JSN_UNIFORM_DATE_MONTH_JULY', 'JSN_UNIFORM_DATE_MONTH_AUGUST', 'JSN_UNIFORM_DATE_MONTH_SEPTEMBER', 'JSN_UNIFORM_DATE_MONTH_OCTOBER', 'JSN_UNIFORM_DATE_MONTH_NOVEMBER', 'JSN_UNIFORM_DATE_MONTH_DECEMBER', 'JSN_UNIFORM_DATE_MONTH_JANUARY_SHORT', 'JSN_UNIFORM_DATE_MONTH_FEBRUARY_SHORT', 'JSN_UNIFORM_DATE_MONTH_MARCH_SHORT', 'JSN_UNIFORM_DATE_MONTH_APRIL_SHORT', 'JSN_UNIFORM_DATE_MONTH_MAY_SHORT', 'JSN_UNIFORM_DATE_MONTH_JUNE_SHORT', 'JSN_UNIFORM_DATE_MONTH_JULY_SHORT', 'JSN_UNIFORM_DATE_MONTH_AUGUST_SHORT', 'JSN_UNIFORM_DATE_MONTH_SEPTEMBER_SHORT', 'JSN_UNIFORM_DATE_MONTH_OCTOBER_SHORT', 'JSN_UNIFORM_DATE_MONTH_NOVEMBER_SHORT', 'JSN_UNIFORM_DATE_MONTH_DECEMBER_SHORT', 'JSN_UNIFORM_DATE_DAY_SUNDAY', 'JSN_UNIFORM_DATE_DAY_MONDAY', 'JSN_UNIFORM_DATE_DAY_TUESDAY', 'JSN_UNIFORM_DATE_DAY_WEDNESDAY', 'JSN_UNIFORM_DATE_DAY_THURSDAY', 'JSN_UNIFORM_DATE_DAY_FRIDAY', 'JSN_UNIFORM_DATE_DAY_SATURDAY', 'JSN_UNIFORM_DATE_DAY_SUNDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_MONDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_TUESDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_WEDNESDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_THURSDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_FRIDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_SATURDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_SUNDAY_MIN', 'JSN_UNIFORM_DATE_DAY_MONDAY_MIN', 'JSN_UNIFORM_DATE_DAY_TUESDAY_MIN', 'JSN_UNIFORM_DATE_DAY_WEDNESDAY_MIN', 'JSN_UNIFORM_DATE_DAY_THURSDAY_MIN', 'JSN_UNIFORM_DATE_DAY_FRIDAY_MIN', 'JSN_UNIFORM_DATE_DAY_SATURDAY_MIN', 'JSN_UNIFORM_DATE_DAY_WEEK_HEADER', 'JSN_UNIFORM_EMAIL_SETTINGS', 'JSN_UNIFORM_SELECT_MENU_ITEM', 'JSN_UNIFORM_SELECT_ARTICLE', 'JSN_UNIFORM_FORM_APPEARANCE', 'JSN_UNIFORM_SELECT', 'JSN_UNIFORM_SAVE', 'JSN_UNIFORM_CANCEL', 'JSN_UNIFORM_ADD_FIELD', 'JSN_UNIFORM_BUTTON_SAVE', 'JSN_UNIFORM_BUTTON_CANCEL', 'JSN_UNIFORM_CONFIRM_CONVERTING_FORM', 'JSN_UNIFORM_UPGRADE_EDITION_TITLE', 'JSN_UNIFORM_YOU_HAVE_REACHED_THE_LIMITATION_OF_10_FIELD_IN_FREE_EDITION', 'JSN_UNIFORM_YOU_HAVE_REACHED_THE_LIMITATION_OF_1_PAGE_IN_FREE_EDITION', 'JSN_UNIFORM_UPGRADE_EDITION', 'JSN_UNIFORM_CONFIRM_SAVE_FORM', 'JSN_UNIFORM_NO_EMAIL', 'JSN_UNIFORM_NO_EMAIL_DES', 'JSN_UNIFORM_CONFIRM_DELETING_A_FIELD', 'JSN_UNIFORM_CONFIRM_DELETING_A_FIELD_DES', 'JSN_UNIFORM_BTN_BACKUP', 'JSN_UNIFORM_IF_CHECKED_VALUE_DUPLICATION', 'JSN_UNIFORM_EMAIL_SUBMITTER_TITLE', 'JSN_UNIFORM_EMAIL_ADDRESS_TITLE', 'JSN_UNIFORM_LAUNCHPAD_PLUGIN_SYNTAX', 'JSN_UNIFORM_LAUNCHPAD_PLUGIN_SYNTAX_DES', 'JSN_UNIFORM_FORM_LIMIT_FILE_EXTENSIONS', 'JSN_UNIFORM_FOR_SECURITY_REASONS_FOLLOWING_FILE_EXTENSIONS', 'JSN_UNIFORM_FORM_LIMIT_FILE_SIZE', 'STREET_ADDRESS', 'ADDRESS_LINE_2', 'CITY', 'POSTAL_ZIP_CODE', 'STATE_PROVINCE_REGION', 'FIRST', 'MIDDLE', 'LAST', 'COUNTRY', 'JSN_UNIFORM_ALLOW_USER_CHOICE', 'JSN_UNIFORM_SET_ITEM_PLACEHOLDER', 'JSN_UNIFORM_SET_ITEM_PLACEHOLDER_DES', 'JSN_UNIFORM_SHOW_DATE_FORMAT', 'JSN_UNIFORM_SHOW_TIME_FORMAT', 'JSN_UNIFORM_ENABLE_RANGE_SELECTION', 'JSN_UNIFORM_YOU_CAN_NOT_HIDE_THE_COPYLINK', 'JSN_UNIFORM_CUSTOM_DATE_FORMAT');
		$params = JComponentHelper::getParams('com_media');
		$listEx = '';
		$extensions = $params->get('upload_extensions');
		if ($extensions)
		{
			$extensions = explode(",", $extensions);
			$exs = array();
			foreach ($extensions as $ex)
			{
				$exs[] = strtolower($ex);
			}
			$listEx = implode(", ", array_unique($exs));
		}
		$extensions = str_replace(",", ", ", $extensions);
		$limitSize = $params->get('upload_maxsize');
		$configSizeSever = (int) (ini_get('post_max_size'));
		if ($limitSize > $configSizeSever)
		{
			$limitSize = $configSizeSever;
		}
		if ($limitSize > (int) (ini_get('upload_max_filesize')))
		{
			$limitSize = (int) (ini_get('upload_max_filesize'));
		}
		$session = JFactory::getSession();
		$openArticle = JRequest::getVar('opentarticle', '');
		$this->pageContent = $session->get('page_content', '', 'form-design-' . $this->_item->form_id);
		$this->edition = defined('JSN_UNIFORM_EDITION') ? strtolower(JSN_UNIFORM_EDITION) : "free";

		JSNHtmlAsset::registerDepends('uniform/libs/googlemaps/jquery.ui.map', array('jquery', 'jquery.ui'));
		JSNHtmlAsset::registerDepends('uniform/libs/googlemaps/jquery.ui.map.services', array('jquery', 'jquery.ui', 'uniform/libs/googlemaps/jquery.ui.map'));
		JSNHtmlAsset::registerDepends('uniform/libs/googlemaps/jquery.ui.map.extensions', array('jquery', 'jquery.ui', 'uniform/libs/googlemaps/jquery.ui.map'));
		JSNHtmlAsset::addScript('http://maps.google.com/maps/api/js?sensor=false&libraries=places');

		JSNHtmlAsset::registerDepends('uniform/libs/jquery.tmpl', array('jquery'));
		JSNHtmlAsset::registerDepends('uniform/libs/jquery-ui-timepicker-addon', array('jquery', 'jquery.ui'));
		JSNHtmlAsset::registerDepends('uniform/libs/jquery.placeholder', array('jquery'));
		JSNHtmlAsset::registerDepends('uniform/libs/colorpicker/js/colorpicker', array('jquery'));
		JSNHtmlAsset::registerDepends('uniform/libs/select2/select2', array('jquery'));
		JSNHtmlAsset::registerDepends('uniform/layout', array('jquery', 'jquery.ui'));

		$titleForm = isset($_GET['form']) ? $_GET['form'] : '';
		echo JSNHtmlAsset::loadScript('uniform/form', array('urlBase' => JURI::base(), 'pathRoot' => JURI::root(), 'opentArticle' => $openArticle, 'baseZeroClipBoard' => JSN_URL_ASSETS . '/3rd-party/jquery-zeroclipboard/ZeroClipboard.swf', 'pageContent' => $this->pageContent, 'edition' => $this->edition, 'checkSubmitModal' => $this->checkSubmitModal, 'urlAction' => $this->urlAction, 'form_style' => $this->_item->form_style, 'dataEmailSubmitter' => $formSubmitter, 'language' => JSNUtilsLanguage::getTranslated($arrayTranslated), 'formActionData' => $formActionData, 'formAction' => $formAction, 'limitEx' => $listEx, 'limitSize' => $limitSize, 'titleForm' => $titleForm), true);
	}
}