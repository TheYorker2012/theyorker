<div id="RightColumn">
	<h2 class="first">Link Nomination</h2>
	<div class="Entry">
			You may nominate your link for addition to the list. Please note that we do not accept personal homepages or other minor sites.
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
-->
</script>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>Add Custom Link</h2>
		<p>To add a link that is not in the list, enter it here. Nominating a link means it will get considered for becoming official, with a proper icon.</p>
		<form action="/account/customlink/store" method="post" enctype="multipart/form-data">
		<fieldset>
			<label for="title1"> Link Name: </label>
			<input type="text" name="title1" value="" default />
			<br />
			<label for="lurl"> Link URL: </label>
			<input type="text" name="lurl" value="http://" />
			<br />
			<label for="lnominate"> Nominate Link: </label>
			<input type="checkbox" name="lnominate" checked/>
			<br style="clear: both;"/>
			<label for="image_pick"> Link Image: </label>
			<input style="float:none;" type="radio" onclick="radio_click();" id="rad_gallery" name="image_pick" value="gallery" checked/> Gallery
			<input style="float:none;" type="radio" onclick="radio_click();" id="rad_custom" name="image_pick" value="custom"/> Upload Custom
			<br style="clear: both;"/>
			<div style="width: 260px; height: 260px;" id="div_gallery">
			Gallery
			</div>
			<div id="div_custom" style="display :none;">
			<p>Please select an image to be used for this link. The image must be larger than 50x50 pixels.</p>
			<label for="userfile1">File Location: </label>
			<input type="file" name="userfile1" size="20" />
			<br style="clear: both;"/>
			</div>
			<input type="submit" value="Finish" class="button"> <input type="button" value="Back" class="button" onClick="window.location='/account/links';">
		</fieldset>
		</form>
	</div>
</div>
