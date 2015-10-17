<?php
/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Helper class for generating and embedding HTML markup into view.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNHtmlGenerate
{
	/**
	 * Generate HTML markup for about page.
	 *
	 * If a product has sub-product, this method need to be called as below to
	 * check all sub-product for latest version:
	 *
	 * <pre>JSNHtmlGenerate::about(
	 *     array(
	 *         // Core component
	 *         'imageshow' => '4.2.0',
	 *
	 *         // Themes
	 *         'themeclassic' => '1.1.5',
	 *         'themeslider'  => '1.0.4',
	 *         'themegrid'    => '1.0.0',
	 *
	 *         // Sources
	 *         'picasa'      => '1.1.2',
	 *         'flickr'      => '1.1.2',
	 *         'phoca'       => '1.0.1',
	 *         'joomgallery' => '1.0.1',
	 *         'rsgallery2'  => '1.0.1',
	 *         'facebook'    => '1.0.1'
	 *     )
	 * );</pre>
	 *
	 * If a product does not have sub-product, the <b>$products</b> parameter
	 * does not required when calling this method:
	 *
	 * <pre>JSNHtmlGenerate::about();</pre>
	 *
	 * @param   array  $products  Array of product identified name.
	 *
	 * @return  string
	 */
	public static function about($products = array())
	{
		// Get extension manifest cache
		$info = JSNUtilsXml::loadManifestCache();

		// Add assets
		JSNBaseHelper::loadAssets();

		JSNHtmlAsset::loadScript(
			'jsn/about',
			array(
				'language' => JSNUtilsLanguage::getTranslated(array('JSN_EXTFW_ABOUT_SEE_OTHERS_MODAL_TITLE'))
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

	/**
	 * Generate HTML info part for about page.
	 *
	 * If a product has sub-product, this method requires the second parameter
	 * in the format look like below:
	 *
	 * <pre>array(
	 *     // Core component
	 *     'imageshow' => '4.2.0',
	 *
	 *     // Themes
	 *     'themeclassic' => '1.1.5',
	 *     'themeslider'  => '1.0.4',
	 *     'themegrid'    => '1.0.0',
	 *
	 *     // Sources
	 *     'picasa'      => '1.1.2',
	 *     'flickr'      => '1.1.2',
	 *     'phoca'       => '1.0.1',
	 *     'joomgallery' => '1.0.1',
	 *     'rsgallery2'  => '1.0.1',
	 *     'facebook'    => '1.0.1'
	 * )</pre>
	 *
	 * If a product does not have sub-product, the <b>$products</b> parameter
	 * does not required when calling this method:
	 *
	 * <pre>JSNHtmlGenerate::aboutInfo($info);</pre>
	 *
	 * @param   object  $info      JSON decoded extension's manifest cache.
	 * @param   array   $products  Array of product identified name.
	 *
	 * @return  string
	 */
	public static function aboutInfo($info, $products = array())
	{
		$name		= $info->name;
		$edition	= JSNUtilsText::getConstant('EDITION');
		$version	= JSNUtilsText::getConstant('VERSION');

		// Initialize links
		$links['info']		= JSNUtilsText::getConstant('INFO_LINK');
		$links['update']	= JSNUtilsText::getConstant('UPDATE_LINK');
		$links['upgrade']	= JSNUtilsText::getConstant('UPGRADE_LINK');

		$html[] = '
		<div class="jsn-product-about jsn-pane jsn-bgpattern pattern-sidebar">';
		$html[] = '
			<h2 class="jsn-section-header"><a href="' . JRoute::_($links['info']) . '" target="_blank">JSN ' . preg_replace('/JSN\s*/i', '', JText::_($name)) . ' ' . $edition . '</a>';
		if ( ! empty($edition) AND ! empty($links['upgrade']) AND ($pos = strpos('free + pro standard', strtolower($edition))) !== false)
		{
			$html[] = '<a href="' . JRoute::_($links['upgrade']) . '" class="btn pull-right" title="' . (($pos)?JText::_('JSN_EXTFW_ABOUT_UPGRADE_TO_PRO_UNLIMITED'):JText::_('JSN_EXTFW_ABOUT_UPGRADE_TO_PRO')) . '"><span class="label label-important">PRO</span>' . JText::_('JSN_EXTFW_GENERAL_UPGRADE') . '</a>';
		}
		$html[] = '</h2>';

			$html[] = '
			<div class="jsn-product-intro jsn-section-content">
				<div class="jsn-product-thumbnail">
					<a href="' . JRoute::_($links['info']) . '" target="_blank"><img src="' . JURI::root(true) . '/administrator/components/' . JFactory::getApplication()->input->getCmd('option') . '/assets/images/product-thumbnail.png" alt="" /></a>
				</div>
				<div class="jsn-product-details">
					<dl>

						<dt>' . JText::_('JSN_EXTFW_GENERAL_AUTHOR') . ':</dt>
						<dd>
							<a href="' . $info->authorUrl . '">' . $info->author . '</a>
						</dd>
						<dt>' . JText::_('JSN_EXTFW_GENERAL_COPYRIGHT') . ':</dt>
						<dd>' . $info->copyright . '</dd>
						<dt>' . JText::_('JSN_EXTFW_GENERAL_VERSION') . ':</dt>
						<dd>
							<strong class="jsn-current-version">' . $version . '</strong>&nbsp;-&nbsp;<span id="jsn-check-version-result">';

			try
			{
				$hasUpdate = false;

				foreach (JSNUpdateHelper::check($products) AS $result)
				{
					if ($result)
					{
						$hasUpdate = true;
						break;
					}
				}

				if ($hasUpdate)
				{
					$html[]	= '<span class="jsn-outdated-version">' . JText::_('JSN_EXTFW_GENERAL_UPDATE_AVAILABLE') . '</span>'
							. '&nbsp;<a href="' . JRoute::_($links['update']) . '" class="label label-success">' . JText::_('JSN_EXTFW_GENERAL_UPDATE_NOW') . '</a>';
				}
				else
				{
					$html[] = '<span class="jsn-latest-version"><span class="label label-success">' . JText::_('JSN_EXTFW_GENERAL_LATEST_VERSION') . '</span></span>';
				}
			}
			catch (Exception $e)
			{
				$html[] = '<span class="label label-important">' . $e->getMessage() . '</span>';
			}

			$html[] = '</span>
						</dd>					</dl>
				</div>
				<div class="clearbreak"></div>
			</div>
			<div class="jsn-product-cta jsn-bgpattern pattern-sidebar">
				<div class="pull-left">
					<ul class="jsn-list-horizontal">';

			if ( ! empty($links['review']))
			{
				$html[] = '
						<li>
							<a href="' . JRoute::_($links['review']) . '" target="_blank" class="btn"><i class="icon-comment"></i>&nbsp;' . JText::_('JSN_EXTFW_ABOUT_REVIEW') . '</a>
						</li>';
			}

			$html[] = '
						<li><a id="jsn-about-promotion-modal" class="btn" href="http://www.joomlashine.com/free-joomla-templates-promo.html"><i class="icon-briefcase"></i>&nbsp;' . JText::_('JSN_EXTFW_ABOUT_SEE_OTHER') . '</a></li>
					</ul>
				</div>
				<div class="pull-right">
					<ul class="jsn-list-horizontal">
						<li>
							<a class="jsn-icon24 jsn-icon-social jsn-icon-facebook" href="http://www.facebook.com/joomlashine" title="' . JText::_('JSN_EXTFW_ABOUT_FB') . '" target="_blank"></a>
						</li>
						<li>
							<a class="jsn-icon24 jsn-icon-social jsn-icon-twitter" href="http://www.twitter.com/joomlashine" title="' . JText::_('JSN_EXTFW_ABOUT_TW') . '" target="_blank"></a>
						</li>
						<li>
							<a class="jsn-icon24 jsn-icon-social jsn-icon-youtube" href="http://www.youtube.com/joomlashine" title="' . JText::_('JSN_EXTFW_ABOUT_YT') . '" target="_blank"></a>
						</li>
					</ul>
				</div>
				<div class="clearbreak"></div>
			</div>
		</div>';

		return implode($html);
	}

	/**
	 * Generate HTML help&support part for about page.
	 *
	 * @param   array  $links  Array of necessary links.
	 *
	 * @return  string
	 */
	public static function aboutHelp($links = array())
	{
		if ( ! $links)
		{
			$links['doc'] = JSNUtilsText::getConstant('DOC_LINK');
		}

		$html[] = '
		<div>
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
			<p>' . JText::_('JSN_EXTFW_ABOUT_ONLY_AVAILABLE') . '</p>
		</div>';

		return implode($html);
	}

	/**
	 * Generate HTML feedback part for about page.
	 *
	 * @param   array  $links  Array of necessary links.
	 *
	 * @return  string
	 */
	public static function aboutFeedback($links = array())
	{
		if ( ! $links)
		{
			$links['review'] = JSNUtilsText::getConstant('REVIEW_LINK');
		}

		$html[] = '
		<div>
			<h3 class="jsn-section-header">' . JText::_('JSN_EXTFW_ABOUT_FEEDBACK') . '</h3>
			<p>' . JText::_('JSN_EXTFW_ABOUT_LIKE_TO_HEAR') . ':</p>
			<ul>
				<li>' . JText::_('JSN_EXTFW_ABOUT_REPORT_BUG') . '</li>
				<li>' . JText::_('JSN_EXTFW_ABOUT_GIVE_TESTIMONIAL') . '</li>';

		if ( ! empty($links['review']))
		{
			$html[] = '
				<li>' . JText::sprintf('JSN_EXTFW_ABOUT_REVIEW_ON_JED', JRoute::_($links['review'])) . '</li>';
		}

		$html[] = '
			</ul>
		</div>';

		return implode($html);
	}

	/**
	 * Generate HTML markup for footer.
	 *
	 * If a product has sub-product, this method need to be called as below to
	 * check all sub-product for latest version:
	 *
	 * <pre>JSNHtmlGenerate::footer(
	 *     array(
	 *         // Core component
	 *         'imageshow' => '4.2.0',
	 *
	 *         // Themes
	 *         'themeclassic' => '1.1.5',
	 *         'themeslider'  => '1.0.4',
	 *         'themegrid'    => '1.0.0',
	 *
	 *         // Sources
	 *         'picasa'      => '1.1.2',
	 *         'flickr'      => '1.1.2',
	 *         'phoca'       => '1.0.1',
	 *         'joomgallery' => '1.0.1',
	 *         'rsgallery2'  => '1.0.1',
	 *         'facebook'    => '1.0.1'
	 *     )
	 * );</pre>
	 *
	 * If a product does not have sub-product, the <b>$products</b> parameter
	 * does not required when calling this method:
	 *
	 * <pre>JSNHtmlGenerate::footer();</pre>
	 *
	 * @param   array    $products  Array of product identified name.
	 * @param   boolean  $echo      Whether to echo the output or not?
	 *
	 * @return  string
	 */
	public static function footer($products = array(), $echo = true)
	{
		// Get extension manifest cache
		$info = JSNUtilsXml::loadManifestCache();

		// Initialize variables
		$name		= $info->name;
		$edition	= JSNUtilsText::getConstant('EDITION');
		$version	= JSNUtilsText::getConstant('VERSION');

		// Initialize links
		$links['info']		= JSNUtilsText::getConstant('INFO_LINK');
		$links['doc']		= JSNUtilsText::getConstant('DOC_LINK');
		$links['review']	= JSNUtilsText::getConstant('REVIEW_LINK');
		$links['update']	= JSNUtilsText::getConstant('UPDATE_LINK');
		$links['upgrade']	= JSNUtilsText::getConstant('UPGRADE_LINK');

		// Generate markup
		$html[] = '
<div id="jsn-footer" class="jsn-page-footer jsn-bootstrap">
<div class="pull-left">
<ul class="jsn-footer-menu">
	<li class="first">';

		if ( ! empty($links['doc']))
		{
			$html[] = '
		<a href="' . JRoute::_($links['doc']) . '" target="_blank">' . JText::_('JSN_EXTFW_GENERAL_DOCUMENTATION') . '</a>
	</li>
	<li>';
		}

		$html[] = '
		<a href="http://www.joomlashine.com/contact-us/get-support.html" target="_blank">' . JText::_('JSN_EXTFW_GENERAL_SUPPORT') . '</a>
	</li>';

		if ( ! empty($links['review']))
		{
			$html[] = '
	<li>
		<a href="' . JRoute::_($links['review']) . '" target="_blank">' . JText::_('JSN_EXTFW_GENERAL_VOTE') . '</a>
	</li>';
		}

		$html[] = '
	<li class="jsn-iconbar">
		<strong>' . JText::_('JSN_EXTFW_GENERAL_KEEP_IN_TOUCH') . ':</strong>
		<a title="' . JText::_('JSN_EXTFW_GENERAL_FACEBOOK') . '" target="_blank" href="http://www.facebook.com/joomlashine"><i class="jsn-icon16 jsn-icon-social jsn-icon-facebook"></i></a><a title="' . JText::_('JSN_EXTFW_GENERAL_TWITTER') . '" target="_blank" href="http://www.twitter.com/joomlashine"><i class="jsn-icon16 jsn-icon-social jsn-icon-twitter""></i></a><a title="' . JText::_('JSN_EXTFW_GENERAL_YOUTUBE') . '" target="_blank" href="http://www.youtube.com/joomlashine"><i class="jsn-icon16 jsn-icon-social jsn-icon-youtube""></i></a>
	</li>
</ul>
<ul class="jsn-footer-menu">
	<li class="first">';

		if ( ! empty($links['info']))
		{
			$html[] = '
		<a href="' . JRoute::_($links['info']) . '" target="_blank">JSN ' . preg_replace('/JSN\s*/i', '', JText::_($name)) . ' ' . $edition . ' v' . $version . '</a>';
		}
		else
		{
			$html[] = 'JSN ' . preg_replace('/JSN\s*/i', '', JText::_($name)) . ' ' . $edition . ' v' . $version;
		}

		$html[] = ' by <a href="http://www.joomlashine.com" target="_blank">JoomlaShine.com</a>';

		if ( ! empty($edition) AND ! empty($links['upgrade']) AND ($pos = strpos('free + pro standard', strtolower($edition))) !== false)
		{
			$html[] = '
		&nbsp;<a class="label label-important" href="' . JRoute::_($links['upgrade']) . '"><strong class="jsn-text-attention">' . JText::_($pos ? 'JSN_EXTFW_GENERAL_UPGRADE_TO_PRO_UNLIMITED' : 'JSN_EXTFW_GENERAL_UPGRADE_TO_PRO') . '</strong></a>';
		}

		$html[] = '
	</li>';

		try
		{
			$hasUpdate = false;

			foreach (JSNUpdateHelper::check($products) AS $result)
			{
				if ($result)
				{
					$hasUpdate = true;
					break;
				}
			}

			if ($hasUpdate)
			{
				$html[] = '
	<li id="jsn-global-check-version-result" class="jsn-outdated-version">
		<span class="jsn-global-outdated-version">' . JText::_('JSN_EXTFW_GENERAL_UPDATE_AVAILABLE') . '</span>
		&nbsp;<a href="' . JRoute::_($links['update']) . '" class="label label-important">' . JText::_('JSN_EXTFW_GENERAL_UPDATE_NOW') . '</a>
	</li>';
			}
		}
		catch (Exception $e)
		{
			// Simply ignore
		}

		$html[] = '
</ul>
</div>
<div class="pull-right">
<ul class="jsn-footer-menu">
	<li class="jsn-iconbar first">
		<a title="' . JText::_('JSN_EXTFW_GENERAL_POWERADMIN') . '" target="_blank" href="http://www.joomlashine.com/joomla-extensions/jsn-poweradmin.html">
			<i class="jsn-icon32 jsn-icon-products jsn-icon-poweradmin"></i>
		</a>
		<a title="' . JText::_('JSN_EXTFW_GENERAL_IMAGESHOW') . '" target="_blank" href="http://www.joomlashine.com/joomla-extensions/jsn-imageshow.html">
			<i class="jsn-icon32 jsn-icon-products jsn-icon-imageshow""></i>
		</a>
		<a title="' . JText::_('JSN_EXTFW_GENERAL_UNIFORM') . '" target="_blank" href="http://www.joomlashine.com/joomla-extensions/jsn-uniform.html">
			<i class="jsn-icon32 jsn-icon-products jsn-icon-uniform""></i>
		</a>
		<a title="' . JText::_('JSN_EXTFW_GENERAL_MOBILIZE') . '" target="_blank" href="http://www.joomlashine.com/joomla-extensions/jsn-mobilize-joomla-mobile-extension.html">
			<i class="jsn-icon32 jsn-icon-products jsn-icon-mobilize""></i>
		</a>
	</li>
</ul>
</div>
<div class="clearbreak"></div>
</div>
';

		if ($echo)
		{
			echo implode($html);
		}
		else
		{
			return implode($html);
		}
	}
/**
	 * Generate HTML markup for menu tool bar.
	 *
	 * @param   array   $options   Options menu tool bar
	 *
	 * @param   String  $titleMenu  Title Menu
	 *
	 * @param   String  $iconMenu   Icon Menu
	 *
	 * @param   String  $btnStyle   button Style
	 *
	 * @return  html code
	 */
	public static function menuToolbar($options = array(),$titleMenu = NULL,$iconMenu = NULL,$btnStyle = NULL)
	{
		$html = '';
		$menuButtonText = !empty($titleMenu) ? $titleMenu : JText::_('JSN_EXTFW_GENERAL_MENU');
		$itemHtml = '';
		JSNHtmlAsset::loadScript('jsn/menutoolbar');

		if (is_array($options) AND count($options) > 0)
		{
			foreach ($options as $index => $item)
			{
				$class = isset($item['class']) ? $item['class'] : "";
				$class = ($index == 0) ? $class . " first" : $class;
				$class = ($index == count($options)) ? $class . " first" : $class;
				$icon = isset($item['icon']) ? "<span class=\"jsn-icon24 {$item['icon']}\"></span>" : "";
				$title = isset($item['title']) ? $item['title'] : "";
				$menuLink = empty($item['link']) ? $title : "<a href=\"{$item['link']}\">{$icon}{$title}</a>";
				$itemSublink = "";
				$subMenu = "";

				if (isset($item['data_sub_menu']))
				{
					$subMenuFieldTitle = isset($item['sub_menu_field_title']) ? $item['sub_menu_field_title'] : "";

					if (is_array($item['data_sub_menu']))
					{
						foreach ($item['data_sub_menu'] as $dataSubMenu)
						{
							if (empty($item['sub_menu_link']))
							{
								$itemSublink .= "<li>{$subMenuFieldTitle}</li>";
							}
							else
							{
								$subLink = $item['sub_menu_link'];

								if (preg_match_all('/\{\$([^\}]+)\}/', $subLink, $matches, PREG_SET_ORDER))
								{
									foreach ($matches AS $match)
									{
										$subLink = str_replace($match[0], @$dataSubMenu -> {$match[1]}, $subLink);
									}
								}

								$itemSublink .= "<li><a href=\"{$subLink}\">{$dataSubMenu -> $subMenuFieldTitle}</a></li>";
							}
						}
					}

					$subMenu = empty($itemSublink) ? "" : $itemSublink . '<li class=\"separator\"></li>';
					$subLinkAddTitle = isset($item['sub_menu_link_add_title']) ? $item['sub_menu_link_add_title'] : "";
					$subLinkAdd = empty($item['sub_menu_link_add']) ? $subLinkAddTitle : "<a href=\"{$item['sub_menu_link_add']}\" title=\"{$subLinkAddTitle}\"><span class=\"jsn-icon16 jsn-icon-plus\"></span>{$subLinkAddTitle}</a>";
					$subMenu = "<ul class=\"jsn-list-items\">{$subMenu}<li class=\"primary\">{$subLinkAdd}</li></ul>";
				}
				$itemHtml .= "<li class=\"{$class}\">{$menuLink}{$subMenu}</li>";
			}
		}
		$iconBtnMenu = !empty($iconMenu)?$iconMenu:'icon-list-view';
		$buttonStyle = !empty($btnStyle)?$btnStyle:'btn btn-small';
		$html = "<ul class=\"jsn-menu\"><li class=\"menu-name\"><button class=\"{$buttonStyle}\"><i class=\"{$iconBtnMenu}\" title=\"{$menuButtonText}\"></i>{$menuButtonText}</button><ul class=\"jsn-submenu\">{$itemHtml}</ul></li></ul>";

		return $html;

	}
}
