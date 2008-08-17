<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
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
		echo('					<td><a href="/office/charity/editinfo/'.$charity['id'].'">'.xml_escape($charity['name']).'</a></td>'."\n");
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
