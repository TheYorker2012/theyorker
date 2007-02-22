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
	?>
	<form name="reviews" class="form">
		<fieldset>
			<label for="filter">Find based on: </label>
			<select name="filter" onChange="updatesortby(this.selectedIndex)" style="width: 150px">
				<option value="all" selected>See All</option>
				<option value="cuisine">Cuisine</option>
				<option value="price">Price</option>
				<option value="rating">Rating</option>
			</select>
			<label for="sortby">Only Show: </label>
			<select name="sortby" style="width: 150px;">
				<option value="all" selected>See All</option>
			</select>
			<br />
			<input type="submit" class="button" value="Find" />
		</fieldset>
	</form>
	<br />

	<script type="text/javascript">

	var filterlist=document.reviews.filter
	var sortbylist=document.reviews.sortby
	/* The following sets the array which links each selection from the first form select with a series of selections
	 * into the second form select
	 * sortby[0] is See Alll.
	 * The first value is what the select option text is, the second is the value tag
	 * So "Dirt Cheap|dirtcheap" makes <option value="dirtcheap">Dirt Cheap</option>
	*/
	var sortby=new Array()
	sortby[0]=["See All|all"]
	sortby[1]=["Eastern|easten", "Western|western", "Mediteranian|mediteranina", "Arabic|arabic", "Asian|asian", "British Traditional|british", "Australian|australian"]
	sortby[2]=["Cheaper Resturants|cheaper", "More Expensive|moreexpensive", "Dirt Cheap|dirtcheap", "Kinda Cheap|kindacheap", "Ish Cheap|ishcheap", "Hella Costly|hellacostly"]
	sortby[3]=["High Ratings Only|highstar", "Low Ratings Only|lowstar", "1 Star|1star", "2 Star|2star", "3 Star|3star", "4 Star|4star", "5 Star|5star"]
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
