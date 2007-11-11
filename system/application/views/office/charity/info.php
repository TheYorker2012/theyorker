<div class="RightToolbar">
	<h4>Quick Links</h4>
	<?php
	echo '<a href="/office/charity/editarticle/'.$charity['id'].'">Article</a><br/ >';
	echo '<a href="/office/charity/editreports/'.$charity['id'].'">Progress Reports</a><br/ >';
	echo '<br/ >';
	?>
</div>

<div id="MainColumn">
	<div class="blue_box">
		<h2>charity info</h2>
<?php
	echo('			<b>Name: </b>'.$charity['name'].'<br />'."\n");
	echo('			<b>Goal Total: </b>'.$charity['target'].'<br />'."\n");
	echo('			<a href="/office/charity/modify/'.$charity['id'].'">[Modify]</a>'."\n");
?>
	</div>
</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>
