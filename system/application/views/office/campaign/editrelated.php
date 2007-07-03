<?php
	//sidebar
	echo('<div class="RightToolbar">'."\n");
	echo('	<h4>Sidebar</h4>'."\n");
	echo('</div>'."\n");
	
	//main - request info
	echo('<div class="blue_box">'."\n");
	echo('	<h2>web links</h2>'."\n");
	echo('	<form class="form" action="/office/campaign/articlemodify" method="post" >'."\n");
	echo('		<fieldset>'."\n");
	echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
	echo('			<input type="hidden" name="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
	echo('			<input type="hidden" id="r_linkcount" name="r_linkcount" value="1" />'."\n");
	echo('		</fieldset>'."\n");
	echo('		<fieldset>'."\n");
	foreach($article['links'] as $key => $link)
	{
		$num = ++$key;
		echo('			<input type="hidden" name="a_id_'.$num.'" value="'.$link['id'].'" />'."\n");
		echo('			<label for="a_name_'.$num.'">Name '.$num.':</label>'."\n");
		echo('			<input type="text" name="a_name_'.$num.'" size="30" value="'.$link['name'].'" />'."\n");
		echo('			<label for="a_url_'.$num.'">Address '.$num.':</label>'."\n");
		echo('			<input type="text" name="a_url_'.$num.'" size="30" value="'.$link['url'].'" />'."\n");
		echo('			<label for="a_delete_'.$num.'">Delete</label>'."\n");
		echo('			<input type="checkbox" name="a_delete_'.$num.'" />'."\n");
		echo('			<br /><br />');
	}
	echo('			<label for="a_header_new">New Header</label>'."\n");
	echo('			<input type="text" name="a_header_new" size="30" value="" />'."\n");
	echo('			<label for="a_link_new">New Link</label>'."\n");
	echo('			<input type="text" name="a_link_new" size="30" value="http://" />'."\n");
	echo('		</fieldset>'."\n");
	echo('		<fieldset>'."\n");
	echo('			<input type="submit" value="Save" class="button" name="r_submit_save_links" />'."\n");
	echo('		</fieldset>'."\n");
	echo('	</form>'."\n");
	echo('</div>'."\n");
?>

<?php
echo('<pre><div class=BlueBox>');
print_r($data);
echo('</div></pre>');
?>