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
 * Helper class for text manipulation.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNUtilsText
{
	/**
	 * Get constant value.
	 *
	 * @param   string  $name       Raw constant name.
	 * @param   string  $component  Component folder name.
	 *
	 * @return  mixed  Constant value or null if constant is not defined.
	 */
	public static function getConstant($name, $component = '')
	{
		// Initialize component
		! empty($component) OR $component = JFactory::getApplication()->input->getCmd('option');
		$component = preg_replace('/^com_/i', '', $component);

		// Generate constant name
		$const = strtoupper("jsn_{$component}_{$name}");

		// Get constant value
		if (defined($const))
		{
			eval('$const = ' . $const . ';');
		}
		else
		{
			$const = null;
		}

		return $const;
	}

	/**
	 * Truncate text to given number of word.
	 *
	 * This method keeps HTML code structure while truncation. For example, the
	 * following text:
	 *
	 * <code>&lt;div class="message"&gt;
	 *     &lt;blockquote class="testimonial"&gt;
	 *         &lt;dl&gt;
	 *             &lt;dt&gt;John says:&lt;/dt&gt;
	 *             &lt;dd&gt;
	 * Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula...
	 *             &lt;/dd&gt;
	 *         &lt;/dl&gt;
	 *     &lt;/blockquote&gt;
	 * &lt;/div&gt;</code>
	 *
	 * is the truncated result of:
	 *
	 * <code>&lt;div class="message"&gt;
	 *     &lt;blockquote class="testimonial"&gt;
	 *         &lt;dl&gt;
	 *             &lt;dt&gt;John says:&lt;/dt&gt;
	 *             &lt;dd&gt;
	 * Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.
	 *             &lt;/dd&gt;
	 *         &lt;/dl&gt;
	 *     &lt;/blockquote&gt;
	 * &lt;/div&gt;</code>
	 *
	 * @param   string   $text      Text to be truncated.
	 * @param   integer  $limit     Word limitation.
	 * @param   boolean  $cleanTag  Whether to clean HTML markup tag from truncated text or not?
	 *
	 * @return  string
	 */
	public static function getWords($text, $limit = 25, $cleanTag = true)
	{
		return self::truncate($text, $limit, $cleanTag);
	}

	/**
	 * Truncate text to given number of character or word.
	 *
	 * This method keeps HTML code structure while truncation. For example, the
	 * following text:
	 *
	 * <code>&lt;div class="message"&gt;
	 *     &lt;blockquote class="testimonial"&gt;
	 *         &lt;dl&gt;
	 *             &lt;dt&gt;John says:&lt;/dt&gt;
	 *             &lt;dd&gt;
	 * Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula...
	 *             &lt;/dd&gt;
	 *         &lt;/dl&gt;
	 *     &lt;/blockquote&gt;
	 * &lt;/div&gt;</code>
	 *
	 * is the truncated result of:
	 *
	 * <code>&lt;div class="message"&gt;
	 *     &lt;blockquote class="testimonial"&gt;
	 *         &lt;dl&gt;
	 *             &lt;dt&gt;John says:&lt;/dt&gt;
	 *             &lt;dd&gt;
	 * Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.
	 *             &lt;/dd&gt;
	 *         &lt;/dl&gt;
	 *     &lt;/blockquote&gt;
	 * &lt;/div&gt;</code>
	 *
	 * @param   string   $text      Text to be truncated.
	 * @param   string   $limit     Character or word limitation, e.g. 100c for 100 character limitation, or 10w for 10 word limitation.
	 * @param   boolean  $cleanTag  Whether to clean HTML markup tag from truncated text or not?
	 *
	 * @return  string
	 */
	public static function truncate($text, $limit = '25w', $cleanTag = false)
	{
		// Initialize text truncation value
		$unit	= in_array($unit = substr($limit, -1), array('c', 'w')) ? $unit : 'w';
		$limit	= (int) $limit;

		// Get all words
		$words	= preg_split('/[\s\t\n]+/u', str_replace('><', ">\n<", $text));
		$max	= count($words);

		if (($unit == 'w' AND $max > $limit) OR ($unit == 'c' AND strlen($text) > $limit))
		{
			// Preset some variables
			$openTag	= array();
			$text		= '';
			$counting	= 0;
			$i			= 0;

			while ($i < $max AND $counting < $limit)
			{
				if ( ! empty($words[$i]))
				{
					// Append word
					if ( ! empty($text))
					{
						$text .= ' ' . $words[$i];

						// Count white-space also if truncate by character
						$unit != 'c' OR $counting++;
					}
					else
					{
						$text = $words[$i];
					}

					if (preg_match('#^.*</[^>]+>.*$#', $words[$i]))
					{
						// Found close tag, e.g. </b>, </i>, </strong>, </em>
						array_pop($openTag);

						// Increase words count also if the close tag is prefixed or suffixed with a word
						$pre = strpos($words[$i], '<');
						$suf = strpos($words[$i], '>');
						$end = strlen($words[$i]) - 1;

						if ($pre > 0 OR $suf < $end)
						{
							$unit == 'w'
								? $counting++
								: $counting += $pre + ($end - $suf);
						}
					}
					elseif (preg_match('/^.*<[^>]+>.*$/', $words[$i]))
					{
						// Found a single word open tag, e.g. <b>, <i>, <strong>, <em>
						$openTag[] = $words[$i];

						// Check if this tag is a self-closed tag, e.g. <br/>
						if (preg_match('#^.*<((?!/>).)*/>.*$#', $words[$i]))
						{
							array_pop($openTag);
						}

						// Increase words count also if the open / self-closed tag is prefixed or suffixed with a word
						$pre = strpos($words[$i], '<');
						$suf = strpos($words[$i], '>');
						$end = strlen($words[$i]) - 1;

						if ($pre > 0 OR $suf < $end)
						{
							$unit == 'w'
								? $counting++
								: $counting += $pre + ($end - $suf);
						}
					}
					elseif (preg_match('/^.*<(script|style|object)$/', $words[$i], $match))
					{
						// Increase words count if the special tag is prefixed with a word
						$pre = strpos($words[$i], '<');

						if ($pre > 0)
						{
							$unit == 'w'
								? $counting++
								: $counting += $pre;
						}

						// Get all remaining parts of the special tag
						do
						{
							$i++;
							$text .= ' ' . $words[$i];
						}
						while ( ! preg_match('#^.*</' . $match[1] . '>.*$#', $words[$i]));

						// Increase words count if the final part of the special tag is suffixed with a word
						$suf = strpos($words[$i], '>');
						$end = strlen($words[$i]) - 1;

						if ($suf < $end)
						{
							$unit == 'w'
								? $counting++
								: $counting += ($end - $suf);
						}
					}
					elseif (preg_match('#^.*<((?!/>).)*$#', $words[$i]))
					{
						// Found starting part of multi-words open tag, e.g. <a, <table
						$openTag[] = $words[$i];

						// Increase words count also if the open tag is prefixed with a word
						$pre = strpos($words[$i], '<');

						if ($pre > 0)
						{
							$unit == 'w'
								? $counting++
								: $counting += $pre;
						}

						// Get all remaining parts of the tag
						do
						{
							$i++;
							$text .= ' ' . $words[$i];
						}
						while ( ! preg_match('/^.*>.*$/', $words[$i]));

						// Increase words count if the final part of the tag is suffixed with a word
						$suf = strpos($words[$i], '>');
						$end = strlen($words[$i]) - 1;

						if ($suf < $end)
						{
							$unit == 'w'
								? $counting++
								: $counting += ($end - $suf);
						}

						// Check if this tag is a self-closed tag or the final part of the tag also contains close tag
						if (preg_match('#^.*/>.*$#', $words[$i]) OR preg_match('#^.*</[^>]+>.*$#', $words[$i]))
						{
							array_pop($openTag);
						}
					}
					else
					{
						// Not a tag, increase words count
						$unit == 'w'
							? $counting++
							: $counting += strlen($words[$i]);
					}

					// Fine tune character-based truncation
					if ($unit == 'c' AND $counting > $limit)
					{
						$words	= explode(' ', $text);
						$last	= array_pop($words);
						$tag	= ($pos = strpos($last, '<')) > 0 ? substr($last, $pos) : '';
						$last	= substr(empty($tag) ? $last : substr($last, 0, $pos), 0, $limit - $counting);
						$text	= implode(' ', $words) . " {$last}{$tag}";

						// Break the loop immediately
						break;
					}
				}

				// Increase words array index
				$i++;
			}

			// Finalize the truncated text
			$i >= $max OR $text = preg_replace('#(.)(</?[^>]+>)*$#', '\1...\2', $text);

			if ( ! $cleanTag AND count($openTag))
			{
				// The truncated text has tag(s) that is/are not closed, close now
				for ($i = count($openTag) - 1; $i >= 0; $i--)
				{
					$text .= '</' . preg_replace('/(.*<)|(>.*)/', '', $openTag[$i]) . '>';
				}
			}
		}

		// Clean all markup tag if necessary
		if ($cleanTag)
		{
			$text = preg_replace(array('#<[a-z0-9]+[^>]*/?>#i', '#</[a-z0-9]+>#i'), '', $text);
		}

		return $text;
	}
}
