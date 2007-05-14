<?php
if (isset($league_data)) {
?>
<div id="RightColumn">
	<h2 class="first">Leagues</h2>
	<div class="Entry">
	<ul>
	<?php
		foreach ($league_data as $league_entry) {
			echo('		');
			echo('<li><a href="/reviews/leagues/'.$league_entry['league_codename'].'">');
			echo($league_entry['league_name']);
			echo('</a></li>'."\n");
		}
	?>
	</ul>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">		<h2><?php if (isset($league_name) == 1) { echo($league_name); } else { echo('League'); }?></h2>
		<p>Read our latest reviews from all around york! </p>
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
			<td>
				No Results.
			</td>
		</tr>
		<?php	
		}
		else
		{		
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
						<font size="+1"><strong><a href="<?php echo($entry['review_link']); ?>"><?php echo($entry['review_title']); ?></a></strong></font>
						<br />
						<span style="color: #999999; font-size: 0.9em;" ><a href="<?php echo($entry['review_website']); ?>">Website</a><!-- | <a href="#">Map</a>--></span>
					</td>
					<td width="126" align="center">
						<?php
						$whole = floor($entry['review_rating'] / 2); 
						$part = $entry['review_rating'] % 2;
						$empty = 5 - $whole - $part;
						for($i=0;$i<$whole;$i++)
						{
							echo '<img src="/images/prototype/reviews/star.png" alt="*" title="*" />';
						}
						if ($part == 1)
						{
							echo '<img src="/images/prototype/reviews/halfstar.png" alt="-" title="-" />';
						}
						for($i=0;$i<$empty;$i++)
						{
							echo '<img src="/images/prototype/reviews/emptystar.png" alt=" " title=" " />';
						}
						
						?>
						<div class="Date" style="font-size: 0.9em;">User Rating: <?php if($entry['review_user_rating'] > 0) {echo($entry['review_user_rating'].'/10');}else{echo('n/a');} ?></div>
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
				<tr>
				<!--
					<td width="20%" valign="top">
						<img style="padding-left: 3px; padding-right: 6px;" src="/images/images/medium/0/127.jpg" width="144" height="116" alt="singer" title="singer" />
					</td>-->
					<td width="80%" valign="top">
						<?php echo($entry['review_blurb']); ?>
					</td>
				</tr>
				<!--
				<tr>
					<td colspan="2">
						<table border="0" width="100%">
						<tr>
							<?php
							foreach($entry['tagbox'] as $tag => $values ) {
								echo('<td width="25% valign="top">');
								echo('<strong>'.$tag.':</strong><br />');
								echo(implode(' / ', $values).'</td>');
							}
							?>
						</tr>
						</table>
					</td>
				</tr>-->
				<?php
				if($entry['review_quote'] != "")
				{
				?>
				<tr>
					<td align="left" colspan="2">
						<img src="/images/prototype/news/quote_open.png" alt="oquote" />
						<?php echo($entry['review_quote']); ?>
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
		
			}		
		}
		?>
		</tbody>
		</table>
	</div>
</div>

<?php
}

echo '<pre>';
print_r($data);
echo '</pre>';
?>