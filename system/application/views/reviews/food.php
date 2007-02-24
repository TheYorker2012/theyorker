<div class="RightToolbar">
	<h4>Leagues</h4>
	<div class="Entry">

<?php
//Display leagues

if (isset($league_data))
{
	foreach ($league_data as $league_entry)
	{
		echo
		'
		<div class="LifestylePuffer">
		<a href="/reviews/leagues/'.$league_entry['league_codename'].'">
		<img src="'.$league_entry['league_image_path'].'" alt="'.$league_entry['league_name'].'" />
		</a>
		</div>
		';
	}
}
?>

</div>
</div>
<div class='grey_box'>
	<h2>browse by</h2>
	<?php
	/* Review types - this depends on the page : food/drink/culture */
	/* Note from Frank $content_type is passed to here containing either 'food'/'drink'/'culture' */
	?>

<?php
	echo '<form name="reviews" class="form" method="post" action="/reviews/table/'.$this->uri->segment(2).'/star">';
?>
		<fieldset>
			<label for="filter">Find based on: </label>
			<select name="item_filter_by" onChange="updatesortby(this.selectedIndex)" style="width: 150px">
				<option value="any" selected>See All</option>
<?php
//List Tag Group Names

//Check if any exist
if (isset($table_data['tag_group_names'][0]) == 1)
{

	//Foreach print out the value
	foreach($table_data['tag_group_names'] as $tag)
	{
		echo '<option value="'.$tag.'">'.$tag.'</option>';
	}
}

?>
			</select>
			<label for="where_equal_to">Only Show: </label>
			<select name="where_equal_to" style="width: 150px;">
				<option value="any" selected>See All</option>
			</select>
			<br />
			<input type="submit" class="button" value="Find" />
		</fieldset>
	</form>
	<br />

	<script type="text/javascript">

	var filterlist=document.reviews.sorted_by
	var sortbylist=document.reviews.where_equal_to
	/* The following sets the array which links each selection from the first form select with a series of selections
	 * into the second form select
	 * sortby[0] is See All.
	 * The first value is what the select option text is, the second is the value tag
	 * So "Dirt Cheap|dirtcheap" makes <option value="dirtcheap">Dirt Cheap</option>
	*/
	var sortby=new Array()
	sortby[0]=["See All|all"]

<?php
//Print out the tags for each tag_group
	
	//Foreach tag_group
	for ($tag_group_no = 0; $tag_group_no < count($table_data['tag_group_names']); $tag_group_no++)
	{
		echo 'sortby['.($tag_group_no+1).']=[';

		//Print each tag
		for ($tag_no = 0; $tag_no < count($table_data[$table_data['tag_group_names'][$tag_group_no]]); $tag_no++)
		{
			echo '"'.$table_data[$table_data['tag_group_names'][$tag_group_no]][$tag_no].'|'.$table_data[$table_data['tag_group_names'][$tag_group_no]][$tag_no].'", ';
		}
		echo "]\n";
	}

?>

	
	function updatesortby(selectedsortby){
		sortbylist.options.length=0
		if (selectedsortby>=0){
		for (i=0; i<sortby[selectedsortby].length; i++)
		sortbylist.options[sortbylist.options.length]=new Option(sortby[selectedsortby][i].split("|")[0], sortby[selectedsortby][i].split("|")[1])
		}
	}

	</script>
</div>

<div class='blue_box'>
		<h2>food feature</h2>
<?php
/*
echo '<a href="'.$article_link.'">';
echo '<img style="float: right;" src="'.$article_photo.'" alt="'.$article_photo_alt_text.'" title="'.$article_photo_title.'" /></a>';
*/
?>
		<h3><?php echo anchor($article_link, $article_title); ?></h3>
		<!-- Enter the default byline here - tempoarry solution follows -->
		#Put default byline here<br />
		<span style='font-size: medium;'><b><?php echo "<a href='".$article_author_link."'>".$article_author."</a>"; ?></b></span><br />
		<?php echo $article_date ?><br />
		<span class="orange"><?php echo anchor($article_link, 'Read more...', array('class' => 'orange')); ?></span>
	        <p>
			<?php echo $article_content; ?>
		</p>
</div>
