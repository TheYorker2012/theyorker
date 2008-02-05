<div id="RightColumn">
<h2>What's this?</h2>
	<p><?php echo(xml_escape($main_text)); ?></p>
<h2>Disclaimer</h2>
	<p><?php echo(xml_escape($disclaimer_text)); ?></p>
</div>
<div id="MainColumn">
	<div class="BlueBox">
	<h2>Current Images</h2>
		<?php if(count($images->result())==0){ echo'<p>This venue has no images</p>';} ?>
		<?php foreach( $images->result() as $image ) { ?>
		<?php echo($this->image->getThumb($image->photo_id, 'slideshow')); ?>
		<p>
		<?php echo('<a href="office/reviews/'.$organisation['shortname'].'/'.$ContextType.'/photos/move/'.$image->photo_id.'/up">move up</a>'); ?> |
		<?php echo('<a href="office/reviews/'.$organisation['shortname'].'/'.$ContextType.'/photos/move/'.$image->photo_id.'/down">move down</a>'); ?> |
		<a href="/office/reviews/<?php echo($organisation['shortname']); ?>/<?php echo(xml_escape($ContextType)); ?>/photos/delete/<?php echo($image->photo_id); ?>" onClick="return confirm('Are you sure you want to delete this photo?');">delete</a>
		<?php } ?>
		</p>
	</div>

	<?php
		$CI = &get_instance();
		$CI->load->view('uploader/upload_single_photo', array('action_url' => '/office/reviews/'.$organisation['shortname'].'/'.$ContextType.'/photos/upload') );
	?>
</div>