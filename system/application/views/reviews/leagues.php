<?php
if (isset($league_data)) {
?>
<div id="RightColumn">
<?php
//If there are some leagues print em
if (!empty($league_data)){
	echo ('	<h2 class="first">'.$leagues_header.'</h2>'."\n");
	foreach ($league_data as $league_entry) {
		echo ('	<div class="Puffer">'."\n");
		if($league_entry['has_image']){
			//There is a puffer image, so use it
			echo('		<a href="/reviews/leagues/'.$league_entry['league_codename'].'"><img src="'.$league_entry['image_path'].'" alt="'.xml_escape($league_entry['league_name']).'" title="'.xml_escape($league_entry['league_name']).'" /></a>');
		}
		else {
			//There is no puffer image, just put a text link
			echo('		<a href="/reviews/leagues/'.$league_entry['league_codename'].'">'.xml_escape($league_entry['league_name']).'</a><br />'."\n");
		}
		echo ('	</div>'."\n");
	}
}
?>
</div>

<div id="MainColumn">
	<div class="BlueBox">		<h2><?php if (isset($league_name) == 1) { echo(xml_escape($league_name)); } else { echo('League'); }?></h2>
		<table border="0" width="97%">
		<tbody>
		<?php
		
		if (isset($reviews) == false)
		{
		?>
		<tr>
			<td>
				<hr style="height: 1px; border: 0; color: #20c1f0; background-color: #20c1f0;"/>
			</td>
		</tr>
		<tr>
			<td align='center' >
				<?php echo($empty_league); ?>
			</td>
		</tr>
		<?php	
		}
		else
		{	
			$count=1;
			foreach($reviews as $entry)
			{
		
		?>
		<tr>
			<td>
				<hr style="height: 1px; border: 0; color: #20c1f0; background-color: #20c1f0;"/>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" width="100%">
				<tbody>
				<tr>
					<td valign="top">
						<font size="+1"><strong><?php echo($count.') '); ?><a href="<?php echo(xml_escape($entry['review_link'])); ?>"><?php echo(xml_escape($entry['review_title'])); ?></a></strong></font>
						<br />
						<span style="color: #999999; font-size: 0.9em;" >&nbsp;&nbsp;<a href="<?php echo(xml_escape($entry['review_website'])); ?>">Website</a><!-- | <a href="#">Map</a>--></span>
					</td>
					<td width="126" align="center">
						<?php
						$whole = floor($entry['review_rating'] / 2); 
						$part = $entry['review_rating'] % 2;
						$empty = 5 - $whole - $part;
						echo(str_repeat('<img src="/images/prototype/reviews/star.png" alt="*" title="*" />', $whole));
						if ($part == 1)
						{
							echo('<img src="/images/prototype/reviews/halfstar.png" alt="-" title="-" />');
						}
						echo(str_repeat('<img src="/images/prototype/reviews/emptystar.png" alt=" " title=" " />', $empty));
						
						?>
						<div class="Date" style="font-size: 0.9em;">User Rating: <?php
						if($entry['review_user_rating'] > 0) {
							echo($entry['review_user_rating'].'/10');
						} else {
							echo('n/a');
						} ?></div>
					</td>
				</tr>
				</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" width="100%">
				<tbody>
				<?php
				if (isset($entry['slideshow'][1]))
				{
				?>
				<tr>
					<td width="20%" valign="top">
						<img style="padding-left: 3px; padding-right: 6px;" src="<?php echo(xml_escape($entry['slideshow'][1]['location'])); ?>" width="144" height="116" alt="singer" title="singer" />
					</td>
					<td width="80%" valign="top">
						<?php echo(xml_escape($entry['review_blurb'])); ?>
					</td>
				</tr>
				<?php
				}
				else
				{
				?>
				<tr>
					<td width="100%" valign="top">
						<?php echo(xml_escape($entry['review_blurb'])); ?>
					</td>
				</tr>
				<?php
				}
				?>
				<tr>
				</tr>
				<tr>
					<td colspan="2">
						<table border="0" width="100%">
						<tr>
							<?php
							/*
							foreach($entry['tagbox'] as $tag => $values )
							{
								echo('<td width="25% valign="top">');
								echo('<strong>'.$tag.':</strong><br />');
								echo(implode(' / ', $values).'</td>');
							}*/
							foreach($entry['alltags']['tag_group_names'] as $tag_group)
							{
								echo('<td width="25% valign="top">');
								echo('<strong>'.xml_escape($tag_group).':</strong><br />');
								if (isset($entry['tags'][$tag_group]))
								{
									foreach($entry['tags'][$tag_group] as $tag)
									{
										echo(xml_escape($tag).'<br />');
									}
								}
								else
								{
									echo('N/A');
								}
								echo('</td>');
							}
							?>
						</tr>
						</table>
					</td>
				</tr>
				<?php
				if($entry['review_quote'] != "")
				{
				?>
				<tr>
					<td align="left" colspan="2">
						<img src="/images/prototype/news/quote_open.png" alt="oquote" />
						<?php echo(xml_escape($entry['review_quote'])); ?>
						<img src="/images/prototype/news/quote_close.png" alt="oquote" />
					</td>
				</tr>
				<?php
				}
				?>
				</tbody>
				</table>
			</td>
		</tr>
		<?php
			$count++;
			}		
		}
		?>
		</tbody>
		</table>
	</div>
	<a href="/reviews/<?php echo($content_type); ?>/">Back to <?php echo(xml_escape(ucfirst($content_type))); ?> Homepage</a>
</div>

<?php
}
?>