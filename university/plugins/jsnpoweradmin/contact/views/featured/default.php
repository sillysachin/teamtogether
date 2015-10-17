<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// If the page class is defined, add to class as suffix.
// It will be a separate class if the user starts it with a space
$params	= $data->params;
$items 	= $data->items;
$pagination = $data->pagination;
?>
<div class="jsn-article-layout">
<?php if ($params->get('show_page_heading')!=0 ): ?>
	<h1>
	<?php echo $params->get('page_heading'); ?>
	</h1>
<?php endif; ?>

<?php include JPATH_ROOT . '/plugins/jsnpoweradmin/contact/views/featured/default_items.php';?>
<?php if ($params->def('show_pagination', 2) == 1  || ($params->get('show_pagination') == 2 && $pagination->get('pages.total') > 1)) : ?>
	<div class="pagination">

		<?php if ($params->def('show_pagination_results', 1)) : ?>
			<p class="counter">
				<?php echo $pagination->getPagesCounter(); ?>
			</p>
		<?php  endif; ?>
				<?php echo $pagination->getPagesLinks(); ?>
	</div>
<?php endif; ?>
</div>
