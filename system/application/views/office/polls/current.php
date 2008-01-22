<div id="RightColumn">
	<h2 class="first">
		Quick Links
	</h2>
	<div class="Entry">
		<a href="/office/">Office Home</a>
	</div>
</div>

<div class="blue_box">
	<h2>set currently displayed poll</h2>
	<form class="form" action="/office/polls/current" method="post">
		<fieldset>
			<label for="a_poll_list">Select Poll:</label>
			<select name="a_poll_list" id="a_poll_list">
<?php
	foreach ($running_poll_list as $poll)
	{
		if($poll['is_displayed'])
			echo('				<option value="'.$poll['id'].'" selected="selected">'.$poll['question'].'</option>'."\n");
		else
			echo('				<option value="'.$poll['id'].'">'.$poll['question'].'</option>'."\n");
	}
?>
			</select>
		</fieldset>
		<fieldset>
			<input type="submit" value="Set As Displayed Poll" class="button" name="r_submit_set_current" />
		</fieldset>
	</form>
</div>

<div class="blue_box">
	<h2>set no poll to be displayed</h2>
	<form class="form" action="/office/polls/current" method="post">
		<fieldset>
			<input type="submit" value="No Displayed Poll" class="button" name="r_submit_set_no_current" />
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
