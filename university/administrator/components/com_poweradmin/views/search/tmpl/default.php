<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: default.php 15295 2012-08-21 03:43:26Z hiepnv $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div id="jsn-poweradmin-sitesearch" class="jsn-page-search jsn-page-list">
<div class="jsn-bootstrap">
	<div class="jsn-search-filter jsn-bgpattern pattern-sidebar">
		<form action="<?php echo JRoute::_('index.php?option=com_poweradmin&view=search'); ?>" method="get" class="form-horizontal">
			<input type="hidden" name="option" value="com_poweradmin" />
			<input type="hidden" name="view" value="search" />

			Search for:
			<input type="text" name="keyword" id="keyword" value="<?php echo $this->keyword; ?>" class="input-large" />

			In:
			<?php echo $this->coverages ?>
			<button type="submit" class="btn btn-primary">Search</button>
		</form>
	</div>
	<?php if (isset($this->tabs) && is_array($this->tabs)): ?>
		<dl class="tabs">
			<?php foreach ($this->tabs as $key => $tab): ?>
				<?php $class = ($tab['selected'] == true) ? 'open' : 'closed' ?>
			<dt class="tabs <?php echo $class ?>">
				<span>
					<h3>
						<a href="<?php echo JRoute::_("index.php?option=com_poweradmin&view=search&coverage={$this->coverage}&tab={$key}") ?>"><?php echo $tab['title'] ?></a>
					</h3>
				</span>
			</dt>
			<?php endforeach ?>
		</dl>
	<?php endif ?>
	<div class="clearbreak" ></div>
	<div class="jsn-search-result">
		<?php $this->includeViewFile($this->config);?>
		<div class="clearbreak"></div>
	</div>
</div>
</div>

<?php
$products	=	JSNPaExtensionsHelper::getDependentExtensions();
// Display footer
JSNHtmlGenerate::footer($products);
?>