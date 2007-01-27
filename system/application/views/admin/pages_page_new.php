<div id='custom_page' align='center'>
	<form name='custom_page_form' action='/admin/pages/newpage/save' method='POST' class='form'>
		<fieldset>
			<label for='codename'>Codename:</label>
			<input name='codename'>
			<br />
			<label for='title'>Title:</label>
			<input name='title'>
			<br />
			<label for='description'>Description</label>
			<input name='description'>
			<br />
			<label for='keywords'>Keywords</label>
			<input name='keywords'>
			<br />
			<label for='comments'>Comments</label>
			<input type='checkbox' name='comments' id="comments">
			<br />
			<label for='ratings'>Ratings</label>
			<input type='checkbox' name='ratings' id="ratings">
			<br />
			<label for='preview_button'></label>
			<input type='submit' class='button' name='preview_button' value='Preview'>
			&nbsp;
			<input type='submit' class='button' name='save_button' value='Save'>
		</fieldset>
	</form>
	<a href='/admin/pages'>Back to Pages Administration</a>
</div>