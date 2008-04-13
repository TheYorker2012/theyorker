<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>edit request</h2>
		<form class="form" action="/office/charity/domodify" method="post" >
			<fieldset>
				<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="<?php echo($_SERVER['REQUEST_URI']); ?>" />
				<input type="hidden" name="r_charityid" value="<?php echo($parameters['charity_id']); ?>" />
			</fieldset>
			<fieldset>
				<label for="a_title">Title:</label>
				<input type="text" name="a_title" size="30" value="<?php echo(xml_escape($article['header']['requesttitle'])); ?>" />
				<label for="a_description">Description:</label>
				<textarea name="a_description" rows="10" cols="50" /><?php echo(xml_escape($article['header']['requestdescription'])); ?></textarea><br />
			</fieldset>
			<fieldset>
				<input type="submit" value="Save" class="button" name="r_submit_save_request" />
			</fieldset>
		</form>
	</div>
	<a href="/office/charity/editarticle/<?php echo($parameters['charity_id']); ?>">Back To Article</a>
</div>