<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: menus.php 13902 2012-07-11 10:34:39Z thangbh $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_menus/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$lang = JFactory::getLanguage();
$this->ordering = array();
// Preprocess the list of items to find ordering divisions.
foreach ($this->items as $item) {
	$this->ordering[$item->parent_id][] = $item->id;

	// item type text
	switch ($item->type) {
		case 'url':
			$value = JText::_('COM_MENUS_TYPE_EXTERNAL_URL');
			break;

		case 'alias':
			$value = JText::_('COM_MENUS_TYPE_ALIAS');
			break;

		case 'separator':
			$value = JText::_('COM_MENUS_TYPE_SEPARATOR');
			break;

		case 'component':
		default:
			// load language
				$lang->load($item->componentname.'.sys', JPATH_ADMINISTRATOR, null, false, false)
			||	$lang->load($item->componentname.'.sys', JPATH_ADMINISTRATOR.'/components/'.$item->componentname, null, false, false)
			||	$lang->load($item->componentname.'.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			||	$lang->load($item->componentname.'.sys', JPATH_ADMINISTRATOR.'/components/'.$item->componentname, $lang->getDefault(), false, false);

			if (!empty($item->componentname)) {
				$value	= JText::_($item->componentname);
				$vars	= null;

				parse_str($item->link, $vars);
				if (isset($vars['view'])) {
					// Attempt to load the view xml file.
					$file = JPATH_SITE.'/components/'.$item->componentname.'/views/'.$vars['view'].'/metadata.xml';
					if (JFile::exists($file) && $xml = simplexml_load_file($file)) {
						// Look for the first view node off of the root node.
						if ($view = $xml->xpath('view[1]')) {
							if (!empty($view[0]['title'])) {
								$vars['layout'] = isset($vars['layout']) ? $vars['layout'] : 'default';

								// Attempt to load the layout xml file.
								// If Alternative Menu Item, get template folder for layout file
								if (strpos($vars['layout'], ':') > 0)
								{
									// Use template folder for layout file
									$temp = explode(':', $vars['layout']);
									$file = JPATH_SITE.'/templates/'.$temp[0].'/html/'.$item->componentname.'/'.$vars['view'].'/'.$temp[1].'.xml';
									// Load template language file
									$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE, null, false, false)
									||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE.'/templates/'.$temp[0], null, false, false)
									||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE, $lang->getDefault(), false, false)
									||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE.'/templates/'.$temp[0], $lang->getDefault(), false, false);

								}
								else
								{
									// Get XML file from component folder for standard layouts
									$file = JPATH_SITE.'/components/'.$item->componentname.'/views/'.$vars['view'].'/tmpl/'.$vars['layout'].'.xml';
								}
								if (JFile::exists($file) && $xml = simplexml_load_file($file)) {
									// Look for the first view node off of the root node.
									if ($layout = $xml->xpath('layout[1]')) {
										if (!empty($layout[0]['title'])) {
											$value .= ' » ' . JText::_(trim((string) $layout[0]['title']));
										}
									}
									if (!empty($layout[0]->message[0])) {
										$item->item_type_desc = JText::_(trim((string) $layout[0]->message[0]));
									}
								}
							}
						}
						unset($xml);
					}
					else {
						// Special case for absent views
						$value .= ' » ' . JText::_($item->componentname.'_'.$vars['view'].'_VIEW_DEFAULT_TITLE');
					}
				}
			}
			else {
				if (preg_match("/^index.php\?option=([a-zA-Z\-0-9_]*)/", $item->link, $result)) {
					$value = JText::sprintf('COM_MENUS_TYPE_UNEXISTING',$result[1]);
				}
				else {
					$value = JText::_('COM_MENUS_TYPE_UNKNOWN');
				}
			}
			break;
	}
	$item->item_type = $value;
}

$user		= JFactory::getUser();
$app		= JFactory::getApplication();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$ordering 	= ($listOrder == 'a.lft');
$canOrder	= $user->authorise('core.edit.state',	'com_menus');
$saveOrder 	= ($listOrder == 'a.lft' && $listDirn == 'asc');
?>
<?php //Set up the filter bar. ?>
<form action="<?php echo JRoute::_('index.php?option=com_poweradmin&view=search');?>" method="post" name="adminForm" id="adminForm">
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('JGRID_HEADING_MENU_ITEM_TYPE'); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_MENUS_HEADING_HOME', 'a.home', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<div class="pull-left" >
						<?php echo $this->pagination->getListFooter(); ?>
					</div>
					<div class="btn-group  pull-left jsn-limitbox" >
						<?php echo $this->pagination->getLimitBox(); ?>
					</div>
				</td>
			</tr>
		</tfoot>
		<?php // Grid layout ?>
		<tbody>
		<?php
		$originalOrders = array();
		foreach ($this->items as $i => $item) :
			$orderkey = array_search($item->id, $this->ordering[$item->parent_id]);
			$canCreate	= $user->authorise('core.create',		'com_menus');
			$canEdit	= $user->authorise('core.edit',			'com_menus');
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out==$user->get('id')|| $item->checked_out==0;
			$canChange	= $user->authorise('core.edit.state',	'com_menus') && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level-1) ?>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'items.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_menus&task=item.edit&id='.(int) $item->id);?>">
							<?php echo $this->escape($item->title); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					<p class="smallsub" title="<?php echo $this->escape($item->path);?>">
						<?php echo str_repeat('<span class="gtr">|&mdash;</span>', $item->level-1) ?>
						<?php if ($item->type !='url') : ?>
							<?php if (empty($item->note)) : ?>
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
							<?php else : ?>
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note));?>
							<?php endif; ?>
						<?php elseif($item->type =='url' && $item->note) : ?>
							<?php echo JText::sprintf('JGLOBAL_LIST_NOTE', $this->escape($item->note));?>
						<?php endif; ?>

						<?php echo $item->menutype ?>
					</p>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'items.', $canChange);?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="nowrap">
					<span title="<?php echo isset($item->item_type_desc) ? htmlspecialchars($this->escape($item->item_type_desc), ENT_COMPAT, 'UTF-8') : ''; ?>">
						<?php echo $this->escape($item->item_type); ?></span>
				</td>
				<td class="center">
					<?php if ($item->type == 'component') : ?>
						<?php if ($item->language=='*' || $item->home=='0'):?>
							<?php echo JHtml::_('jgrid.isdefault', $item->home, $i, 'items.', ($item->language != '*' || !$item->home) && $canChange);?>
						<?php elseif ($canChange):?>
							<a href="<?php echo JRoute::_('index.php?option=com_menus&task=items.unsetDefault&cid[]='.$item->id.'&'.JUtility::getToken().'=1');?>">
								<?php echo JHtml::_('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title'=>JText::sprintf('COM_MENUS_GRID_UNSET_LANGUAGE', $item->language_title)), true);?>
							</a>
						<?php else:?>
							<?php echo JHtml::_('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title'=>$item->language_title), true);?>
						<?php endif;?>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php if ($item->language==''):?>
						<?php echo JText::_('JDEFAULT'); ?>
					<?php elseif ($item->language=='*'):?>
						<?php echo JText::alt('JALL','language'); ?>
					<?php else:?>
						<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
					<?php endif;?>
				</td>
				<td class="center">
					<span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt);?>">
						<?php echo (int) $item->id; ?></span>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
