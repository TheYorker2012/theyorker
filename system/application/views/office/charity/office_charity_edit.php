<div class="RightToolbar">
	<h4>Quick Links</h4>
	<?php
	echo '<a href="/office/charity/article/'.$charity['id'].'">Article</a><br/ >';
	echo '<a href="/office/charity/progressreports/'.$charity['id'].'">Progress Reports</a><br/ >';
	echo '<br/ >';
	?>
	<h4>Areas for Attention</h4>
	You have been requested to answer this question.
	<form class="form" action="/office/charity/#" method="post" >
		<fieldset>
			<input type="submit" value="Accept" class="button" name="r_submit_accept" />
			<input type="submit" value="Decline" class="button" name="r_submit_decline" />
		</fieldset>
	</form>
</div>

<div class="blue_box">
	<h2>charity info</h2>
	<?php
	echo '
	<b>Title: </b>'.$charity['name'].'<br />
	<b>Current Total: </b>'.$charity['current'].'<br />
	<b>Goal Total: </b>'.$charity['target'].'<br />
	<b>Progress To Goal: </b>'.($charity['current']/$charity['target']*100).'%<br />
	<b>Goal Text: </b>'.$charity['target_text'].'<br />
	<a href="/office/charity/#">[Modify]</a>';
	?>
</div>

<div class="grey_box">
	<h2>edit charity</h2>
	<form class="form" action="/charity/howdoi/#" method="post" >
		<fieldset>
			<label for="a_question">Heading:</label>
			<input type="text" name="a_question" value="why we want blah?" /><br />
			<label for="a_answer">Description:</label>
			<textarea name="a_answer" rows="5" cols="30" />Every time you click or view our ads it earns us money and we likes monies. It goes into our pocketsies very nicely. We likes pockets, theyre full of monies from you clicking.</textarea><br />
			<input type="submit" value="Save" class="button" name="r_submit_save" />
		</fieldset>
	</form>
</div>

<div class="blue_box">
	<h2>options</h2>
	<form class="form" action="/office/charity/#" method="post" >
		<fieldset>
			<input type="submit" value="Publish" class="button" name="r_submit_publish" />
		</fieldset>
		<fieldset>
			<input type="submit" value="Unpublish" class="button" name="r_submit_unpublish" />
		</fieldset>
	</form>
</div>

<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>
