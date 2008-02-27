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
					<input type="text" name="a_name" size="31" value="<?php echo(xml_escape($podcast['name'])); ?>" />
				<label for="a_description">Description: </label>
					<textarea name="a_description" rows="7" cols="33"><?php echo(xml_escape($podcast['description'])); ?></textarea>
				<label for="file_address">File Address:</label>
					<input	
						type="text"
						name="file_address"
						size="31"
						readonly="readonly"
						value="<?php echo(
									$this->config->item('static_web_address').
									'/media/podcasts/'.
									xml_escape($podcast['file'])); ?>">
				<label for="file_size">File Size:</label>
					<input	
						type="text"
						name="file_size"
						size="10"
						readonly="readonly"
						value="<?php echo(round($podcast['file_size']/1048576,1).' MBytes'); ?>">
				<br />
				<div style="float:left; margin: 5px;width:150px">
					<a href="/office/podcasts">Return to Podcasts</a>
				</div>
				<div style="float:right; margin: 5px;width:120px">
					<div style="float:left; margin: 5px">
						<a href="<?php echo(
								$this->config->item('static_web_address').
								'/media/podcasts/'.
								xml_escape($podcast['file'])); ?>">Play</a>
					</div>
					<input type="submit" class="button" value="Save" name="r_submit_save" />
				</div>
			</fieldset>
		</form>
	</div>
</div>
