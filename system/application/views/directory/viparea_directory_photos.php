<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
<h4>Disclaimer</h4>
	<p>
		<?php echo $disclaimer_text; ?>
	</p>
</div>
<div class='blue_box'>
	<?php foreach( $images->result() as $image ) { ?>
	<img src='<?=imageLocation($image->photo_id, 'slideshow')?>' alt='<?php echo $organisation.' image '.$image->photo_title; ?>'/>
	<br />
	<?=anchor('viparea/directory/'.$organisation['shortname'].'/photos/move/'.$image->photo_id.'/up', 'move up')?> | 
	<?=anchor('viparea/directory/'.$organisation['shortname'].'/photos/move/'.$image->photo_id.'/down', 'move down')?> | 
	<?=anchor('viparea/directory/'.$organisation['shortname'].'/photos/delete/'.$image->photo_id.'', 'delete')?> 
	<br />
	<?php } ?>
</div>
<div class='blue_box'>
<h2> photo upload </h2>
<form action='/viparea/directory/<?=$organisation['shortname']?>/photos/upload' method='POST'>
	Photo File : <input type="file" name="userfile1" size="30" />
<input type="hidden" name="destination" id="destination" value="1" />
<input type="submit" value="upload" />
</form>
</div>
<a href='/viparea/'>Back to the vip area.</a>