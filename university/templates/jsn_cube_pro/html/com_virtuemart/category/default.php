<?php
/**
 *
 * Show the products in a category
 *
 * @package    VirtueMart
 * @subpackage
 * @author RolandD
 * @author Max Milbers
 * @todo add pagination
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 6413 2012-09-10 07:42:25Z alatak $
 */

//vmdebug('$this->category',$this->category);
vmdebug ('$this->category ' . $this->category->category_name);
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
JHTML::_ ('behavior.modal');
/* javascript for list Slide
  Only here for the order list
  can be changed by the template maker
*/
$js = "
jQuery(document).ready(function () {
	jQuery('.orderlistcontainer').hover(
		function() { jQuery(this).find('.orderlist').stop().show()},
		function() { jQuery(this).find('.orderlist').stop().hide()}
	)
});
";

$document = JFactory::getDocument ();
$document->addScriptDeclaration ($js);

/*$edit_link = '';
if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
if (Permissions::getInstance()->check("admin,storeadmin")) {
	$edit_link = '<a href="'.JURI::root().'index.php?option=com_virtuemart&tmpl=component&view=category&task=edit&virtuemart_category_id='.$this->category->virtuemart_category_id.'">
		'.JHTML::_('image', 'images/M_images/edit.png', JText::_('COM_VIRTUEMART_PRODUCT_FORM_EDIT_PRODUCT'), array('width' => 16, 'height' => 16, 'border' => 0)).'</a>';
}

echo $edit_link; */
if (empty($this->keyword)) {
	?>

<div class="category_description"> <?php echo $this->category->category_description; ?> </div>
<?php
}

/* Show child categories */

if (VmConfig::get ('showCategory', 1) and empty($this->keyword)) {
	if ($this->category->haschildren) {

		// Category and Columns Counter
		$iCol = 1;
		$iCategory = 1;

		// Calculating Categories Per Row
		$categories_per_row = VmConfig::get ('categories_per_row', 3);
		$category_cellwidth = ' width' . floor (100 / $categories_per_row);

		// Separator
		$verticalseparator = " vertical-separator";
		?>
<div class="category-view">
	<?php // Start the Output
		if (!empty($this->category->children)) {
			foreach ($this->category->children as $category) {

				// Show the horizontal seperator
				if ($iCol == 1 && $iCategory > $categories_per_row) {
					?>
	<div class="horizontal-separator"></div>
	<?php
				}

				// this is an indicator wether a row needs to be opened or not
				if ($iCol == 1) {
					?>
	<div class="row">
		<?php
				}

				// Show the vertical seperator
				if ($iCategory == $categories_per_row or $iCategory % $categories_per_row == 0) {
					$show_vertical_separator = ' ';
				} else {
					$show_vertical_separator = $verticalseparator;
				}

				// Category Link
				$caturl = JRoute::_ ('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id);

				// Show Category
				?>
		<div class="category floatleft<?php echo $category_cellwidth . $show_vertical_separator ?>">
			<div class="spacer">
				<h2> <a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>"> <?php echo $category->category_name ?> <br/>
					<?php // if ($category->ids) {
								echo $category->images[0]->displayMediaThumb ("", FALSE);
								//} ?>
					</a> </h2>
			</div>
		</div>
		<?php
				$iCategory++;

				// Do we need to close the current row now?
				if ($iCol == $categories_per_row) {
					?>
		<div class="clear"></div>
	</div>
	<?php
					$iCol = 1;
				} else {
					$iCol++;
				}
			}
		}
		// Do we need a final closing row tag?
		if ($iCol != 1) {
			?>
	<div class="clear"></div>
</div>
<?php } ?>
</div>
<?php
	}
}
?>
<div class="browse-view">
	<?php
if (!empty($this->keyword)) {
	?>
	<h3><?php echo $this->keyword; ?></h3>
	<?php
} ?>
	<?php if ($this->search !== NULL) { ?>
	<form action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=category&limitstart=0&virtuemart_category_id=' . $this->category->virtuemart_category_id); ?>" method="get">
		
		<!--BEGIN Search Box -->
		<div class="virtuemart_search"> <?php echo $this->searchcustom ?> <br/>
			<?php echo $this->searchcustomvalues ?>
			<input name="keyword" class="inputbox" type="text" size="20" value="<?php echo $this->keyword ?>"/>
			<input type="submit" value="<?php echo JText::_ ('COM_VIRTUEMART_SEARCH') ?>" class="button" onclick="this.form.keyword.focus();"/>
		</div>
		<input type="hidden" name="search" value="true"/>
		<input type="hidden" name="view" value="category"/>
	</form>
	<!-- End Search Box -->
	<?php } ?>
	<?php // Show child categories
if (!empty($this->products)) {
	?>
	<h1><?php echo $this->category->category_name; ?></h1>
	<div class="orderby-displaynumber">
		<div class="width70 floatleft"> <?php echo $this->orderByList['orderby']; ?> <?php echo $this->orderByList['manufacturer']; ?> </div>
		<div class="width30 floatright display-number"><?php echo $this->vmPagination->getResultsCounter ();?><br/>
			<?php echo $this->vmPagination->getLimitBox (); ?></div>
		<div class="vm-pagination">
			<?php //echo $this->vmPagination->getPagesLinks (); ?>
			<span style="float:right"><?php //echo $this->vmPagination->getPagesCounter (); ?></span>
		</div>
		<div class="clear"></div>
	</div>
	<!-- end of orderby-displaynumber -->
	
	<?php
	// Category and Columns Counter
	$iBrowseCol = 1;
	$iBrowseProduct = 1;

	// Calculating Products Per Row
	$BrowseProducts_per_row = $this->perRow;
	$Browsecellwidth = ' width' . floor (100 / $BrowseProducts_per_row);

	// Separator
	$verticalseparator = " vertical-separator";

	// Count products ?? why not just count ($this->products)  ?? note by Max Milbers
	$BrowseTotalProducts = 0;
	foreach ($this->products as $product) {
		$BrowseTotalProducts++;
	}

	// Start the Output
	foreach ($this->products as $product) {

		// Show the horizontal seperator
		if ($iBrowseCol == 1 && $iBrowseProduct > $BrowseProducts_per_row) {
			?>
	<div class="horizontal-separator"></div>
	<?php
		}

		// this is an indicator wether a row needs to be opened or not
		if ($iBrowseCol == 1) {
			?>
	<div class="row">
		<?php
		}

		// Show the vertical seperator
		if ($iBrowseProduct == $BrowseProducts_per_row or $iBrowseProduct % $BrowseProducts_per_row == 0) {
			$show_vertical_separator = ' ';
		} else {
			$show_vertical_separator = $verticalseparator;
		}

		// Show Products
		?>
		<div class="jsn-product floatleft<?php echo $Browsecellwidth . $show_vertical_separator ?>">
			<div class="spacer">
				<div class="product-image center">
					<?php /** @todo make image popup */
					echo $product->images[0]->displayMediaThumb ('class="browseProductImage" border="0" title="' . $product->product_name . '" ', TRUE, 'class="modal"');
					?>
				</div>
				<div class="clear"></div>
				<div class="center product-name">
					<h2><?php echo JHTML::link ($product->link, $product->product_name); ?></h2>
					<?php // Product Short Description
					if (!empty($product->product_s_desc)) {
						?>
						<!--p class="product_s_desc">
							<?php //echo shopFunctionsF::limitStringByWord ($product->product_s_desc, 40, '...') ?>
						</p-->
					<?php } ?>
					<div class="product-price marginbottom12" id="productPrice<?php echo $product->virtuemart_product_id ?>">
						<?php
						if ($this->show_prices == '1') {
							if (empty($product->prices) and VmConfig::get ('askprice', 1) and  !$product->images[0]->file_is_downloadable) {
								echo JText::_ ('COM_VIRTUEMART_PRODUCT_ASKPRICE');
							}
							//todo add config settings
							if ($this->showBasePrice) {
								echo $this->currency->createPriceDiv ('basePrice', 'COM_VIRTUEMART_PRODUCT_BASEPRICE', $product->prices);
								echo $this->currency->createPriceDiv ('basePriceVariant', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_VARIANT', $product->prices);
							}
							echo $this->currency->createPriceDiv ('variantModification', 'COM_VIRTUEMART_PRODUCT_VARIANT_MOD', $product->prices);
							if ($product->prices['basePriceWithTax'] !== $product->prices['salesPrice']) {
								echo '<span class="price-crossed" >' . $this->currency->createPriceDiv ('basePriceWithTax', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX', $product->prices) . "</span>";
							}
							if ($product->prices['salesPriceWithDiscount'] != $product->prices['salesPrice']) {
								echo $this->currency->createPriceDiv ('salesPriceWithDiscount', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT', $product->prices);
							}
							echo $this->currency->createPriceDiv ('salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $product->prices);
							echo $this->currency->createPriceDiv ('priceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $product->prices);
							echo $this->currency->createPriceDiv ('discountAmount', 'COM_VIRTUEMART_PRODUCT_DISCOUNT_AMOUNT', $product->prices);
							echo $this->currency->createPriceDiv ('taxAmount', 'COM_VIRTUEMART_PRODUCT_TAX_AMOUNT', $product->prices);
							$unitPriceDescription = JText::sprintf ('COM_VIRTUEMART_PRODUCT_UNITPRICE', $product->product_unit);
							echo $this->currency->createPriceDiv ('unitPrice', $unitPriceDescription, $product->prices);
						} ?>
					</div>
								
					<form method="post" class="product" action="index.php" id="addtocartproduct<?php echo $product->virtuemart_product_id ?>">
						<div class="addtocart-bar">
								<?php // Display the quantity box ?>
								<!-- <label for="quantity<?php echo $this->product->virtuemart_product_id;?>" class="quantity_box"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label> -->
								<span class="quantity-box">
									<input style="display:none;" type="text" class="quantity-input" name="quantity[]" value="1" />
								</span>
								
								<?php // Display the quantity box END ?>
					
								<?php // Add the button
								$button_lbl = JText::_('COM_VIRTUEMART_CART_ADD_TO');
								$button_cls = ''; //$button_cls = 'addtocart_button';
								if (VmConfig::get('check_stock') == '1' && !$this->product->product_in_stock) {
									$button_lbl = JText::_('COM_VIRTUEMART_CART_NOTIFY');
									$button_cls = 'notify-button';
								} ?>
					
								<?php // Display the add to cart button ?>
								<span class="addtocart-button">
									<input type="submit" name="addtocart"  class="addtocart-button" value="<?php echo $button_lbl ?>" title="<?php echo $button_lbl ?>" />
								</span>
					
							<div class="clear"></div>
							</div>
					
							<?php // Display the add to cart button END ?>
							<input type="hidden" class="pname" value="<?php echo $product->product_name ?>">
							<input type="hidden" name="option" value="com_virtuemart" />
							<input type="hidden" name="view" value="cart" />
							<noscript><input type="hidden" name="task" value="add" /></noscript>
							<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $product->virtuemart_product_id ?>" />
							<?php /** @todo Handle the manufacturer view */ ?>
							<input type="hidden" name="virtuemart_manufacturer_id" value="<?php echo $product->virtuemart_manufacturer_id ?>" />
							<input type="hidden" name="virtuemart_category_id[]" value="<?php echo $product->virtuemart_category_id ?>" />
					</form>
					
					<?php
					if (!VmConfig::get ('use_as_catalog') and !(VmConfig::get ('stockhandle', 'none') == 'none') && (VmConfig::get ('display_stock', 1))) {
						?>
					<!-- if (!VmConfig::get('use_as_catalog') and !(VmConfig::get('stockhandle','none')=='none')){?> -->
					<div class="paddingtop8"> <span class="vmicon vm2-<?php echo $product->stock->stock_level ?>" title="<?php echo $product->stock->stock_tip ?>"></span> <span class="stock-level"><?php echo JText::_ ('COM_VIRTUEMART_STOCK_LEVEL_DISPLAY_TITLE_TIP') ?></span> </div>
					<?php }?>
				</div>
				<div class="clear"></div>
			</div>
			<!-- end of spacer --> 
		</div>
		<!-- end of product -->
		<?php

		// Do we need to close the current row now?
		if ($iBrowseCol == $BrowseProducts_per_row || $iBrowseProduct == $BrowseTotalProducts) {
			?>
		<div class="clear"></div>
	</div>
	<!-- end of row -->
	<?php
			$iBrowseCol = 1;
		} else {
			$iBrowseCol++;
		}

		$iBrowseProduct++;
	} // end of foreach ( $this->products as $product )
	// Do we need a final closing row tag?
	if ($iBrowseCol != 1) {
		?>
	<div class="clear"></div>
	<?php
	}
	?>
	<div class="vm-pagination"><?php echo $this->vmPagination->getPagesLinks (); ?><span style="float:right"><?php echo $this->vmPagination->getPagesCounter (); ?></span></div>
	<?php
} elseif ($this->search !== NULL) {
	echo JText::_ ('COM_VIRTUEMART_NO_RESULT') . ($this->keyword ? ' : (' . $this->keyword . ')' : '');
}
?>
</div>
<!-- end browse-view -->