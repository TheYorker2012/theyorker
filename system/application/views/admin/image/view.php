<div class="blue_box" style="width:638px;overflow:auto">
<?php if($images->num_rows() > 0) foreach ($images->result() as $image) {?>
	<div style="display:inline;padding: 10px;float:left">
		<h5><?=$image->image_title?> - <?=$image->image_id?></h5>
		<?=$this->image->getImage($image->image_id, $image->image_image_type_id)?><br />
<?php if (!$image->image_type_photo_thumbnail) {?>
		<a href="<?=site_url('admin/imagecp/view/'.$codename.'/'.$image->image_id.'/delete')?>" onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
<?php } ?>
	</div>
<?php } else { ?>
			<p>There are no images :(</p>
<?php } ?>
</div><br />

<div class="blue_box" style="width:638px;overflow:auto">
	<form action="<?=site_url($this->uri->uri_string())?>" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>Replace Image</legend>
			<label for="upload">New Image</label>
			<input type="file" name="upload" /></br>
			<label for="image_id">Image Id</label>
			<input type="text" name="image_id" value="" />
			<input type="submit" value="Upload" />
		</fieldset>
	</form>
</div>