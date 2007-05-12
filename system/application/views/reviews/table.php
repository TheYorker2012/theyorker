<div id="RightColumn">
	<h2 class="first">The Guide</h2>
	<div class="Entry">
		<form name="reviews" action="/reviews/table/<?php echo($this->uri->segment(3)); ?>/star" method="post">
			<fieldset>
				<label for="item_filter_by">Find based on: </label>
				<select name="item_filter_by" onchange="updatesortby(this.selectedIndex)">
					<option value="any" selected="selected">See All</option>
					<?php
					foreach($table_data['tag_group_names'] as $tag) {
						echo('					');
						echo('<option value="'.$tag.'">'.$tag.'</option>'."\n");
					}
					?>
				</select><br />

				<label for="where_equal_to">Only Show: </label>
				<select name="where_equal_to">
					<option value="any" selected="selected">See All</option>
				</select><br />
			</fieldset>
			<fieldset>
				<input type="submit" value="Find" class="button" />
			</fieldset>
		</form>

		<script type="text/javascript">
				//<![CDATA[

				var filterlist=document.reviews.sorted_by
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
				//]]>
		</script>

	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo($item_type); ?> search results for:</h2>
		<table border="0" width="97%">
		<tbody>
		<tr>
			<td>
				<?php echo($item_filter_by.' - '.$where_equal_to); ?>
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