<script type="text/javascript">
function addPhoto (path, img_preview) {
	img_preview.className = (img_preview.className == 'selected') ? '' : 'selected';
	var store = document.getElementById('selected_photos');
	var photos = new Array();
	// Check if image is already selected
	for (var x = 0; x < store.options.length; x++) {
		if (store.options[x].value != path) {
			// If it is, deselect it
			photos[photos.length] = store.options[x].value;
		}
	}
	// If no images were deselected then must be selecting a deselected image
	if (store.options.length == photos.length) {
		photos[photos.length] = path;
	}
	// Clear form input
	store.options.length = 0;
	// Re-populate form input with selected photos
	for (var y = 0; y < photos.length; y++) {
		store.options[store.options.length] = new Option(photos[y], photos[y], true);
	}
	var select_button = document.getElementById('selected_button');
	if (store.options.length == 0) {
		select_button.value = 'Select Photos to Add';
		select_button.disabled = true;
	} else {
		select_button.value = 'Add ' + store.options.length + ' Photo' + ((store.options.length > 1) ? 's' : '');
		select_button.disabled = false;
	}
}

function getProperties () {
	var store = document.getElementById('selected_photos');
	for (var x = 0; x < store.options.length; x++) {
		cloneProperties(x, store.options[x].value);
	}
	document.getElementById('page2').className = 'show';
	document.getElementById('page1').className = 'hide';
}

function cloneProperties (count, path) {
	var newClone = document.getElementById('clone_source').cloneNode(true);
	newClone.id = newClone.id + count;
	newClone.style.display = 'block';
	cloneAttributes(count, newClone);

	var container = document.getElementById('clone_container');
	container.appendChild(newClone);
	document.getElementById('p_photo' + count).src = '/office/gallery/mass_upload_preview/' + path;
}

function cloneAttributes (count, newClone) {
	var newField = newClone.childNodes;
	for (var i = 0; i < newField.length; i++) {
		if (newField[i].name)
			newField[i].name = newField[i].name + count;
		if (newField[i].id)
			newField[i].id = newField[i].id + count;
		if (newField[i].nodeType == '1') if (newField[i].getAttribute('for')) //needs to be asked in order
			newField[i].setAttribute('for', newField[i].getAttribute('for') + count);
		cloneAttributes(count, newField[i]);
	}
}

function copyGlobal () {
	var x = 0;
	var photo = document.getElementById('clone_source' + x);
	while ((photo != null) && (photo != undefined)) {
		document.getElementById('p_title' + x).value = document.getElementById('g_title').value;
		document.getElementById('p_photo_source' + x).value = document.getElementById('g_photo_source').value;
		document.getElementById('p_watermark' + x).value = document.getElementById('g_watermark').value;
		document.getElementById('p_tags' + x).value = document.getElementById('g_tags').value;
		document.getElementById('p_watermark_colour' + x).selectedIndex = document.getElementById('g_watermark_colour').selectedIndex;
		document.getElementById('p_public_gallery' + x).checked = document.getElementById('g_public_gallery').checked;
		x++;
		photo = document.getElementById('clone_source' + x);
	}
}
</script>

<div id="RightColumn">
	<h2 class="first"><?php echo(xml_escape($intro_heading)); ?></h2>
	<?php echo($intro_text); ?>
</div>

<form action="/office/gallery/mass_upload_process" method="post">
	<div id="MainColumn">
		<div id="page1" class="BlueBox">
			<h2>mass photo upload</h2>
			<p>Select the photos you wish to add to the gallery.</p>
<?php if (count($files) == 0) { ?>
			<p><b>No photos have been found! Make sure you have uploaded the photos to the correct location and try again!</b></p>
<?php } else { ?>
			<div id="massupload_selection">
				<?php foreach ($files as $file) { ?>
					<div onclick="addPhoto('<?php echo(substr($file, 1)); ?>', this);" style="background-image:url('/office/gallery/mass_upload_preview<?php echo($file); ?>');" alt="<?php echo(substr($file, 1)); ?>" title="<?php echo(substr($file, 1)); ?>">
						<img src="/images/icons/add.png" alt="Selected" />
					</div>
				<?php } ?>
				<select name="selected_photos[]" id="selected_photos" multiple="multiple" size="10"></select>
				<input type="button" id="selected_button" value="Select Photos to Add" disabled="disabled" class="button" onclick="getProperties();" style="clear:both" />
			</div>
<?php } ?>
			<div style="clear: both"></div>
		</div>
	
		<div id="page2" class="hide">
			<div class="BlueBox">
				<h2>global properties</h2>
				<div>
					<label for="g_title">Photo Title: </label>
					<input type="text" name="g_title" id="g_title" size="32" />
					<label for="g_photo_source">Photo Source: </label>
					<input type="text" name="g_photo_source" id="g_photo_source" size="32" />
					<label for="g_watermark">Watermark: </label>
					<input type="text" name="g_watermark" id="g_watermark" size="32" />
					<label for="g_watermark_colour">Watermark Text Colour: </label>
					<select name="g_watermark_colour" id="g_watermark_colour" size="1">
<?php foreach ($watermark_colours as $colour) { ?>
						<option value="<?php echo($colour->id); ?>"><?php echo($colour->name); ?></option>
<?php } ?>
					</select>
					<label for="g_tags">Tags: </label>
					<input type="text" name="g_tags" id="g_tags" size="32" />
					<label for="g_public_gallery">Show in Public Gallery: </label>
					<input type="checkbox" name="g_public_gallery" id="g_public_gallery" value="show" />
					<input type="button" id="g_applyall" value="Apply To All" class="button" onclick="copyGlobal();" />
				</div>
			</div>
		
			<div class="BlueBox">
				<h2>photo properties</h2>
				<div id="clone_container">
					<div id="clone_source" class="photo_props hide">
						<img id="p_photo" src="/office/gallery/mass_upload_preview/roses.jpg" />
						<div>
							<label for="p_title">Photo Title: </label>
							<input type="text" name="p_title" id="p_title" size="32" />
							<label for="p_photo_source">Photo Source: </label>
							<input type="text" name="p_photo_source" id="p_photo_source" size="32" />
							<label for="p_watermark">Watermark: </label>
							<input type="text" name="p_watermark" id="p_watermark" size="32" />
							<label for="p_watermark_colour">Watermark Text Colour: </label>
							<select name="p_watermark_colour" id="p_watermark_colour" size="1">
<?php foreach ($watermark_colours as $colour) { ?>
								<option value="<?php echo($colour->id); ?>"><?php echo($colour->name); ?></option>
<?php } ?>
							</select>
							<label for="p_tags">Tags: </label>
							<input type="text" name="p_tags" id="p_tags" size="32" />
							<label for="p_public_gallery">Show in Public Gallery: </label>
							<input type="checkbox" name="p_public_gallery" id="p_public_gallery" value="show" />
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<input type="submit" id="g_add" name="g_add" value="Add To Gallery" class="button" />
			</div>
		</div>
	</div>
</form>