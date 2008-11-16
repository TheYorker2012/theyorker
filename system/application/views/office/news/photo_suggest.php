<form action="<?php echo($this->uri->uri_string()); ?>" method="post">
	<?php foreach ($_SESSION['img'] as $image) { ?>
		<div class="BlueBox">
			<?php echo($this->image->getThumb($image['list'], 'medium', 'false', array('style' => 'float:left; margin: 0.5em;'))); ?>
			<div style="float: left; width: 60%;">
				<label for="img<?php echo($image['list']); ?>_title">Photo Title:</label>
				<input type="text" name="img<?php echo($image['list']); ?>_title" id="img<?php echo($image['list']); ?>_title" value="" />
				<br />
				<label for="img<?php echo($image['list']); ?>_alt">ALT / Hover Text:</label>
				<input type="text" name="img<?php echo($image['list']); ?>_alt" id="img<?php echo($image['list']); ?>_alt" value="" />
				<br />
				<label for="img<?php echo($image['list']); ?>_add">Add to article:</label>
				<input name="imgadd[]" id="img<?php echo($image['list']); ?>_add" type="checkbox" value="<?php echo($image['list']); ?>" checked="checked" />
			</div>
		</div>
	<?php } ?>
	<div>
		<input type="submit" name="add_photos" value="Add Photos to Article" class="button" />
	</div>
</form>