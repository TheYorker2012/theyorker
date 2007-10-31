<div id="RightColumn">
<?php
//If there are some leagues print em
if (!empty($league_data)){
	echo ('	<h2 class="first">'.$leagues_header.'</h2>'."\n");
	foreach ($league_data as $league_entry) {
		echo ('	<div class="Puffer">'."\n");
		if($league_entry['has_image']){
			//There is a puffer image, so use it
			echo '		<a href="/reviews/leagues/'.$league_entry['league_codename'].'"><img src="'.$league_entry['image_path'].'" alt="'.$league_entry['league_name'].'" title="'.$league_entry['league_name'].'" /></a>';
		}
		else {
			//There is no puffer image, just put a text link
			echo('		<a href="/reviews/leagues/'.$league_entry['league_codename'].'">'.$league_entry['league_name'].'</a><br />'."\n");
		}
		echo ('	</div>'."\n");
	}
}
?>
</div>
<div id="MainColumn">
	<div id="HomeBanner">
		<?php echo($banner) ?>
	</div>
	<div class="BlueBox">
		<h2><?php echo($page_header) ?></h2>
		<?php echo($page_about) ?>
		<form name="reviews" action="/reviews/table/<?php echo($this->uri->segment(2)); ?>/star" method="post">
			<div style="float: left; width: 75%">
				<table>
					<tr>
						<td>
							<fieldset>
								Find based on:
								<select name="item_filter_by" onchange="updatesortby(this.selectedIndex)">
									<option value="any" selected="selected">See All</option>
									<?php
									foreach($table_data['tag_group_names'] as $tag) {
										echo('					');
										echo('<option value="'.$tag.'"');
										if (!empty($item_filter_by) && $tag==$item_filter_by)
										{echo ' selected="selected"';}
										echo('>'.$tag.'</option>'."\n");
									}
									?>
								</select>
							</fieldset>
						</td>
						<td>
							<fieldset>
								Only Show:
								<select name="where_equal_to">
									<option value="any" selected="selected">See All</option>
								</select>
							</fieldset>
						</td>
					</tr>
				</table>
			</div>
			<div style="float: right; width: 25%">
				<fieldset>
					<br />
					<input type="submit" value="Find" style="align: right;" class="button" />
				</fieldset>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		var filterlist=document.reviews.item_filter_by
		var sortbylist=document.reviews.where_equal_to
		/* The following sets the array which links each selection from the first form select with a series of selections
		 * into the second form select
		 * sortby[0] is See All.
		 * The first value is what the select option text is, the second is the value tag
		*/
		var sortby=new Array()
		sortby[0]=["See All|all"]
			<?php
	//Print out the tags for each tag_group
	//Foreach tag_group
	for ($tag_group_no = 0; $tag_group_no < count($table_data['tag_group_names']); $tag_group_no++) {
		echo('		sortby['.($tag_group_no+1).']=[');
		//Print each tag
		for ($tag_no = 0; $tag_no < count($table_data[$table_data['tag_group_names'][$tag_group_no]]); $tag_no++) {
			echo('"'.$table_data[$table_data['tag_group_names'][$tag_group_no]][$tag_no].'|'.$table_data[$table_data['tag_group_names'][$tag_group_no]][$tag_no].'", ');
		}
		echo("]\n");
	}
	?>
		function updatesortby(selectedsortby){
			sortbylist.options.length=0
			if (selectedsortby>=0){
			for (i=0; i<sortby[selectedsortby].length; i++)
			sortbylist.options[sortbylist.options.length]=new Option(sortby[selectedsortby][i].split("|")[0], sortby[selectedsortby][i].split("|")[1])
			}
		}
		updatesortby(filterlist.selectedIndex)
		for (index=0; index<=sortbylist.options.length;index++){
			if(sortbylist.options[index].value == "<?php if (!empty($where_equal_to)){echo $where_equal_to;}?>")
			{
			sortbylist.options[index].selected = true;
			}
		}
	</script>
	<div class="BlueBox">
		<table border="0" width="97%">
		<tbody>
		<tr>
			<td>
				<?php if($item_filter_by!='any' && $item_filter_by!='') { echo('Showing results for <b>'.$where_equal_to.'</b> based on <b>'.$item_filter_by.'</b>.'); } else { echo('Showing <b>all entries</b> in the '.$content_type.' guide.'); } ?>
			</td>
		</tr>
		<?php

		if (count($entries) == 0)
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
						<font size="+1"><strong><a href="<?php echo($entry['review_table_link']); ?>"><?php echo($entry['review_title']); ?></a></strong></font>
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
				<?php
				if (isset($entry['review_image']))
				{
				?>
				<tr>
					<td width="20%" valign="top">
						<img style="padding-left: 3px; padding-right: 6px;" src="<?php echo($entry['review_image']); ?>" width="144" height="116"/>
					</td>
					<td width="80%" valign="top">
						<?php echo($entry['review_blurb']); ?>
					</td>
				</tr>
				<?php
				}
				else
				{
				?>
				<tr>
					<td width="100%" valign="top">
						<?php echo($entry['review_blurb']); ?>
					</td>
				</tr>
				<?php
				}
				?>
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