<div class="blue_box" style="width:638px;overflow:auto">
<?php if($images->num_rows() > 0) foreach ($images->result() as $image) {?>
	<div style="display:inline;padding: 10px;float:left">
		<h5><?=$image->image_title?></h5>
		<?=$this->image->getImage($image->image_id, $type->image_image_type_id)?><br />
		<a href="<?=site_url('admin/imagecp/'.$codename.'/'.$image->image_id.'/delete')?>">Delete</a>
	</div>
<?php } else { ?>
			<p>There are no images :(</p>
<?php } ?>
</div><br />
