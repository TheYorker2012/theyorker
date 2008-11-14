<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>advert listing</h2>
		<div class="ArticleBox">
			<table>
				<thead>
					<tr>
						<th style="width:55%;">
							Name
						</th>
						<th style="width:30%;">
							Views
						</th>
						<th style="width:15%;">
							Is Live?
						</th>
					</tr>
				</thead>
				<tbody>
	<?php
		$alternate = 1;
		foreach ($adverts as $advert)
		{
			echo('				<tr class="tr'.$alternate.'">'."\n");
			echo('					<td>'."\n");
			echo('						<a href="/office/advertising/view/'.$advert['id'].'">'.xml_escape($advert['name']).'</a> <a href="/office/advertising/edit/'.$advert['id'].'">[edit]</a>'."\n");
			echo('					</td>'."\n");
			echo('					<td>'."\n");
			if ($advert['current_views'] == $advert['max_views']) {
				echo('						<span class="orange">'.$advert['current_views'].' of '.$advert['max_views'].'</span>'."\n");
			}
			else
			{
				echo('						'.$advert['current_views'].' of '.$advert['max_views']."\n");
			}
			echo('					</td>'."\n");
			echo('					<td style="text-align:right;">'."\n");
			$result_xml = ($advert['is_live'] ? 'Yes' : 'No');
			echo('						'.$result_xml."\n");
			echo('					</td>'."\n");
			echo('				</tr>'."\n");
			$alternate = ($alternate == 1 ? 2 : 1);
		}
	?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="BlueBox">
		<h2>add a new advert</h2>
		<form class="form" action="/office/advertising/" method="post">
			<fieldset>
				<label for="advert_name">Name: </label>
				<input type="text" id="advert_name" name="advert_name" style="width: 250px" />
			</fieldset>
			<fieldset>
				<input type="submit" name="submit_add_advert" value="Add" />
			</fieldset>
		</form>
	</div>
</div>