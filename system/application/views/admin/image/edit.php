<?php if (isset($image_type_id)) {?>
<div class="blue_box">
	<form action="<?=site_url($this->uri->uri_string())?>" method="post" enctype="multipart/form-data">
		<fieldset>
			<input type="hidden" name="image_type_id" value="<?=$image_type_id?>" />
			<label for="image_type_name">Type Name</label>
			<input type="text" name="image_type_name" value="<?=$image_type_name?>" />
			<label for="image_type_codename">Type Codename</label>
			<input type="text" name="image_type_codename" value="<?=$image_type_codename?>" />
			<label for="image_type_width">Width</label>
			<input type="text" name="image_type_width" value="<?=$image_type_width?>" />
			<label for="image_type_height">Height</label>
			<input type="text" name="image_type_height" <?php if($image_type_height==1) echo 'checked'?> />
			<label for="image_type_photo_thumbnail">Is a photo thumbnail</label>
			<input type="checkbox" name="image_type_photo_thumbnail" value="1" /><br />
			<input type="submit" value="Save"/>
		</fieldset>
	</form>
	<form action="<?=site_url($this->uri->uri_string())?>" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>Null Image</legend>
			<?=$this->image->getImage(0, $type->image_type_codename)?><br />
			<label for="upload">New null image</label>
			<input type="file" name="upload" /></br>
			<input type="hidden" name="image_type_id" value="<?=$type->image_type_id?>" />
			<input type="submit" value="Upload" />
		</fieldset>
	</form>
</div>
<?php }?>