<div id="RightColumn">
	<h2 class="first">Quick Links</h2>
	<div class="Entry">
		
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>podcast details</h2>
		<form class="form" action="<?php echo($target); ?>" method="post" enctype="multipart/form-data">
			<fieldset>
				<label for="a_name">Name: </label>
				<input type="text" name="a_name" size="60" value="<?php echo($podcast['name']); ?>" />
				<label for="a_description">Description: </label>
				<textarea name="a_description" rows="5" cols="55"><?php echo($podcast['description']); ?></textarea>
			</fieldset>
			<br />
			<b>Current File</b>: <a href="<?php echo($this->config->item('static_web_address')); ?>/podcasts/<?php echo($podcast['file']); ?>"><?php echo($podcast['file']); ?></a>
			<br />
			<b>File Size</b>: <?php echo($podcast['file_size']); ?>
			<br />
			<fieldset>
				<label for="a_file">Change File: </label>
				<input type="hidden" name="MAX_FILE_SIZE" value="52428800" />
				<input type="file" name="a_file" id="a_file" size="45" />
			</fieldset>
			<fieldset>
				<input type="submit" class="button" value="Save" name="r_submit_save" />
			</fieldset>
		</form>
	</div>
	<div class="BlueBox">
		<h2>podcast file</h2>
		<form class="form" action="<?php echo($target); ?>" method="post" enctype="multipart/form-data">
			<fieldset>
				<label for="a_file">Filename: </label>
				<input type="file" name="a_file" id="a_file" size="45" />
			</fieldset>
			<fieldset>
				<input type="submit" class="button" value="Upload" name="r_submit_file_upload" />
			</fieldset>
		</form>
	</div>
</div>
