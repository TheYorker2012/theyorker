<div class="RightToolbar">
	<h4>Quick Links</h4>
	A Link
</div>

<div class="blue_box">
	<h2>set current charity</h2>
	delete this page?
	<form class="form" action="/office/charity/charitymodify" method="post">
		<fieldset>
<?php
	echo('			<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
?>
		</fieldset>
		<fieldset>
			<label for="a_charitylist">Select Charity:</label>
			<select name="a_charitylist" id="a_charitylist">
<?php
	foreach ($charities as $charity)
	{
		if($charity['iscurrent'])
			echo('				<option value="'.$charity['id'].'" selected="selected">'.$charity['name'].'</option>'."\n");
		else
			echo('				<option value="'.$charity['id'].'">'.$charity['name'].'</option>'."\n");
	}
?>
			</select>
		</fieldset>
		<fieldset>
			<input type="submit" value="Set As Current Charity" class="button" name="r_submit_set_current" />
		</fieldset>
	</form>
</div>

<?php
/*
echo '<div class="BlueBox"><pre>';
echo print_r($data);
echo '</pre></div>';
*/
?>
