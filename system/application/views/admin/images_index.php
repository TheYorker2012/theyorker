TODO Gallery system to view and edit photos and associated images
<?=anchor('admin/images/upload', 'Upload new photos')?>
<p>
<?php
$column = 0;
if ($shownPhotos->num_rows() > 0) foreach($shownPhotos->result() as $photo) {
	if ($column = PHOTOS_PERROW) {
		$column = 0;
		echo '</p><p>';
	}
	echo '<a href="id/'.$photo->photo_id.'"><img src="'.imageLocation($photo->photo_id, $imageType->image_type_id).'" /></a>';
	$column++;
}
?>
</p>
<?=$pages?>