<?php

function print_table($name, $campaign_list)
{
	echo('<div class="MainToolbar">'."\n");
	echo('	<div class="blue_box">'."\n");
	echo('		<h2>'.$name.'</h2>'."\n");
	echo('		<table width="90%" cellpadding="3" align="center">'."\n");
	echo('			<thead>'."\n");
	echo('				<tr>'."\n");
	echo('					<th width="80%">Name</th>'."\n");
	echo('					<th width="20%">Petitioned</th>'."\n");
	echo('				</tr>'."\n");
	echo('			</thead>'."\n");
	echo('			<tbody>'."\n");
	foreach ($campaign_list as $key => $campaign)
	{
		echo('				<tr>'."\n");
		echo('					<td><a href="/office/campaign/editarticle/'.$key.'">'.$campaign['name'].'</a></td>'."\n");
		if ($campaign['has_been_petitioned'] == 1)
			echo('					<td>yes</td>'."\n");
		else
			echo('					<td>no</td>'."\n");
		echo('				</tr>'."\n");
	}
	echo('			</tbody>'."\n");
	echo('		</table>'."\n");
	echo('	</div>'."\n");
	echo('</div>'."\n");
}

?>

<div class="RightToolbar">
	<h4>Quick Links</h4>
	<div class="Entry">
		a link
	</div>
</div>

<?php print_table('live campaigns', $campaign_list_live); ?>

<?php print_table('future campaigns', $campaign_list_future); ?>

<?php print_table('unpublished campaigns', $campaign_list_unpublished); ?>

<?php print_table('expired campaigns', $campaign_list_expired); ?>