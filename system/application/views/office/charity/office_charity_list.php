<div class="RightToolbar">
	<h4>Write Requests</h4>
	<h4>Accepted Requests</h4>
</div>
<div class="grey_box">
	<h2>charities</h2>
	<?php
	foreach ($charities as $charity)
	{
		echo '<form class="form" action="/office/charity/setcurrent" method="post" >
		<fieldset>
			<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
			<input type="hidden" name="r_charityid" value="'.$charity['id'].'" />
		</fieldset>
		<fieldset>
			<label for="r_submit_delete"><a href="/office/charity/edit/'.$charity['id'].'">'.$charity['name'].'</a></label>
			<input type="submit" value="Delete" class="button" name="r_submit_delete" />';
		if ($charity['iscurrent'] == 1)
			echo '<input type="submit" value="Current" class="disabled_button" name="r_submit_makecurrent" disabled />';
		else
			echo '<input type="submit" value="Make Current" class="button" name="r_submit_makecurrent" />';
		echo '</fieldset>
	</form>';
	}
	?>
</div>

<div class="blue_box">
	<h2>add charity</h2>
	<form class="form" action="/office/charity/addcharity" method="post" >
		<fieldset
			<?php
			echo '<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />';
			?>
			<label for="a_charityname">Name:</label>
			<input type="text" name="a_charityname" id="a_charityname" />
			<input type="submit" value="Add" class="button" name="r_submit_add" onclick="addCharity();" />
		</fieldset>
	</form>
</div>

<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>
