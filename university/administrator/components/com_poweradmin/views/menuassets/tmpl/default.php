<?php
$cssPlainList = array();
$jsPlainList  = array();
$initCssList = "";
$initJsList = "";
// Proceed to init css File list.
if(count($this->cssFiles)){
	$initCssList = "<div id='jsn-tsn-css-item-list' class='jsn-items-list'>";	
	foreach ($this->cssFiles as $key=>$value){		
		array_push($cssPlainList, $key);
		$checked = "";		
		if($value->loaded == "true"){
			$checked = "checked='checked'";
		}
		$initCssList .= "<div class='jsn-item ui-state-default'><label class='checkbox'><input type='checkbox' " . $checked . " name='cssItems[]' value='" . $key . "'>" . $key . "</input></label></div>";
		
	}
	
	$initCssList .= "</div>";
}

// Proceed to init js File list.
if(count($this->jsFiles)){
	$initJsList = "<ul id='jsn-tsn-js-item-list' class='jsn-items-list'>";
	foreach ($this->jsFiles as $key=>$value){
		array_push($jsPlainList, $key);
		$checked = "";
		if($value->loaded == "true"){
			$checked = "checked='checked'";			
		}
		$initJsList .= "<li class='jsn-item ui-state-default'><label class='checkbox'><input type='checkbox' " . $checked . " name='jsItems[]' value='" . $key . "'>" . $key . "</input></label></li>";

	}

	$initJsList .= "</ul>";
}


?>

<div id="jsn-menu-assets" class="jsn-bootstrap">
	<form action="index.php?option=com_poweradmin&task=menuitem.saveassets" name="adminForm" method="post">
		<input type="hidden" name="id" value="<?php echo JRequest::getInt('id'); ?>"/>
			<div class="control-group jsn-items-list-container">
				<label class="control-label" for="css-item-list" >
					<span class="control-label-withtip"  title="<?php echo JText::_('JSN_POWERADMIN_MENUASSETS_HELP_CSS')?>">
						<?php echo JText::_('JSN_POWERADMIN_MENUASSETS_CUSTOM_CSS_FILES')?>
					</span>
					
				</label>		
				<div class="controls">
					<div class="jsn-buttonbar">
						<button type="button" id="css-editor" class="btn btn-small"> <i class="icon-pencil"></i><?php echo JText::_('JSN_POWERADMIN_MENUASSETS_EDIT')?></button>
					</div>			
					<div>
						<textarea id="css-item-list" class="jsn-asset-item-edit" name="cssPlainFiles"><?php echo implode("\n", $cssPlainList)?></textarea>
					</div>
					<?php echo $initCssList;?>					
				</div>	
			</div>

			<div class="jsn-form-bar">
				<label  class="checkbox"><input type="checkbox" <?php echo $this->applyCssToChildren ? 'checked="checked"': ''; ?> class="assets-apply-all" name="cssSameLevelApply"/><?php echo JText::_('JSN_POWERADMIN_MENUASSETS_APPLY_TO_CHILDREN_ITEMS')?></label>
			</div>

			<hr>
			<div class="control-group jsn-items-list-container">
				<label class="control-label" for="css-item-list">
					<span class="control-label-withtip"  title="<?php echo JText::_('JSN_POWERADMIN_MENUASSETS_HELP_JS')?>">
						<?php echo JText::_('JSN_POWERADMIN_MENUASSETS_CUSTOM_JS_FILES')?>
					</span>					
				</label>		
				<div class="controls">
					<div class="jsn-buttonbar">
						<button type="button" id="js-editor" class="btn btn-small"> <i class="icon-pencil"></i><?php echo JText::_('JSN_POWERADMIN_MENUASSETS_EDIT')?></button>
					</div>			
					<div>
						<textarea id="js-item-list" class="jsn-asset-item-edit" name="jsPlainFiles"><?php echo implode("\n", $jsPlainList)?></textarea>
					</div>
					<?php echo $initJsList;?>					
				</div>	
			</div>
			

			<div class="jsn-form-bar">
				<label  class="checkbox"><input type="checkbox" <?php echo $this->applyJsToChildren ? 'checked="checked"': ''; ?> class="assets-apply-all" name="jsSameLevelApply"/><?php echo JText::_('JSN_POWERADMIN_MENUASSETS_APPLY_TO_CHILDREN_ITEMS')?></label>
				</div>	

	</form>
</div>