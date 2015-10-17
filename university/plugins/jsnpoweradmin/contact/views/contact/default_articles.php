<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_content/helpers/route.php';

?>

<?php if (count($item->articles)) :?>
<?php $showArticles = $params->get('show_articles') ? 'display-default display-item' : 'hide-item'; ?>
<div parname="show_articles" id="show_articles" class="contact-articles element-switch contextmenu-approved <?php echo $showArticles;?>" >
	<ol>
		<?php foreach ($item->articles as $article) :	?>
			<li>
				<a href="javascript:void(0)" ><?php echo $article->title ?></a>
			</li>
		<?php endforeach; ?>
	</ol>
</div>
<?php endif;?>

