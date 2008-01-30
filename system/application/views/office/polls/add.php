<div id="RightColumn">
	<h2 class="first">
		Quick Links
	</h2>
	<div class="Entry">
		<a href="/office/">Office Home</a>
	</div>
</div>

<div class="blue_box">
	<h2>add a new poll</h2>
	<form class="form" action="/office/polls/add" method="post">
		<fieldset>
			<label for="poll_question">Question: </label>
			<input type="text" id="poll_question" name="poll_question" style="width: 250px" />
		</fieldset>
		<fieldset>
			<input type="submit" name="submit_add_poll" value="Add" />
		</fieldset>
	</form>
</div>

<?php
/*
	echo('<div class="BlueBox"><pre>');
	print_r($data);
	echo('</pre></div>');
*/
?>