<div id="RightColumn">
	<h2 class="first">Browse By</h2>
	<div class="entry">
		<form name="reviews" action="#" method="post">
			<fieldset>
				<label for="item_filter_by">Find based on: </label>
				<select name="item_filter_by">
					<option value="any" selected="selected">See All</option>

					<option value="Cuisine">Cuisine</option>
					<option value="Price">Price</option>
					<option value="Find by Time">Find by Time</option>
					<option value="Atmosphere">Atmosphere</option>
				</select><br />

				<label for="where_equal_to">Only Show: </label>
				<select name="where_equal_to">
					<option value="any" selected="selected">See All</option>
				</select><br />

				<label for="sort_by">Sort By: </label>
				<select name="sort_by">
					<option value="name" selected="selected">Name</option>
				</select><br />

			</fieldset>
			<fieldset>
				<input type="submit" value="Find" class="button" />
			</fieldset>
		</form>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo($item_type); ?> search results</h2>
		<table border="0" width="97%">
		<tbody>
		<?php
		
		foreach($entries as $entry)
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
						<font size="+1"><strong><?php echo($entry['review_title']); ?></strong></font>
						<br />
						<span style="color: #999999; font-size: 0.9em;" ><a href="<?php echo($entry['review_website']); ?>">Website</a> | <a href="#">Map</a></span>
					</td>
					<td width="126" align="center">
						<?php
						$whole = floor($entry['review_rating'] / 2); 
						$part = $entry['review_rating'] % 2;
						for($i=0;$i<$whole;$i++)
						{
							echo '<img src="/images/prototype/reviews/star.png" alt="*" title="*" />';
						}
						if ($part == 1)
						{
							echo '<img src="/images/prototype/reviews/halfstar.png" alt="-" title="-" />';
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
					<td width="20%" valign="top">
						<img style="padding-left: 3px; padding-right: 6px;" src="/images/images/medium/0/127.jpg" width="144" height="116" alt="singer" title="singer" />
						<!--<img src="/images/images/small/0/94.jpg" alt="singer" title="singer" />-->
					</td>
					<td width="80%" valign="top">
						<?php echo($entry['review_blurb']); ?>
					</td>
				</tr>
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
				</tr>
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
		
		?>
		</tbody>
		</table>
	</div>
</div>