<div class="BlueBox">
	<form action="<?= isset($action_url) ? $action_url : site_url($this->uri->uri_string())?>" method="post" onsubmit="return ValidateClones();" enctype="multipart/form-data">
		<h2>image upload</h2>
		<p> Please choose the image you wish up upload here. The file is limited to 2Mb in size, and must be larger than the image size required.</p>
		<div>
			<label for="title1">Title / ALT Text: </label>
			<input type="text" name="title1" id="title1" size="32" />
			<label for="userfile1">File Location: </label>
			<input type="file" name="userfile1" id="userfile1" size="20" />
		</div>
		<div style="clear: both"></div>
		<p>When you have selected a file, click the button below to proceed.</p>
		<fieldset>
			<input type="hidden" name="destination" id="destination" value="1" />
			<input type="submit" class="button" value="Upload" />
		</fieldset>
	</form>
</div>
