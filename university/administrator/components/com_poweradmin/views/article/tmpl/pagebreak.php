<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: pagebreak.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$script  = 'function insertPagebreak() {'."\n\t";
// Get the pagebreak title
$script .= 'var title = document.getElementById("title").value;'."\n\t";
$script .= 'if (title != \'\') {'."\n\t\t";
$script .= 'title = "title=\""+title+"\" ";'."\n\t";
$script .= '}'."\n\t";
// Get the pagebreak toc alias -- not inserting for now
// don't know which attribute to use...
$script .= 'var alt = document.getElementById("alt").value;'."\n\t";
$script .= 'if (alt != \'\') {'."\n\t\t";
$script .= 'alt = "alt=\""+alt+"\" ";'."\n\t";
$script .= '}'."\n\t";
$script .= 'var tag = "<hr class=\"system-pagebreak\" "+title+" "+alt+"/>";'."\n\t";
$script .= 'window.parent.jInsertEditorText(tag, \''.$this->eName.'\');'."\n\t";
$script .= 'window.parent.SqueezeBox.close();'."\n\t";
$script .= 'return false;'."\n";
$script .= '}'."\n";

JFactory::getDocument()->addScriptDeclaration($script);
?>
<form>
	<table width="100%" align="center">
		<tr width="40%">
			<td class="key" align="right">
				<label for="title">
					<?php echo JText::_( 'COM_CONTENT_PAGEBREAK_TITLE' ); ?>
				</label>
			</td>
			<td>
				<input type="text" id="title" name="title" />
			</td>
		</tr>
		<tr width="60%">
			<td class="key" align="right">
				<label for="alias">
					<?php echo JText::_( 'COM_CONTENT_PAGEBREAK_TOC' ); ?>
				</label>
			</td>
			<td>
				<input type="text" id="alt" name="alt" />
			</td>
		</tr>
	</table>
</form>
<button onclick="insertPagebreak();"><?php echo JText::_( 'COM_CONTENT_PAGEBREAK_INSERT_BUTTON' ); ?></button>
