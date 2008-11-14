<div class="BlueBox">
	<div class="search_options">
		<a href="/office/gallery">
			<img src="/images/icons/button_back2gallery.png" alt="Go back to the gallery" title="Go back to the gallery" />
		</a>
		<br />
		<a href="/office/gallery/return">
			<img src="/images/icons/button_insertphoto.png" alt="Insert this photo into an article" title="Insert this photo into an article" />
		</a>
	</div>

	<h2>photo information</h2>
	<form method="post" action="/office/gallery/show/<?php echo($photo->id); ?>/save">
		<fieldset>
			<label for="title">Title: </label>
			<input type="text" name="title" id="title" value="<?php echo(xml_escape($photo->title)); ?>" size="30" />
			<br />
			<label for="date">Date: </label>
			<div id="date" class="input"><?php echo(date('d/m/y H:i', $photo->timestamp)); ?></div>
			<br />
			<label for="uploader">Uploader: </label>
			<div id="uploader" class="input"><?php echo(xml_escape($photo->user_firstname . ' ' . $photo->user_surname)); ?></div>
			<br />
			<label for="source">Source: </label>
			<input type="text" name="source" id="source" value="<?php echo(xml_escape($photo->source)); ?>" size="30" />
			<br />
			<label for="watermark">Watermark: </label>
			<input type="text" name="watermark" id="watermark" value="<?php echo(xml_escape($photo->watermark)); ?>" />
			<br />
			<label for="watermark_colour">Watermark Colour: </label>
			<select name="watermark_colour" id="watermark_colour" size="1">
<?php foreach ($watermark_colours as $colour) { ?>
				<option value="<?php echo($colour->id); ?>"<?php if ($colour->id == $photo->watermark_colour_id) echo(' selected="selected"'); ?>><?php echo($colour->name); ?></option>
<?php } ?>
			</select>
			<br />
			<label for="tags_container">Tags: </label>
			<div id="tags_container" class="input">
				<div id="tags_existing">
					<?php foreach ($photo_tags as $tag) { ?>
						<span><img src="/images/icons/tag_orange.png" alt="Photo Tag" title="Photo Tag" />&nbsp;<a href="/office/gallery/change_tag/<?php echo(xml_escape($tag['tag_name'])); ?>"><?php echo(xml_escape($tag['tag_name'])); ?></a></span>
					<?php } ?>
				</div>
				<div id="tags_addbutton">
					<a href="#" onclick="return togglePrompt(true);">
						<img src="/images/icons/button_addtag.png" alt="Add Tag" />
					</a>
				</div>
				<div id="tags_addprompt" style="display:none">
					<img src="/images/icons/tag_blue_add.png" alt="Add Tag" style="float:left;margin:0.4em 0" />
					<input type="text" name="add_tag_name" id="add_tag_name" value="" style="font-size:x-small" onkeydown="return processKey(event);" />
					<input type="button" name="add_tag_submit" id="add_tag_submit" value="Add" style="font-size:x-small" onclick="return addTag();" />
				</div>
				<div id="tags_adding" style="display:none">
					<img src="/images/prototype/prefs/loading.gif" alt="Loading" title="Loading" /> Adding new tag...
				</div>
			</div>
			<label for="public_gallery">Show in Public Gallery: </label>
			<input type="checkbox" name="public_gallery" id="public_gallery" value="show"<?php if ($photo->public_gallery == 1) echo(' checked="checked"'); ?> />
			<br />
			<label for="hidden">Deleted: </label>
			<input type="checkbox" name="hidden" id="hidden" value="hide"<?php if ($photo->deleted == 1) echo(' checked="checked"'); ?> />
			<br />
			<label for="hidden-gallery">Removed from Gallery: </label>
			<input type="checkbox" name="hidden-gallery" value="hide"<?php if ($photo->gallery == 1) echo(' checked="checked"'); ?> />
			<br />
			<input type="submit" class="button" name="save_photo_details" value="Save" />
		</fieldset>
	</form>
</div>

<div class="BlueBox">
	<div class="search_options">
		<a href="/office/gallery/edit/<?php echo($photo->id); ?>">
			<img src="/images/icons/button_recrop.png" alt="Re-Crop this thumbnail" />
		</a>
		<a href="/photos/full/<?php echo($photo->id); ?>">
			<img src="/images/icons/button_fullsize.png" alt="View Full Image" />
		</a>
	</div>
	<h2>thumbnails</h2>
	<div>
		<label for="image_types" style="clear:none;width:auto">Thumbnail Type: </label>
		<select name="image_types" id="image_types" size="1" onchange="switchThumbnail(this);">
			<?php for ($i = 0; $i < count($image_types); $i++) { ?>
				<option value="<?php echo(xml_escape($image_types[$i]->codename)); ?>"<?php if ($i == (count($image_types)-1)) echo(' selected="selected"'); ?>><?php echo(xml_escape($image_types[$i]->name)); ?></option>
			<?php } ?>
		</select>
		<div class="clear"></div>
	</div>
	<img id="thumbnail_preview" src="/photos/<?php echo($image_types[(count($image_types)-1)]->codename); ?>/<?php echo($photo->id); ?>" alt="<?php echo(xml_escape($photo->title)); ?>" title="<?php echo(xml_escape($photo->title)); ?>" />
</div>

<script type="text/javascript">
function switchThumbnail (control) {
	var size = control.options[control.selectedIndex].value;
	document.getElementById('thumbnail_preview').src = '/photos/' + size + '/<?php echo($photo->id); ?>';
}

function togglePrompt (show) {
	var control = document.getElementById('tags_addbutton');
	var control2 = document.getElementById('tags_addprompt');
	if (show) {
		control2.style.display = 'block';
		control.style.display = 'none';
		document.getElementById('add_tag_name').focus();
	} else {
		control.style.display = 'block';
		control2.style.display = 'none';
		document.getElementById('tags_adding').style.display = 'none';
	}
	return false;
}

function processKey (e) {
	var e = (window.event) ? e : e;
	if (e.keyCode == 13) {
		// Enter key was pressed
		addTag();
		return false;
	} else if (e.keyCode == 32) {
		// Space bar was pressed
		alert('Spaces are not allowed to be entered.\nThis is because each tag should be a single word.\nPlease try entering a suitable tag again.');
		return false;
	} else {
		return true;
	}
}

function addTag () {
	document.getElementById('tags_adding').style.display = 'block';
	document.getElementById('tags_addprompt').style.display = 'none';
	var suggestion = document.getElementById('add_tag_name');
	if (suggestion.value != '') {
		xajax_tag_suggest(suggestion.value);
	} else {
		processTag();
	}
	suggestion.value = '';
	return false;
}

function clearTags () {
	document.getElementById('tags_existing').innerHTML = '';
}

function createTag (new_tag) {
	var tag_img = document.createElement('img');
	tag_img.src = '/images/icons/tag_orange.png';
	tag_img.alt = 'Photo Tag';

	var tag_link = document.createElement('a');
	tag_link.href = '/office/gallery/change_tag/' + new_tag;
	tag_link.appendChild(document.createTextNode(new_tag));

	var container = document.createElement('span');
	container.appendChild(tag_img);
	container.appendChild(document.createTextNode(' '));
	container.appendChild(tag_link);

	var existing = document.getElementById('tags_existing');
	existing.appendChild(container);
	existing.appendChild(document.createTextNode(' '));
}

function processTag () {
	togglePrompt(false);
}
</script>