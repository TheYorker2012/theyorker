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

	<?php
		$CI = &get_instance();
		$CI->load->view('uploader/upload_single_photo', array('action_url' => vip_url('directory/photos/upload')) );
	?>
</div>
