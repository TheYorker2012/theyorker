<div class="RightToolbar">
	<h4>Areas for Attention</h4>
	<div class="Entry">
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b>3</b> <a href='#'>Questions</a> that are waiting to be published.
		</div>
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b>3</b> <a href='#'>Suggestions</a> that require attention.
		</div>
	</div>
</div>
<div class="grey_box">
	<h2>edit category</h2>
	<form class="form" action="/office/howdoi/categoryadd" method="post" >
		<fieldset>
			<label for="title">Name: </label>
			<input type="text" name="a_name" />
			<label for="codename">Codename: </label>
			<input type="text" name="a_codename" />
			<label for="blurb">Category Blurb: </label>
			<textarea name="a_blurb">POO</textarea>
			<input type="submit" class="button" value="Save" />
		</fieldset>
	</form>
</div>

<?php

echo '<pre>';
echo print_r($_POST);
echo '</pre>';

?>
