<div class="RightToolbar">
	<h4>Quick Links</h4>
	A Link
</div>

<div class="MainToolbar">
	<div class="blue_box">
		<h2>charities</h2>
		<table width="90%" cellpadding="3" align="center">
			<thead>
				<tr>
					<th style="width: 80%">Name</th>
					<th style="width: 20%">Is Current?</th>
				</tr>
			</thead>
			<tbody>
<?php
	foreach ($charities as $charity)
	{
		echo('				<tr>'."\n");
		echo('					<td><a href="/office/charity/editinfo/'.$charity['id'].'">'.$charity['name'].'</a></td>'."\n");
		if ($charity['iscurrent'] == 1)
			echo('					<td>yes</td>'."\n");
		else
			echo('					<td>no</td>'."\n");
		echo('				</tr>'."\n");
	}
?>
			</tbody>
		</table>
	</div>
</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>
