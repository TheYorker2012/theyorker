<div class="RightToolbar">
	<h4>Write Requests</h4>
	<h4>Accepted Requests</h4>
</div>
<div class="grey_box">
	<h2>charities</h2>
	<form class="form" action="/office/charity/#" method="post" >
		<fieldset>
			<label for="r_submit_delete">Our Pockets</label>
			<input type="submit" value="Delete" class="button" name="r_submit_delete" />
			<input type="submit" value="Current" class="disabled_button" name="r_submit_makecurrent" disabled />
		</fieldset>
	</form>
	<form class="form" action="/office/charity/#" method="post" >
		<fieldset>
			<label for="r_submit_delete">Local Hospital</label>
			<input type="submit" value="Delete" class="button" name="r_submit_delete" />
			<input type="submit" value="Make Current" class="button" name="r_submit_makecurrent" />
		</fieldset>
	</form>
</div>

<div class="blue_box">
	<h2>add charity</h2>
	<form class="form" action="/office/charity/#" method="post" >
		<fieldset>
			<label for="a_charityname">Name:</label>
			<input type="text" name="a_charityname" />
			<input type="submit" value="Add" class="button" name="r_submit_add" />
		</fieldset>
	</form>
</div>

<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>
