<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPL
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JSNFont field
 *
 * @package     JSNTPL
 * @subpackage  Form
 * @since       2.0.0
 */
class JFormFieldJSNFont extends JSNTplFormField
{
	public $type = 'JFormFieldJSNFont';

	/**
	 * Font style options.
	 *
	 * @var  array
	 */
	protected $options = array();

	/**
	 * Standard font declaration.
	 *
	 * @var  array
	 */
	protected $standard = array(
		"Verdana, Geneva, sans-serif",
		"Georgia, 'Times New Roman', Times, serif",
		"'Times New Roman', Times, serif",
		"'Courier New', Courier, monospace",
		"Arial, Helvetica, sans-serif",
		"Tahoma, Geneva, sans-serif",
		"'Trebuchet MS', Arial, Helvetica, sans-serif",
		"'Palatino Linotype', 'Book Antiqua', Palatino, serif",
		"'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
		"'Lucida Console', Monaco, monospace"
	);

	/**
	 * Google font declaration.
	 *
	 * @var  array
	 */
	protected $google = array(
		'Open Sans',
		'Oswald',
		'Droid Sans',
		'Lato',
		'Open Sans Condensed',
		'PT Sans',
		'Ubuntu',
		'PT Sans Narrow',
		'Yanone Kaffeesatz',
		'Roboto Condensed',
		'Source Sans Pro',
		'Nunito',
		'Francois One',
		'Roboto',
		'Raleway',
		'Arimo',
		'Cuprum',
		'Play',
		'Dosis',
		'Abel',
		'Droid Serif',
		'Arvo',
		'Lora',
		'Rokkitt',
		'PT Serif',
		'Bitter',
		'Merriweather',
		'Vollkorn',
		'Cantata One',
		'Kreon',
		'Josefin Slab',
		'Playfair Display',
		'Bree Serif',
		'Crimson Text',
		'Old Standard TT',
		'Sanchez',
		'Crete Round',
		'Cardo',
		'Noticia Text',
		'Judson',
		'Lobster',
		'Unkempt',
		'Changa One',
		'Special Elite',
		'Chewy',
		'Comfortaa',
		'Boogaloo',
		'Fredoka One',
		'Luckiest Guy',
		'Cherry Cream Soda',
		'Lobster Two',
		'Righteous',
		'Squada One',
		'Black Ops One',
		'Happy Monkey',
		'Passion One',
		'Nova Square',
		'Metamorphous',
		'Poiret One',
		'Bevan',
		'Shadows Into Light',
		'The Girl Next Door',
		'Coming Soon',
		'Dancing Script',
		'Pacifico',
		'Crafty Girls',
		'Calligraffitti',
		'Rock Salt',
		'Amatic SC',
		'Leckerli One',
		'Tangerine',
		'Reenie Beanie',
		'Satisfy',
		'Gloria Hallelujah',
		'Permanent Marker',
		'Covered By Your Grace',
		'Walter Turncoat',
		'Patrick Hand',
		'Schoolbell',
		'Indie Flower'
	);

	/**
	 * Default font styles.
	 *
	 * @var  array
	 */
	protected $default = array();

	/**
	 * Support sections.
	 *
	 * @var  array
	 */
	protected $sections = array('heading', 'menu', 'body');

	/**
	 * Support font type.
	 *
	 * @var  array
	 */
	protected $types = array('standard', 'google'/*, 'embed'*/);

	/**
	 * Disable label by default.
	 *
	 * @return  string
	 */
	protected function getLabel()
	{
		return '';
	}

	/**
	 * Parse field declaration to render input.
	 *
	 * @return  void
	 */
	public function getInput()
	{
		// Make sure we have options declared
		if ( ! isset($this->element->option))
		{
			return JText::_('JSN_TPLFW_FONT_MISSING_DEFAULT_FONT_STYLES');
		}

		// Initialize field value
		! empty($this->value) OR $this->value = (string) $this->element['default'];

		if (is_string($this->value))
		{
			$this->value	= (substr($this->value, 0, 1) == '{' AND substr($this->value, -1) == '}')
							? json_decode($this->value, true)
							: array('style' => $this->value);
		}

		// Store default font style for this option
		if (is_string($this->default))
		{
			$this->default = array('style' => $this->default);
		}

		// Parse default font style options
		foreach ($this->element->option AS $option)
		{
			// Store option
			$this->options[(string) $option['name']] = array(
				'label' => (string) $option['label'],
				'customizable' => isset($option['customizable']) ? (int) $option['customizable'] : 0
			);

			foreach ($option->children() AS $default)
			{
				$this->default[(string) $option['name']][$default->getName()] = array(
					'type' => (string) $default['type'],

					// Standard fonts
					'family' => (string) $default['family'],

					// Google fonts
					'primary' => (string) $default['primary'],
					'secondary' => (string) $default['secondary'],

					// Embed fonts
					'file' => (string) $default['file']
				);
			}

			// Store default font size for this option
			if (isset($option['defaultFontSize']))
			{
				$this->default[(string) $option['name']]['size'] = (int) $option['defaultFontSize'];
			}
		}

		// Preset missing field value
		foreach (array_keys($this->options) AS $style)
		{
			foreach ($this->sections AS $section)
			{
				if ( ! isset($this->value[$style][$section]) AND isset($this->default[$style][$section]))
				{
					$this->value[$style][$section] = $this->default[$style][$section];
				}
			}

			if ( ! isset($this->value[$style]['size']) AND isset($this->default[$style]['size']))
			{
				$this->value[$style]['size'] = $this->default[$style]['size'];
			}
		}

		// Prepare other field attributes
		$this->disabled = (string) $this->element['disabled'] == 'true';

		return parent::getInput();
	}
}
