<div class="blue_box" style="width:638px;overflow:auto">
<?php if($imageType->num_rows() > 0) foreach ($imageType->result() as $type) {?>
	<div style="display:inline;min-width: 310px;float:left">
		<h5><?=$type->image_type_name?></h5>
		<?=$this->image->getImage(0, $type->image_type_codename)?><br />
		<a href="imagecp/<?=$type->image_type_codename?>">Edit</a>, <a href="imagecp/view/<?=$type->image_type_codename?>">View All</a>, <a href="imagecp/delete/<?=$type->image_type_codename?>">Delete All</a>
	</div>
<?php } else { ?>
			<p>There are no image types :(</p>
<?php } ?>
</div><br />
<?=$extra?>
