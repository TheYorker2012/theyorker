<?php
	//sidebar
	echo('<div class="RightToolbar">'."\n");
	echo('	<h4>Quick Links</h4>'."\n");
	echo('	<div class="Entry">'."\n");
	echo('		<a href="/office/campaign/">Back To Campaign Index</a>'."\n");
	echo('		<br />');
	echo('		<a href="/office/campaign/">Back To Progress Reports</a>'."\n");
	echo('	</div>'."\n");
	echo('</div>'."\n");
	
	//main - request info
	echo('<div class="blue_box">'."\n");
	echo('	<h2>edit progress report</h2>'."\n");
	echo('</div>'."\n");
?>

<?php
echo('<pre><div class=BlueBox>');
print_r($data);
echo('</div></pre>');
?>