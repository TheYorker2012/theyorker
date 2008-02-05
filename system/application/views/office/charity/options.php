<div class="RightToolbar">
	<h4>Quick Links</h4>
	<div class="Entry">
		<a href="/office/charity/">Back To Charity List</a>
	</div>
</div>

<div class="blue_box">
	<h2>options</h2>
<?php
	if ($current_charity == $parameters['charity_id'])
	{
		echo('	This charity is the current charity. Click the button to remove it as current.'."\n");
		echo('	<form class="form" action="/office/charity/domodify" method="post">'."\n");
		echo('		<fieldset>'."\n");
		echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />');
		echo('			<input type="hidden" name="r_charityid" id="r_charityid" value="'.$parameters['charity_id'].'" />');
		echo('		</fieldset>'."\n");
		echo('		<fieldset>'."\n");
		echo('			<input type="submit" value="Remove as Current" class="button" name="r_submit_remove_current" />'."\n");
		echo('		</fieldset>'."\n");
		echo('	</form>'."\n");
	}
	else
	{
		echo('	Set this charity as the current charity'."\n");
		echo('	<form class="form" action="/office/charity/domodify" method="post">'."\n");
		echo('		<fieldset>'."\n");
		echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />');
		echo('			<input type="hidden" name="r_charityid" id="r_charityid" value="'.$parameters['charity_id'].'" />');
		echo('		</fieldset>'."\n");
		echo('		<fieldset>'."\n");
		echo('			<input type="submit" value="Set As Current" class="button" name="r_submit_set_current" />'."\n");
		echo('		</fieldset>'."\n");
		echo('	</form>'."\n");
	}
?>
</div>
