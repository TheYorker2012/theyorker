<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<?php
	echo('<div class="BlueBox">'."\n");
	echo('	<h2>web links</h2>'."\n");
	echo('	<form class="form" action="/office/campaign/articlemodify" method="post" >'."\n");
	echo('		<fieldset>'."\n");
	echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
	echo('			<input type="hidden" name="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
	echo('			<input type="hidden" id="r_linkcount" name="r_linkcount" value="'.count($article['links']).'" />'."\n");
	echo('		</fieldset>'."\n");
	echo('		<fieldset>'."\n");
	foreach($article['links'] as $key => $link)
	{
		$num = ++$key;
		echo('			<input type="hidden" name="a_id_'.$num.'" value="'.$link['id'].'" />'."\n");
		echo('			<label for="a_name_'.$num.'">Name '.$num.':</label>'."\n");
		echo('			<input type="text" name="a_name_'.$num.'" size="30" value="'.xml_escape($link['name']).'" />'."\n");
		echo('			<label for="a_url_'.$num.'">Address '.$num.':</label>'."\n");
		echo('			<input type="text" name="a_url_'.$num.'" size="30" value="'.xml_escape($link['url']).'" />'."\n");
		echo('			<label for="a_delete_'.$num.'">Delete</label>'."\n");
		echo('			<input type="checkbox" name="a_delete_'.$num.'" />'."\n");
		echo('			<br /><br />');
	}
	echo('			<label for="a_name_new">New Name</label>'."\n");
	echo('			<input type="text" name="a_name_new" size="30" value="" />'."\n");
	echo('			<label for="a_url_new">New Address</label>'."\n");
	echo('			<input type="text" name="a_url_new" size="30" value="http://" />'."\n");
	echo('		</fieldset>'."\n");
	echo('		<fieldset>'."\n");
	echo('			<input type="submit" value="Save" class="button" name="r_submit_save_links" />'."\n");
	echo('		</fieldset>'."\n");
	echo('	</form>'."\n");
	echo('</div>'."\n");
	?>
	<a href="/office/campaign/">Back To Campaign Index</a>
</div>