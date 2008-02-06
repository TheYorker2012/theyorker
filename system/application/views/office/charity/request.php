<?php
	//sidebar
	echo('<div class="RightToolbar">'."\n");
	echo('	<h4>Quick Links</h4>'."\n");
	echo('	<div class="Entry">'."\n");
	echo('		<a href="/office/charity/">Back To Charity Index</a>'."\n");
	echo('		<br />'."\n");
	echo('		<a href="/office/charity/editarticle/'.$parameters['charity_id'].'">Back To Article</a>'."\n");
	echo('	</div>'."\n");
	echo('</div>'."\n");
	
	//main - request info
	echo('<div class="blue_box">'."\n");
	echo('	<h2>edit request</h2>'."\n");
	echo('	<form class="form" action="/office/charity/domodify" method="post" >'."\n");
	echo('		<fieldset>'."\n");
	echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
	echo('			<input type="hidden" name="r_charityid" value="'.$parameters['charity_id'].'" />'."\n");
	echo('		</fieldset>'."\n");
	echo('		<fieldset>'."\n");
	echo('			<label for="a_title">Title:</label>'."\n");
	echo('			<input type="text" name="a_title" size="30" value="'.$article['header']['requesttitle'].'" />'."\n");
	echo('			<label for="a_description">Description:</label>'."\n");
	echo('			<textarea name="a_description" rows="10" cols="56" />'.$article['header']['requestdescription'].'</textarea><br />'."\n");
	echo('		</fieldset>'."\n");
	echo('		<fieldset>'."\n");
	echo('			<input type="submit" value="Save" class="button" name="r_submit_save_request" />'."\n");
	echo('		</fieldset>'."\n");
	echo('	</form>'."\n");
	echo('</div>'."\n");
?>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>
