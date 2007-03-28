<div id="RightColumn">
	<h2 class="first">Leagues</h2>
	<div class="Entry">
<?php
	foreach ($league_data as $league_entry) {
		echo('		');
		echo('<a href="/reviews/leagues/'.$league_entry['league_codename'].'">');
		echo('<img src="'.$league_entry['league_image_path'].'" alt="'.$league_entry['league_name'].'" />');
		echo('</a>'."\n");
	}
?>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>browse by</h2>
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

	<div class="BlueBox">
		<h2><?php echo($content_type); ?> feature</h2>
<?php
		$this->byline->load();
		echo('		<a href="'.$article_link.'">'."\n");
		echo('			<img src="'.$article_photo.'" alt="'.$article_photo_alt_text.'" title="'.$article_photo_title.'" />'."\n");
		echo('		</a>');
		echo('		<h3>'."\n");
		echo('			<a href="'.$article_link.'">'.$article_title.'</a>'."\n");
		echo('		</h3>'."\n");
		echo('		<p>'.$article_content.'</p>'."\n");
		echo('		<p>'.anchor($article_link, 'Read more...').'</p>'."\n");
?>
	</div>
</div>
