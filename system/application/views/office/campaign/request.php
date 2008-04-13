<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<?php
	echo('<div class="BlueBox">'."\n");
	echo('	<h2>edit request</h2>'."\n");
	echo('	<form class="form" action="/office/campaign/articlemodify" method="post" >'."\n");
	echo('		<fieldset>'."\n");
	echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
	echo('			<input type="hidden" name="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
	echo('		</fieldset>'."\n");
	echo('		<fieldset>'."\n");
	echo('			<label for="a_title">Title:</label>'."\n");
	echo('			<input type="text" name="a_title" size="30" value="'.xml_escape($article['header']['requesttitle']).'" />'."\n");
	echo('			<label for="a_description">Description:</label>'."\n");
	echo('			<textarea name="a_description" rows="10" cols="50" />'.xml_escape($article['header']['requestdescription']).'</textarea><br />'."\n");
	echo('		</fieldset>'."\n");
	echo('		<fieldset>'."\n");
	echo('			<input type="submit" value="Save" class="button" name="r_submit_save_request" />'."\n");
	echo('		</fieldset>'."\n");
	echo('	</form>'."\n");
	echo('</div>'."\n");
	?>
	<a href="/office/campaign/editarticle/<?php echo($parameters['campaign_id']); ?>">Back To Article</a>
</div>