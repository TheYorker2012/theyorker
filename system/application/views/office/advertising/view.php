<div class="RightToolbar">
	<h4 class="first">Quick Links</h4>
	<div class="Entry">
		<?php echo('<a href="/office/advertising/edit/'.$advert['id'].'">Edit This Advert</a>'."\n"); ?>
	</div>
	<div class="Entry">
		<?php echo('<a href="/office/advertising/editimage/'.$advert['id'].'">Edit Advert Image</a>'."\n"); ?>
	</div>
	<div class="Entry">
		<?php echo('<a href="/office/advertising/">Back To Adverts List</a>'."\n"); ?>
	</div>
</div>
<div class="blue_box">
	<h2>advert summary</h2>
	<table width="100%">
		<tbody>
			<tr>
				<td valign="top">
					<table width="100%">
						<tbody>
							<tr>
								<td style="width: 30%">
									<b>Name:</b>
								</td>
								<td style="width: 70%">
									<?php echo($advert['name']); ?>
								</td>
							</tr>
							<tr>
								<td>
									<b>Image URL:</b>
								</td>
								<td>
									<?php echo($advert['url']); ?>
								</td>
							</tr>
							<tr>
								<td>
									<b>Image Text:</b>
								</td>
								<td>
									<?php echo($advert['alt']); ?>
								</td>
							</tr>
							<tr>
								<td>
									<b>Is Live:</b>
								</td>
								<td>
									<?php $advert['is_live'] ? $result = "Yes" : $result = "No" ; echo($result); ?>
								</td>
							</tr>
							<tr>
								<td>
									<b>Current Views:</b>
								</td>
								<td>
									<?php echo($advert['current_views']); ?>
								</td>
							</tr>
							<tr>
								<td>
									<b>Max Views:</b>
								</td>
								<td>
									<?php echo($advert['max_views']); ?>
								</td>
							</tr>
							<tr>
								<td>
									<b>Last View:</b>
								</td>
								<td>
									<?php $dateformatted = date('d/m/y @ H:i', $advert['last_display']); echo($dateformatted); ?>
								</td>
							</tr>
							<tr>
								<td>
									<b>Created:</b>
								</td>
								<td>
									<?php $dateformatted = date('d/m/y @ H:i', $advert['created']); echo($dateformatted); ?>
								</td>
							</tr>
							<tr>
								<td>
									<b>Options:</b>
								</td>
								<td>
									<?php echo('<a href="/office/advertising/edit/'.$advert['id'].'">[edit details]</a>'."\n"); ?>
									<?php echo('<a href="/office/advertising/editimage/'.$advert['id'].'">[edit image]</a>'."\n"); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td style="text-align: right; width: 150px;">

<?php
	//echo('					<img src="/images/adverts/'.$advert['image'].'" width="120" height="600" alt="'.$advert['alt'].'" title="'.$advert['alt'].'" />'."\n");
	echo('					'.$advert['image']."\n");
?>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>