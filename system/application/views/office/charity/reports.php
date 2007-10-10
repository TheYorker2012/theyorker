<?php
	//sidebar
	echo('<div class="RightToolbar">'."\n");
	echo('	<h4>Quick Links</h4>'."\n");
	echo('	<div class="Entry">'."\n");
	echo('		<a href="/office/charity/">Back To Charity Index</a>'."\n");
	echo('	</div>'."\n");
	echo('</div>'."\n");
	
	//main - request info
	echo('<div class="blue_box">'."\n");
	echo('	<h2>progress reports</h2>'."\n");
	if (count($progressreports) == 0)
	{
		echo('	No Progress Reports Yet.');
	}
	else
	{
		foreach($progressreports as $pr)
		{
			echo('	<hr /><br />'."\n");
			echo('	'.$pr['header']['publish_date']);
			if ($pr['header']['live_content'] != NULL)
				echo(' <span class="orange">(published)</span>');
			echo(' <a href="/office/charity/editprogressreport/'.$parameters['charity_id'].'/'.$pr['id'].'">[edit]</a>'."\n");
			echo('	<br />'."\n");
			if ($pr['header']['live_content'] != NULL)
				echo('	'.word_limiter($pr['article']['text'], 50)."\n");
			else
				echo('	No Preview.'."\n");
		}
	}
	echo('</div>'."\n");
	
	echo('<div class="blue_box">'."\n");
	echo('	<h2>add new progress report</h2>'."\n");
	echo('	Enter the date for the new progress report.'."\n");
	echo('	<form class="form" action="/office/charity/domodify" method="post" >'."\n");
	echo('		<fieldset>'."\n");
	echo('			<input type="hidden" name="r_charityid" value="'.$parameters['charity_id'].'" />'."\n");
	echo('		</fieldset>'."\n");
	echo('		<fieldset>'."\n");
	echo('			<label for="a_date">Date:</label>'."\n");
	echo('			<input type="text" name="a_date" size="60" value="');
	echo(date('Y-m-d H:i:s', time()));
	echo('" /><br />'."\n");
	echo('		</fieldset>'."\n");
	echo('		<fieldset>'."\n");
	echo('			<input type="submit" value="Add" class="button" name="r_submit_pr_add" />'."\n");
	echo('		</fieldset>'."\n");
	echo('	</form>'."\n");
	echo('</div>'."\n");
	/*
	echo('<div class="BlueBox"><pre>');
	print_r($data);
	echo('</pre></div>');
	*/
?>