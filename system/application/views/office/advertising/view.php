<div id="RightColumn">
	<h2 class="first">Quick Links</h2>
	<div class="Entry">
		<ul>
			<li><a href="/office/advertising/view/<?php echo($advert['id']); ?>">View This Advert</a></li>
			<li><a href="/office/advertising/editimage/<?php echo($advert['id']); ?>">Edit Advert Image</a></li>
			<li><a href="/office/advertising/">Back To Adverts List</a></li>
		</ul>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
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
										<?php echo(xml_escape($advert['name'])); ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>Target Web Address:</b>
									</td>
									<td>
										<?php echo(xml_escape($advert['url'])); ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>Image Hover Text:</b>
									</td>
									<td>
										<?php echo(xml_escape($advert['alt'])); ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>Is Live:</b>
									</td>
									<td>
										<?php echo($advert['is_live'] ? 'Yes' : 'No'); ?>
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
										<b>Start Date:</b>
									</td>
									<td>
										<?php $dateformatted = $advert['start_date']==0?'-': date('d/m/y @ H:i', $advert['start_date']); echo($dateformatted); ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>End Date:</b>
									</td>
									<td>
										<?php $dateformatted = $advert['end_date']==0?'-': date('d/m/y @ H:i', $advert['end_date']); echo($dateformatted); ?>
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
</div>
