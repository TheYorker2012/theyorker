<p><em>Note: this doesn't yet actually delete</em></p>
<?php if (!$complete) { ?>
	<form name='delete_confirm_form' action='<?php echo $target; ?>' method='POST' class='form'>
		<fieldset>
			Are you sure you want to delete this page:
			page details
			<br />
			<input type='submit' class='button' name='confirm_delete' value='Yes, Delete' />
		</fieldset>
	</form>
<?php } else { ?>
	<p>The page was successfully deleted</p>
<?php } ?>
<a href='/admin/pages'>Back to Pages Administration</a>