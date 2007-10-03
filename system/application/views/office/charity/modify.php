<div class="RightToolbar">
	<h4>Quick Links</h4>
	<?php
	echo '<a href="/office/charity/editinfo/'.$charity['id'].'">Return to content</a>';
	?>
</div>

<div class="blue_box">
	<h2>charity info</h2>
	<form class="form" action="/office/charity/domodify" method="post" >
		<fieldset>
			<?php
			echo '<input type="hidden" name="a_charityid" value="'.$charity['id'].'" />
			<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
			<label for"a_name">Name:</label>
			<input type="text" name="a_name" value="'.$charity['name'].'" />
			<label for="a_goal">Goal Total:</label>
			<input type="text" name="a_goal" value="'.$charity['target'].'" />
			<input type="submit" value="Modify" class="submit" name="r_submit_modify" />';
			?>
		</fieldset>
	</form>
</div>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>
