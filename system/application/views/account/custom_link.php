<div id="RightColumn">
	<h2 class="first">Link Nomination</h2>
	<div class="Entry">
			You may <b>nominate</b> your link for addition to the main list. Please note that we do not accept personal homepages or other minor sites.
	</div>
</div>

<script type="text/javascript">
<!--
function radio_click() {
	var rad_gallery = document.getElementById('rad_gallery');
	var rad_custom = document.getElementById('rad_custom');
	var div_gallery = document.getElementById('div_gallery');
	var div_custom = document.getElementById('div_custom');
	div_gallery.style.display = (rad_gallery.checked ? 'block' : 'none');
	div_custom.style.display = (rad_custom.checked ? 'block' : 'none');
}

function validate_form() {
	var rad_gallery = document.getElementById('rad_gallery');
	var chosen_image = document.getElementById('chosen_image');
	if (rad_gallery.checked && chosen_image.value == '') {
		alert("Please select an image from the gallery.");
		return false;
	}
	return true;
}

function select_image(id) {
	alert('Image selected');
	document.getElementById('chosen_image').value=id;
	return false;
}
-->
</script>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>Add Custom Link</h2>
		<p>To add a link that is not in the main list, enter it here. Nominating a link means it will get considered for becoming official, with a proper icon.</p>
		<form action="/account/customlink/store" method="post" enctype="multipart/form-data" onsubmit="return validate_form();">
		<fieldset>
			<label for="title1"> Link Name: </label>
			<input type="text" name="title1" id="title1" />
			<br />
			<label for="lurl"> Link URL: </label>
			<input type="text" name="lurl"  id="lurl" value="http://" />
			<br />
			<label for="lnominate"> Nominate Link: </label>
			<input type="checkbox" name="lnominate" id="lnominate" checked="checked" />
			<br style="clear: both;"/>
			<label> Link Image: </label>
			<input style="float:none;" type="radio" onclick="radio_click();" id="rad_gallery" name="image_pick" value="gallery" checked="checked" /> Gallery
			<input style="float:none;" type="radio" onclick="radio_click();" id="rad_custom" name="image_pick" value="custom"/> Upload Custom
			<br style="clear: both;"/>
			<div style="width: 100%; height: 260px;" id="div_gallery">
			<?php if($gallery_images->num_rows() > 0) {
				foreach ($gallery_images->result() as $image) {?>
				<div style="display:inline;padding: 10px;">
					<a href="#" onclick="return select_image(<?=$image->image_id?>);">
					<?=$this->image->getImage($image->image_id, $image->image_image_type_id)?>
					</a>
				</div>
				<?php } ?>
			<?php } else { ?>
					<p>There are currently no images in the gallery.</p>
			<?php } ?>
			<input type="hidden" name="chosen_image" id="chosen_image" value="" />
			</div>
			<div id="div_custom" style="display :none;">
			<p>Please select an image to be used for this link. The image must be larger than 50x50 pixels.</p>
			<label for="userfile1">File Location: </label>
			<input type="file" name="userfile1" id="userfile1" size="20" />
			<input type="hidden" name="destination" id="destination" value="1" />
			</div>
			<div style="clear: both;"/>
			<input type="submit" value="Finish" class="button" /> <input type="button" value="Back" class="button" onclick="window.location='/account/links';" />
		</fieldset>
		</form>
	</div>
</div>
