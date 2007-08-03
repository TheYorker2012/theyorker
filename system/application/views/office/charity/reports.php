<div class="RightToolbar">
	<h4>Quick Links</h4>
	<a href="/office/charity/#">Return to content</a>
</div>

<div class="blue_box" id="pr_1">
	<h2>progress report - 05/02/07</h2>
	<form class="form" action="/office/howdoi/#" method="post" >
		<fieldset>
			<label for="title">Info Title:</label>
			<div id="title" class="info">random title 1</div>
			<br />
			<label for="description">Description:</label>
			<div id="description" class="info">a random description 1</div>
			<br />
			<label for="revision">Latest Revision:</label>
			<div id="revision" class="info">a random revision 1</div>
			<br />
			<label for="editor">Editor:</label>
			<div id="editor" class="info">editor 1</div>
			<br />
			<label for="writers">Writers:</label>
			<div id="writers" class="info">writer 1 (accepted)<br / >writer 2 (requested)</div>
			<br />
			<label for="options">Options:</label>
			<div id="options" class="info">
				<input type="submit" value="Edit" class="button" name="r_submit_edit" />
				<input type="submit" value="Assign" class="button" name="r_submit_assign" />
			</div>
			<br />
		</fieldset>
	</form>
</div>

<?php

echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');

?>
