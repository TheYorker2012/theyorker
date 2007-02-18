<div class="RightToolbar">
	<h4>Quick Links</h4>
	<a href="/office/charity/#">Return to content</a>
</div>

<div class="blue_box">
	<h2>progress report</h2>
	<form class="form" action="/office/howdoi/#" method="post" >
		<fieldset>
			<label for="a_date">Date:</label>
			<input type="text" name="a_date" value="03/02/2007" disabled /><br />
			<label for"a_report">Report:</label>
			<textarea name="a_report" rows="5" cols="30" />This is a test for a random charity progress report.</textarea>
			<input type="submit" value="Save" class="button" name="r_submit_prsave" />

		</fieldset>
	</form>
</div>

<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>
