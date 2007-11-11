<div id="RightColumn">
	<h2 class="first">The Guide</h2>
	<div class="Entry">
		<form name="reviews" action="/reviews/table/<?php echo($this->uri->segment(2)); ?>/star" method="post">
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
	<div class="BlueBox">
		<h2 class="Headline"><?php echo $main_article['heading']; ?></h2>
		<?php if(isset($main_article['primary_photo_xhtml'])) { ?>
		<div style="float:right;margin-top:0;line-height:95%;">
			<?php echo($main_article['primary_photo_xhtml']); ?><br />
			<?php echo($main_article['primary_photo_caption']); ?>
		</div>
		<?php } ?>
		<div class="Date"><?php echo($main_article['date']); ?></div>
		<div class="Author">
<?php foreach($main_article['authors'] as $reporter) { ?>
			<a href="/contact"><?php echo($reporter['name']); ?></a>
<?php } ?>
		</div>
<?php if ($main_article['subtext'] != '') { ?>
		<div class="SubText"><?php echo($main_article['subtext']); ?></div>
<?php } ?>

        <?php echo $main_article['text']; ?>

		<?php if (isset($office_preview)) { ?>
			<p class='form'><button class="button" onclick="window.location='/office/news/<?php echo $main_article['id']; ?>';">GO BACK TO NEWS OFFICE</button></p>
		<?php } ?>
	</div>
	<?php if (count($main_article['links']) > 0) { ?>
	<div class="BlueBox">
		<h2><?php echo $links_heading; ?></h2>
		<ul>
		<?php foreach ($main_article['links'] as $link) {
			echo '<li><a href=\'' . $link['url'] . '\' target=\'_blank\'>' . $link['name'] . '</a></li>';
		} ?>
		</ul>
	</div>
	<?php } ?>
	<?php
	// Comments if they're included
	if (isset($comments) && NULL !== $comments) {
		$comments->Load();
	}
	?>
</div>