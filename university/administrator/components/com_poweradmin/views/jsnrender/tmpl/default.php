<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: default.php 16006 2012-09-13 03:29:17Z hiepnv $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if ( $this->jsnpwrender->isExternal() ){
	//External link.
	 echo JSNHtmlHelper::addCustomScript('', '', "
	 	var answer = confirm(JSNLang.translate('".JText::_('JSN_CONFIRM_LINK_RENDER',true)."'));
	 	if ( answer) {
			history.go(-1);
		}else{
			return;
		}	    
	 ");	
	jexit();
}else{
	$doc = JFactory::getDocument();
	error_reporting(0);
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo  $doc->getLanguage(); ?>" lang="<?php echo  $doc->getLanguage(); ?>" dir="<?php echo  $doc->getDirection(); ?>" >
	<head>
	<style type="text/css">
		body{
			background:#FFF;
		}
	</style>
	<?php	

	/** Add Scripts and StyleSheets to header of page **/
	$header = array();
	$header[] = JSNHtmlHelper::addCustomStyle(JSN_POWERADMIN_STYLE_URI, 'styles.css');
	$header[] = JSNHtmlHelper::addCustomStyle(JSN_POWERADMIN_STYLE_URI, 'visualmode_styles.css');
	$header[] = JSNHtmlHelper::addCustomStyle(JURI::root().'media/system/css/', 'modal.css');	
	$header[] = $this->jsnpwrender->getHeader();
	echo implode(PHP_EOL, $header);
	$this->JSNMedia->addMedia();
	?>
	</head>
	<?php 
		$body = $this->jsnpwrender->getBody(); 
	?>
	<body <?php echo $body->attr;?>>
		<div id="jsn-page-container" >
			<?php echo $body->html;?>
		</div>	
	</body>
	</html>
	<?php
}
