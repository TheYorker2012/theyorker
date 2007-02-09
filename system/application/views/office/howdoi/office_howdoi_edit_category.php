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
	<h4>Question Categories</h4>
	<div class="Entry">
	<a href="/office/howdoi/editquestion">Opening Times</a><br />
	<a href="/office/howdoi/editquestion">Numbers</a><br />
	<a href="/office/howdoi/editquestion">Essentials</a><br />
	<a href="/office/howdoi/editquestion">Other Info</a><br />
	<a href="/office/howdoi/editquestion">The Nearest ...</a><br />
	</div>
</div>
<div class="grey_box">
	<h2>add category</h2>
	<form class="form" action="/office/howdoi/categoryadd" method="post" >
		<fieldset>
			<label for="title">Name: </label>
			<input type="text" name="title" />
			<input type="submit" class="button" value="Create" />
		</fieldset>
	</form>
</div>
<div class="blue_box">
	<h2>edit categories</h2>
	Type in a name next to the category to rename it - otherwise leave it blank.
</div>

<?php

echo '<pre>';
echo print_r($_POST);
echo '</pre>';

?>