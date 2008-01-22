<div id="RightColumn">
	<h2 class="first">
		Quick Links
	</h2>
	<div class="Entry">
		<a href="/office/polls/">Back To Poll List</a>
	</div>
<?php $this->polls_view->print_sidebar_poll_no_voting($poll_info, $poll_choice_data); ?>
</div>

<div class="blue_box">
	<h2>edit poll</h2>
<?php echo('		<form class="form" action="/office/polls/edit/'.$poll_id.'" method="post">'."\n"); ?>
		<fieldset>
			<label for="poll_question">Question: </label>
<?php echo('			<input type="text" id="poll_question" name="poll_question" value="'.$poll_info['question'].'" style="width: 250px" />'."\n"); ?>
		</fieldset>
		<fieldset>
			<input type="submit" name="submit_edit_poll" value="Edit" />
		</fieldset>
	</form>
</div>

<div class="blue_box">
	<h2>options</h2>
<?php
	if ($poll_info['is_running'] == false)
	{
		if ($poll_choice_count >= 2)
		{
?>
	<div class="Entry">
			* For this poll to be made the displayed poll you must set it as running.
<?php echo('			<form class="form" action="/office/polls/edit/'.$poll_id.'" method="post">'."\n"); ?>
			<fieldset>
				<input type="submit" name="submit_edit_set_running" value="Set As Running" />
			</fieldset>
		</form>
	</div>
<?php
		}
		else
		{
?>
<?php echo('			* You need to add at least <a href="/office/polls/choices/'.$poll_id.'">two choices</a> to run a poll.'."\n"); ?>
			<br />
<?php
		}
	}
	else
	{
?>
	<div class="Entry">
			* End the voting on this poll.
<?php echo('			<form class="form" action="/office/polls/edit/'.$poll_id.'" method="post">'."\n"); ?>
			<fieldset>
				<input type="submit" name="submit_edit_set_not_running" value="End Poll" />
			</fieldset>
		</form>
	</div>
<?php
	}
?>
	<br />
	<div class="Entry">
			* Remove this poll from the poll list.
<?php echo('			<form class="form" action="/office/polls/edit/'.$poll_id.'" method="post">'."\n"); ?>
			<fieldset>
				<input type="submit" name="submit_edit_delete" value="Delete Poll" />
			</fieldset>
		</form>
	</div>
</div>

<?php
/*
	echo('<div class="blue_box"><pre>');
	print_r($data);
	echo('</pre></div>');
*/
?>