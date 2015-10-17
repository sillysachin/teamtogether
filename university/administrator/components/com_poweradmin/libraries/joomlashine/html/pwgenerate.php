<?php
/**
 * @author    JoomlaShine.com
 * @copyright JoomlaShine.com
 * @link      http://joomlashine.com/
 * @package   JSN Poweradmin
 * @version   $Id$
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class JSNPwgenerate extends JSNHtmlGenerate
{
    public static function about($products = array())
    {
        // Get extension manifest cache
		$info = JSNUtilsXml::loadManifestCache();

		// Add assets
		JSNHtmlAsset::loadScript('jsn/about',
			array(
				'language' => JSNUtilsLanguage::getTranslated(array('JSN_EXTFW_ABOUT_SEE_OTHERS_MODAL_TITLE','JSN_EXTFW_GENERAL_CLOSE'))
			)
		);

		// Generate markup
		$html[] = '
<div id="jsn-about" class="jsn-page-about">
<div class="jsn-bootstrap">';
		$html[] = self::aboutInfo($info, $products);
		$html[] = '
	<div class="jsn-product-support">';
		$html[] = self::aboutHelp();
		$html[] = self::aboutFeedback();
		$html[] = '
	</div>
</div>
</div>
<div class="clr"></div>';

		echo implode($html);
    }

    public static function aboutHelp($links = array())
    {
    	$links['doc']		= JSNUtilsText::getConstant('DOC_LINK');
    	
        $html[] = '<div>
			<h3 class="jsn-section-header">' . JText::_('JSN_EXTFW_ABOUT_HELP') . '</h3>
			<p>' . JText::_('JSN_EXTFW_ABOUT_HAVE_PROBLEMS') . ':</p>
			<ul>';

		if ( ! empty($links['doc']))
		{
			$html[] = '
				<li>' . JText::sprintf('JSN_EXTFW_ABOUT_READ_DOCS', JRoute::_($links['doc'])) . '</li>';
		}

		$html[] = '
				<li>' . JText::_('JSN_EXTFW_ABOUT_ASK_FORUM') . '</li>
				<li>' . JText::_('JSN_EXTFW_ABOUT_DEDICATED_SUPPORT') . '</li>
			</ul>
		</div>';

		return implode($html);
    }
}