<div class="RightToolbar">
	<h4>Quick Links</h4>
	<a href="/office/charity/#">Return to content</a>
</div>

<div class="blue_box">
	<h2>charity info</h2>
	<form class="form" action="/office/howdoi/#" method="post" >
		<fieldset>
			<label for"a_title">Title:</label>
			<input type="text" name="a_title" value="blah" />
			<label for="a_goal">Goal Total:</label>
			<input type="text" name="a_goal" value="15000" />
			<label for"a_goaltext">Goal Text:</label>
			<textarea name="a_goaltext" rows="5" cols="30" />Cool Runnings is a 1993 comedy film directed b</textarea>
			<input type="submit" value="Modify" class="button" name="r_submit_modify" />

		</fieldset>
	</form>
</div>

<div class="grey_box">
	<h2>writers</h2>
	<table width="90%" cellpadding="3" align="center">
		<tr>
			<th width="45%">Name</td>
			<th width="30%">Status</td>
			<th width="25%">Options</td>
		</tr>
		<tr>
			<td>Daniel Ashby</td>
			<td>requested</td>
			<td>
				<form class="form" action="/office/charity/#" method="post" ><fieldset>
					<input type="hidden" name="r_articleid" value="67" >
					<input type="hidden" value="439" class="button" name="r_userid" />
					<input type="submit" value="Remove" class="button" name="r_submit_remove" />
				</fieldset></form>
			</td>
		</tr>
	</table>
	<form class="form" action="/office/charity/#" method="post" >
		<fieldset>
			<label for="a_addwriter">Add New Writer:</label>
			<select name="a_addwriter"><option value="434">James Hogan</option><option value="2">Nick Evans</option><option value="436">Richard Ingle</option></select>
			<input type="submit" value="Add" class="button" name="r_submit_add" />
		</fieldset>
	</form>
</div>


<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>
