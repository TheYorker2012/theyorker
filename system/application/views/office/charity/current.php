<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
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
				echo('				<option value="'.$charity['id'].'" selected="selected">'.xml_escape($charity['name']).'</option>'."\n");
			else
				echo('				<option value="'.$charity['id'].'">'.xml_escape($charity['name']).'</option>'."\n");
		}
	?>
				</select>
			</fieldset>
			<fieldset>
				<input type="submit" value="Set As Current Charity" class="button" name="r_submit_set_current" />
			</fieldset>
		</form>
	</div>
</div>
