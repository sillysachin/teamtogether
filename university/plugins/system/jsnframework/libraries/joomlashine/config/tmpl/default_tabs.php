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
?>
<div class="jsn-tabs">
	<ul>
<?php
foreach ($tabs AS $tab)
{
?>
		<li>
			<a href="#<?php echo $tab->name; ?>">
				<?php echo JText::_($tab->label); ?></a>
		</li>
<?php
}
?>
	</ul>
<?php
foreach ($tabs AS $tab)
{
?>
	<div id="<?php echo $tab->name; ?>" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
<?php
	$fieldsets = & $tab->fieldsets;
	require dirname(__FILE__) . '/' . str_replace('_tabs.php', '_form.php', basename(__FILE__));
?>
	</div>
<?php
}
?>
</div>
