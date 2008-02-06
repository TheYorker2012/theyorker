<div id="RightColumn">
	<h2 class="first">
		Quick Links
	</h2>
	<div class="Entry">
		<a href="/office/polls/">Back To Poll List</a>
	</div>
</div>

<div class="blue_box">
	<h2>edit poll choices</h2>
<?php echo('		<form class="form" action="/office/polls/choices/'.$poll_id.'" method="post">'."\n"); ?>
		<fieldset>
<?php
	$choice_count = 0;
	foreach($poll_choices as $choice)
	{
		$choice_count++;
?>
<?php echo('			<label for="poll_choice_name['.$choice['id'].']">Choice #'.$choice_count.': </label>'."\n"); ?>
<?php echo('			<input type="text" id="poll_choice_name['.$choice['id'].']" name="poll_choice_name['.$choice['id'].']" value="'.xml_escape($choice['name']).'" style="width: 250px" />'."\n"); ?>
<?php echo('			<label for="poll_choice_delete['.$choice['id'].']">Delete: </label>'."\n"); ?>
<?php echo('			<input type="checkbox" id="poll_choice_delete['.$choice['id'].']" name="poll_choice_delete['.$choice['id'].']" value="'.xml_escape($choice['name']).'" style="width: 250px" />'."\n"); ?>
<?php echo('			<br /><br />'."\n"); ?>
<?php
	}
?>
			<label for="poll_new_choice_name">Add Choice: </label>
<?php echo('			<input type="text" id="poll_new_choice_name" name="poll_new_choice_name" value="" style="width: 250px" />'."\n"); ?>
		</fieldset>
		<fieldset>
			<input type="submit" name="submit_edit_choices" value="Save Choices" />
		</fieldset>
	</form>
</div>
