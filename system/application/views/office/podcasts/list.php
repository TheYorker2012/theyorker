<div id="RightColumn">
	<h2 class="first">Quick Links</h2>
	<div class="Entry">
		
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>podcasts</h2>
		<div class="ArticleBox">
			<table>
				<thead>
					<tr>
						<th style="width:85%;">
							Title
						</th>
						<th style="width:15%;text-align:right;">
							Is Live
						</th>
					</tr>
				</thead>
				<tbody>
<?php
	foreach ($podcasts as $podcast) {
		$podcast['is_live'] ? $is_live = "Yes" : $is_live = "No";
?>
					<tr>
						<td>
							<?php echo('<a href="/office/podcasts/edit/'.$podcast['id'].'">'.$podcast['name'].'</a>'); ?>
						</td>
						<td style="text-align:right;">
							<?php echo($is_live); ?>
						</td>
					</tr>
<?php
	}
?>
				</tbody>
			</table>
		</div>
	</div>
</div>
