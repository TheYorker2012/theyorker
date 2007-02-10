	<h2>information</h2>
	<form class="form" method="post" action="save">
		<fieldset>
			<label for="title">Title: </label>
				<input type="text" name="title" value="<?=$photoDetails->photo_title?>" /><br />
			<label for="date">Date: </label>
				<input type="date" name="date" value="<?=$photoDetails->photo_timestamp?>" /><br />
			<label for="photographer">Photographer: </label>
				<select name="photographer">
					<?php foreach($photographer as $person): ?>
					<option value="<?=$person->user_entity_id?>" <?php if ($person->user_entity_id == $photoDetails->photo_author_user_entity_id) echo 'selected';?>><?=$person->user_firstname.' '.$person->user_surname?></option>
					<?php endforeach;?>
				</select><br />
			<label for="tags">Tags: </label>
				<select multiple size="8" name="tags">
					<?php foreach ($photoTag->result as $tag):?>
					<option value="<?=$tag->tag_id?>"><?=$tag->tag_name?></option>
					<?php endforeach;?>
				</select><br />
			<label></label>
				<a href="#">+ Add More Tags</a><br />
			<label></label>
				<a href="#">- Delete Selected Tags</a><br />
			<label>Home Feature: </label>
				<input type='checkbox' name='onfrontpage' /><br />
			<label>Hidden: </label>
				<input type='checkbox' name='hidden' /><br />
			<input type="submit" class="button" value="Save" />
		</fieldset>
	</form>
</div>
<div class="grey_box">
	<h2>previews</h2>
	<?php foreach($type as $image):?>
	<?=$image->image_type_name?> (<?=$image->image_type_width?>x<?=$image->image_type_height?>)<br />
	<img src="<?=imageLocation($photoDetails->photo_id, $image->image_type_id)?>" /><br /><br />
	<?php endforeach;?>
	Full Size<br />
	<a href="<?=photoLocation($photoDetails->photo_id)?>">Click here to view</a><br /><br />
	Not happy with these thumbnails? <a href="edit">Click here</a> to re-thumbnail.
</div>