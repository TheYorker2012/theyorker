<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<?php echo($main_text); ?>
	</div>

	<h2>Disclaimer</h2>
	<div class="Entry">
		<?php echo($disclaimer_text); ?>
	</div>
</div>

<div id="MainColumn">
<?php if($images->num_rows() > 0) { ?>
	<div class="BlueBox">
	<?php foreach( $images->result() as $image ) {
		echo($this->image->getThumb($image->photo_id, 'slideshow'));
		echo('<br />');
		echo(anchor(vip_url('directory/photos/move/'.$image->photo_id.'/up'), 'move up').'|');
		echo(anchor(vip_url('directory/photos/move/'.$image->photo_id.'/down'), 'move down').'|');
		echo('<a href="'.vip_url('directory/photos/delete/'.$image->photo_id).'" onclick="return confirm(\'Are you sure you want to delete this photo?\');">delete</a>');
		echo('<br />');
	} ?>
	</div>
<?php } ?>

	<div class="BlueBox">
		<h2>photo upload</h2>

<?php echo(form_open_multipart(vip_url('directory/photos/upload'))); ?>
		<p>Photo's should be in jpg format. The upload size limit is 2MB.</p>
		<fieldset>
			<label for="title1">Photo Title:</label>
				<input type="text" name="title1" id="title1" />
			<label for="userfile1">Photo File:</label>
				<input type="file" name="userfile1" id="userfile1" />
			<input type="hidden" name="destination" id="destination" value="1" />
		</fieldset>
		<fieldset>
			<input type="submit" value="Upload" class="button" />
		</fieldset>
		</form>
	</div>
</div>
