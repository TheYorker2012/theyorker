<div id="RightColumn">
<h2>What's this?</h2>
	<p><?php echo $main_text; ?></p>
<h2>Disclaimer</h2>
	<p><?php echo $disclaimer_text; ?></p>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<?php foreach( $images->result() as $image ) { ?>
		<?=$this->image->getThumb($image->photo_id, 'slideshow')?>
		<br />
		<?=anchor('office/reviews/'.$organisation['shortname'].'/'.$ContextType.'/photos/move/'.$image->photo_id.'/up', 'move up')?> |
		<?=anchor('office/reviews/'.$organisation['shortname'].'/'.$ContextType.'/photos/move/'.$image->photo_id.'/down', 'move down')?> |
		<a href="/office/reviews/<?=$organisation['shortname']?>/<?=$ContextType?>/photos/delete/<?=$image->photo_id?>" onClick="return confirm('Are you sure you want to delete this photo?');">delete</a>
		<br />
		<?php } ?>
	</div>

	<?php
		$CI = &get_instance();
		$CI->load->view('uploader/upload_single_photo', array('action_url' => 'office/reviews/'.$organisation['shortname'].'/'.$ContextType.'/photos/upload') );
	?>
</div>