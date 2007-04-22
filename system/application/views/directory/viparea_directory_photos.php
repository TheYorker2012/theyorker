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
	<?=anchor(vip_url('directory/photos/move/'.$image->photo_id.'/up'), 'move up')?> |
	<?=anchor(vip_url('directory/photos/move/'.$image->photo_id.'/down'), 'move down')?> |
	<?=anchor(vip_url('directory/photos/delete/'.$image->photo_id.''), 'delete')?>
	<br />
	<?php } ?>
</div>
<div class='blue_box'>
<h2> photo upload </h2>
<div id="source" style="display:none">
	<label for="title">Photo Title:</label><input type="text" name="title" size="30" /><br />
	<label for="userfile">Photo File:</label><input type="file" name="userfile" size="30" /><br />
</div>
<?=form_open_multipart(vip_url('directory/photos/upload')); ?>
<p>Photo's should be in jpg format. The upload size limit is 2mb(?).</p><br />
<div>
	<label for="title1">Photo Title:</label><input type="text" name="title1" size="30" />
	<br />
	<label for="userfile1">Photo File:</label><input type="file" name="userfile1" size="30" />
	<br />
</div>
<input type="hidden" name="destination" id="destination" value="1" />

<input type="button" onClick="AddClones()" value="Another"/>
<input type="submit" value="upload" />
</form>
</div>
<a href='/viparea/'>Back to the vip area.</a>