<?php
	//sidebar
	echo('<div class="RightToolbar">'."\n");
	echo('	<h4>Quick Links</h4>'."\n");
	echo('	<div class="Entry">'."\n");
	echo('		<a href="/office/campaign/">Back To Campaign Index</a>'."\n");
	echo('	</div>'."\n");
	echo('</div>'."\n");
	
	//main - request info
	echo('<div class="blue_box">'."\n");
	echo('	<h2>edit progress reports</h2>'."\n");
	foreach($progressreports as $pr)
	{
		echo('	<hr /><br />'."\n");
		echo('	'.$pr['header']['publish_date'].' <a href="/office/campaign/editprogressreport/'.$parameters['campaign_id'].'/'.$pr['id'].'">[edit]</a>'."\n");
		echo('	<br />'."\n");
		if ($pr['header']['live_content'] != NULL)
			echo('	'.$pr['article']['text']."\n");
		else
			echo('	No Preview.'."\n");
	}
	echo('</div>'."\n");
?>