<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: poweradmin.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.module.helper');

/**
 * @package		Joomla.Site
 * @subpackage	com_poweradmin
 */
abstract class PoweradminFrontHelper
{
	
	/**
	* Changes the links (src/href) of HTML
	* @return: String 
	*/
	public function changeLinks($contents, $root_path = JPATH_ROOT, $base_uri = '' )
	{
		/** change js links	**/
		$regex = '/src=(["\'])(.*?)\1/';
		$count = preg_match_all($regex, $contents, $match);
		if ($count > 0)
		{
			$changes = $match[2];
			foreach($changes as $change)
			{
				//remove url file not exists				
				$uri = new JURI($change);
				if ($_SERVER['HTTP_HOST'] != $uri->getHost() && $uri->getHost() != ''){
					$headers = @get_headers($change, 1);
					if ($headers[0] == 'HTTP/1.1 404 Not Found') {
						$contents = str_replace('src="'.$change.'"', 'src=""', $contents);
					}
				}
			}
		}

		/** change href reference **/
		$regex = '/href=(["\'])(.*?)\1/';
		$count = preg_match_all($regex, $contents, $match);
		if ($count > 0)
		{
			$changes = $match[2];
			foreach($changes as $change)
			{
				//remove url file not exists				
			    $uri = new JURI($change);
				if ($_SERVER['HTTP_HOST'] != $uri->getHost() && $uri->getHost() != ''){
					$headers = @get_headers($change, 1);
					if ($headers[0] == 'HTTP/1.1 404 Not Found') {
						$contents = str_replace('href="'.$change.'"', 'href=""', $contents);
					}
				}
			}
		}
		
		/** change href reference **/
		$regex = '/action=(["\'])(.*?)\1/';
		$count = preg_match_all($regex, $contents, $match);
		if ($count > 0)
		{
			$changes = $match[2];
			foreach($changes as $change)
			{ 
				//remove url file not exists				
			    $uri = new JURI($change);
				if ($_SERVER['HTTP_HOST'] != $uri->getHost() && $uri->getHost() != ''){
					$headers = @get_headers($change, 1);
					if ($headers[0] == 'HTTP/1.1 404 Not Found') {
						$contents = str_replace('action="'.$change.'"', 'action=""', $contents);
					}
				}
			}
		}
		
		/** change links **/
		$doc = new DOMDocument();
		if ( @$doc->loadhtml( $contents ) ){
		    $xpath = new DOMXpath( $doc );
			$uri = JURI::getInstance();
		    foreach($xpath->query('//html//a') as $eInput) {
				$href = $eInput->getAttribute('href');
		    	$uri  = new JURI($href);
				if (JString::trim($href) != '#' && JString::trim($href) != '' && ($_SERVER['HTTP_HOST'] == $uri->getHost() || $uri->getHost() == '')){
					$extend_url = JSN_RENDER_PAGE_URL.base64_encode($href);				
					$eInput->setAttribute('href', $extend_url);
				}else{
					$eInput->setAttribute('href', 'javascript:;');
				}
		    }
		}
		
		/** SAVE HTML after changed **/
		return $doc->saveHTML();
	}
	
	/**
	* render module and make an format support for poweradmin component
	*
	* @param: $module is object row of database 
	* @param: $attributes are attributes for module
	* @return: HTML of module after rendered
	*/
	public function renderModule( $module, $attributes = array(), $showmode )
	{
		$published     = ($module->published == 0) ? 'unpublished':'published';		
		$class_publish = ($module->published == 0 || $module->assignment == '' || $module->assignment == 'except') ? ' jsn-module-unpublish':'';

		ob_start();
		if ($showmode == 'visualmode')
		{
		?>
		<div class="poweradmin-module-item<?php echo $class_publish;?>" id="<?php echo $module->id;?>-jsnposition-<?php echo $published;?>" title="<?php echo $module->title?>" assignment="<?php echo $module->assignment;?>">
			<div id="<?php echo $module->id;?>-content">
				<?php 
					$contents = JModuleHelper::renderModule($module, $attributes);
					echo PoweradminFrontHelper::changeLinks($contents, JPATH_ROOT, JURI::root());
				?>
			</div>
		</div>
		<?php
		}else if ($module->id > 0){
		?>
		<div class="poweradmin-module-item<?php echo $class_publish;?>" id="<?php echo $module->id;?>-jsnposition-<?php echo $published;?>"  showtitle="<?php echo $module->showtitle;?>" assignment="<?php echo $module->assignment;?>">
		    <?php
		    	if ($module->assignment == '' || $module->assignment == 'except'){
		    		?><span class="not-assigned-indicate"></span><?php 
		    	} 
		    ?>
		    
			<div class="poweradmin-module-item-inner">
				<div class="poweradmin-module-item-inner-text">
					<?php echo $module->title;?>					
				</div>
				<div class="jsn-clearfix"></div>
			</div>
			<div class="jsn-clearfix"></div>
		</div>
		<?php 
		}
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
	
}

