<div class="blue_box" style="width:638px;overflow:auto">
<?php if($imageType->num_rows() > 0) foreach ($imageType->result() as $type) {?>
	<div style="display:inline;min-width: 310px;float:left">
		<h5><?=$type->image_type_name?></h5>
		<?=$this->image->getImage(0, $type->image_type_codename)?><br />
		<a href="<?=site_url('admin/imagecp/edit/'.$type->image_type_codename)?>">Edit</a>,
<?php if(!$type->image_type_photo_thumbnail) { ?>
		<a href="<?=site_url('admin/imagecp/add/'.$type->image_type_codename)?>">Add</a>,
<?php } ?>
		<a href="<?=site_url('admin/imagecp/view/'.$type->image_type_codename)?>">View All</a>,
		<a href="<?=site_url('admin/imagecp/delete/'.$type->image_type_codename)?>">Delete All</a>
	</div>
<?php } else { ?>
			<p>There are no image types :(</p>
<?php } ?>
</div><br />
<?=$extra?>
