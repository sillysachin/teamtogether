<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
?>
<ul class="menu_types">
    <?php foreach($this->types as $name=>$list): ?>
        <li>
    	<dl class="menu_type jsn-listmenu">
    	    <dt>
		<?php
		if(isset($_GET['multiselect']) && $_GET['multiselect'] == "true")
		{
		    echo '<input type="checkbox" class="jsn-checkall" />';
		}
		echo JText::_($name);
		?>
    	    </dt>
    	    <dd>
    		<ul>
			<?php foreach($list as $item): ?>
			    <li>
				<?php
				$layout    = (isset($item->request['layout'])) ? $item->request['layout'] : 'default';
				$layout    = (isset($item->request['task'])) ? $item->request['task'] : $layout;
				$option    = isset($item->request['option']) ? $item->request['option'] : '';
				$view      = isset($item->request['view']) ? $item->request['view'] : 'default';
				$title     = isset($item->title) ? trim(JText::_($item->title)) : '';
				$dataValue = array(
				    'option'=>$option,
				    'view'  =>$view,
				    'layout'=>$layout,
				    'title' =>$title
				);
				$onclick = '';
				$function	= JFactory::getApplication()->input->getCmd('function', 'jQuery.jSelectMenuTypes');
				if(isset($_GET['multiselect']) && $_GET['multiselect'] == "true")
				{
				    echo '<input class="jsn-menutypes" type="checkbox" value=\'' . json_encode($dataValue) . '\'/>';
				}
				else
				{
				    $onclick = 'onclick="if (window.parent && window.parent.' . $this->escape($function) . ') window.parent.' . $this->escape($function) . '(\'' . $this->escape($title) . '\',\'' . $this->escape($option) . '\', \'' . $this->escape($view) . '\',  \'' . $this->escape($layout) . '\');"';
				}
				?>
				<a class="choose_type" <?php echo $onclick; ?> href="#" title="<?php echo JText::_($item->description); ?>">
				    <?php echo JText::_($item->title); ?>
				</a>
			    </li>
			<?php endforeach; ?>
    		</ul>
    	    </dd>
    	</dl>
        </li>
    <?php endforeach; ?>
</ul>
