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
	<a href='/viparea/directory/<?php echo $organisation['shortname']; ?>/photos/move/<?php echo $image->photo_id; ?>/up'>Move up</a> | <a href='/viparea/directory/<?php echo $organisation['shortname']; ?>/photos/move/<?php echo $image->photo_id; ?>/down'>Move down</a> | <a href='/viparea/directory/<?php echo $organisation['shortname']; ?>/photos/delete/<?php echo $image->photo_id; ?>'>Delete</a>
	<br />
	<?php } ?>
</div>
<div class='blue_box'>
<h2> photo upload </h2>
<form action='/viparea/directory/<?php echo $organisation; ?>/photos/upload' method='POST'>
	Photo File : <input type="file" name="userfile1" size="30" />
<input type="hidden" name="destination" id="destination" value="1" />
<input type="submit" value="upload" />
</form>
</div>
<a href='/viparea/'>Back to the vip area.</a>