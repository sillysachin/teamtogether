<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  TPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get custom style declaration
$curDocument = JFactory::getDocument();
$customStyle = $curDocument->customStyle;

// Process custom style
foreach ($customStyle AS $section => $data)
{
	if ( ! is_array($data))
	{
		continue;
	}

	switch ($customStyle[$section]['type'])
	{
		case 'google':
			// Generate declaration for 'font-family' property
			$customStyle[$section]['type'] = "'{$customStyle[$section]['primary']}', {$customStyle[$section]['secondary']}";
?>
/* Import Google font face for use in <?php echo $section; ?> */
@import url(https://fonts.googleapis.com/css?family=<?php echo str_replace(' ', '+', $customStyle[$section]['primary']); ?>);
<?php
			echo "\n";
		break;

		case 'embed':
			// Generate name for custom font face
			$name = preg_replace('/\.(ttf|otf|eot|svg|woff)$/', '', $customStyle[$section]['file']);

			// Generate declaration for 'font-family' property
			$customStyle[$section]['type'] = "'{$name}'";
?>
/* Declare custom font face for use in <?php echo $section; ?> */
@font-face {
	font-family: '<?php echo $name; ?>';
	src: url('<?php echo $curDocument->templateUrl . "/uploads/fonts/{$name}.eot"; ?>') format('eot'),
	     url('<?php echo $curDocument->templateUrl . "/uploads/fonts/{$name}.woff"; ?>') format('woff'),
	     url('<?php echo $curDocument->templateUrl . "/uploads/fonts/{$name}.ttf"; ?>') format('truetype'),
	     url('<?php echo $curDocument->templateUrl . "/uploads/fonts/{$name}.svg"; ?>') format('svg');
}
<?php
			echo "\n";
		break;

		case 'standard':
		default:
			// Generate declaration for 'font-family' property
			$customStyle[$section]['type'] = $customStyle[$section]['family'];
		break;
	}
}

// Set custom style
?>
/* Set font style for body */
body {
	font-family: <?php echo $customStyle['body']['type']?>;
	font-size: <?php echo $customStyle['size']; ?>%;
}

/* Set font style for heading */
h1,
h2,
h3,
h4,
h5,
h6,
#jsn-gotoplink,
.page-header,
.subheading-category,
.componentheading,
.contentheading {
	font-family: <?php echo $customStyle['heading']['type']?>;
}

/* Set font style for menu */
#jsn-menu a,
#jsn-menu span {
	font-family: <?php echo $customStyle['menu']['type']?>;
}
