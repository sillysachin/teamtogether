<div class="<?php echo implode(' ', $this->inputClass) ?>">
	<?php if (!empty($this->textPrefix)): ?>
	<span class="add-on"><?php echo $this->textPrefix ?></span>
	<?php endif ?>

	<input type="<?php echo $this->dataType ?>"
		name="<?php echo $this->name ?>"
		id="<?php echo $this->id ?>"
		value="<?php echo $this->value ?>"
		class="<?php echo $this->element['class'] ?>" <?php echo $this->inputAttrs ?> />

	<?php if (!empty($this->textSuffix)): ?>
	<span class="add-on"><?php echo $this->textSuffix ?></span>
	<?php endif ?>
</div>