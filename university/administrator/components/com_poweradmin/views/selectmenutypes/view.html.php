<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: view.html.php 13486 2012-06-23 08:57:04Z thangbh $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');

class PoweradminViewSelectmenutypes extends JViewLegacy
{
	public function display($tpl = null)
	{
		$JSNMedia = JSNFactory::getMedia();
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI.'jsn.filter.js');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$language = JFactory::getLanguage();
		$language->load('com_menus');

		$model 			= $this->getModel();
		$this->assign('model', $model);
		
		$menutype   = '';
		$menutypeid = JRequest::getVar("menutypeid", '');
		if ($menutypeid) {
			JSNFactory::localimport('models.menuitem');
			$paMenuModel	= new PoweradminModelMenuitem();
			$menutype	= $paMenuModel->getMenuType($menutypeid);
			
		}		
		$parentid   = JRequest::getVar("parentid", '');

		$customScript = "
			var selectMenuType;
            (function($){
				$(window).ready(function(){
				  	  selectMenuType =  $.jsnFilter(
				  	  {
			  	  		  frameElement: $('.jsn-menu-type'),
			  	  		  category    : true,
					  	  itemClass   : '.jsn-item-type',
					  	  totalColumn : 3,
					  	  itemWidth   : 220,
					  	  itemHeight  : 30,
					  	  mPosLeft    : 0,
					  	  mPosTop     : 15,
					  	  marginOffset: {
					  	  	  right : 15,
					  	  	  bottom: 20
					  	  },
					  	  eventClick: function(){
					  	  	 var params = $(this).attr('params');
					  	  	 window.parent.JoomlaShine.jQuery.addNewMenuItem(params, '".$menutype."', '".$menutypeid."', '".$parentid."');
					  	  }
				  	  	}
				  	  );
				  });
			  })(JoomlaShine.jQuery);
		";
		$JSNMedia->addScriptDeclaration( $customScript );

		return parent::display();
	}
}