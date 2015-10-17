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
 * Helper class for working with messages.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNUtilsMessage
{
	/**
	 * Get message list.
	 *
	 * @param   string   $screen  Message screen.
	 * @param   boolean  $all     Query for both published and unpublished messages or published only.
	 *
	 * @return  array	Array of database query result objects.
	 */
	public static function getList($screen = '', $all = false)
	{
		// Build query conditions
		$all           OR $where[] = "published = 1";
		empty($screen) OR $where[] = "`msg_screen` = '{$screen}'";

		// Get name of messages table
		$table = '#__jsn_' . preg_replace('/^com_/i', '', JFactory::getApplication()->input->getCmd('option')) . '_messages';

		// Get message list
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($table);
		$query->where(isset($where) ? implode(' AND ', $where) : '1');
		$query->order(array('msg_screen', 'ordering'));

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Generate markup for message configuration.
	 *
	 * @param   array  $msgs  Queried messages.
	 *
	 * @return  string
	 */
	public static function showConfig($msgs = array())
	{
		// Parse configuration declaration
		$xml = JSNUtilsXml::load(JPATH_COMPONENT_ADMINISTRATOR . '/config.xml');

		$html[] = '
	<table border="0" class="table table-bordered">
		<thead>
			<tr>
				<th width="20" class="center">#</th>
				<th class="center">' . JText::_('JSN_EXTFW_MESSAGE') . '</th>
				<th width="150" nowrap="nowrap" class="center">' . JText::_('JSN_EXTFW_MESSAGE_SCREEN') . '</th>
				<th width="80" nowrap="nowrap" class="center">' . JText::_('JSN_EXTFW_MESSAGE_ORDER') . '</th>
				<th width="80" nowrap="nowrap" class="center">' . JText::_('JSN_EXTFW_MESSAGE_SHOW') . '</th>
			</tr>
		</thead>
		<tbody>';

		for ($i = 0, $n = count($msgs); $i < $n; $i++)
		{
			$msg = & $msgs[$i];
			$scr = $xml->xpath('//field[@type="messagelist"]/option[@value="' . $msg->msg_screen . '"]');

			$html[] = '
			<tr>
				<td class="center">' . ($i + 1) . '</td>
				<td><span title="::' . htmlspecialchars(strip_tags(JText::_('MESSAGE_' . $msg->msg_screen . '_' . $msg->ordering . '_PRIMARY'))) . '" class="editlinktip hasTip">' . JSNUtilsText::getWords(strip_tags(JText::_('MESSAGE_' . $msg->msg_screen . '_' . $msg->ordering . '_PRIMARY')), 15) . '</span></td>
				<td class="center">' . JText::_((string) $scr[0]) . '</td>
				<td class="center">' . $msg->ordering . '</td>
				<td class="center"><input type="checkbox"' . ($msg->published ? ' checked="checked"' : '') . ' value="' . $msg->msg_id . '" name="messages[]" /></td>
			</tr>';

			if ($msg->published)
			{
				$html[] = '<input type="hidden" name="messagelist[]" value="' . $msg->msg_id . '" />';
			}
		}

		$html[] = '
		</tbody>
	</table>
';

		return implode($html);
	}

	/**
	 * Update message configuration.
	 *
	 * @param   array  $before  Original message list.
	 * @param   array  $after   Updated message list.
	 *
	 * @return  void
	 */
	public static function saveConfig($before, $after)
	{
		// Get name of messages table
		$table = '#__jsn_' . preg_replace('/^com_/i', '', JFactory::getApplication()->input->getCmd('option')) . '_messages';

		// Get database object
		$db = JFactory::getDbo();

		// Get disabled messages
		$disabled = array_diff($before, $after);

		if (count($disabled))
		{
			// Disable selected messages
			$query = $db->getQuery(true);

			$query->update($table);
			$query->set('published = 0');
			$query->where('msg_id IN (' . implode(', ', $disabled) . ')');

			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		// Get enabled messages
		$enabled = array_diff($after, $before);

		if (count($enabled))
		{
			// Enable selected messages
			$query = $db->getQuery(true);

			$query->update($table);
			$query->set('published = 1');
			$query->where('msg_id IN (' . implode(', ', $enabled) . ')');

			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
	}

	/**
	 * Show messages.
	 *
	 * @param   array  $msgs  Messages to show.
	 *
	 * @return  string
	 */
	public static function showMessages($msgs)
	{
		$html = array();

		// Add asset
		$html[] = JSNHtmlAsset::loadScript('jsn/message', array('option' => JFactory::getApplication()->input->getCmd('option')), true);

		// Generate markup
		$html[] = '
<div class="jsn-bootstrap">';

		foreach ($msgs AS $msg)
		{
			// Initialize variables
			if (is_object($msg))
			{
				$message = JText::_('MESSAGE_' . $msg->msg_screen . '_' . $msg->ordering . '_PRIMARY');
			}
			else
			{
				$onclick = '';
				$message = JText::_($msg);
			}

			$html[] = '
	<div class="alert alert-block fade in">
		<a href="javascript:void(0);" title="' . JText::_('JSN_EXTFW_GENERAL_CLOSE') . '" class="jsn-close-message close" data-message-id="' . $msg->msg_id . '" data-dismiss="alert">×</a>
		' . $message . '
	</div>';
		}

		$html[] = '
</div>
';

		return implode($html);
	}

	/**
	 * Hide and disable message.
	 *
	 * @param   integer  $msg_id  Message ID.
	 *
	 * @return  boolean
	 */
	public static function hideMessage($msg_id)
	{
		// Get name of messages table
		$table = '#__jsn_' . preg_replace('/^com_/i', '', JFactory::getApplication()->input->getCmd('option')) . '_messages';

		// Disable message
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->update($table);
		$query->set('published = 0');
		$query->where("msg_id = {$msg_id}");

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
}
