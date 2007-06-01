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
	<?php
	/*
	<h2 class="first">Leagues</h2>
	<div class="Entry">
	<ul>

		foreach ($league_data as $league_entry) {
			echo('		');
			echo('<li><a href="/reviews/leagues/'.$league_entry['league_codename'].'">');
			echo($league_entry['league_name']);
			echo('</a></li>'."\n");
		}
	?>
	</ul>
	</div>
	*/
	?>
</div>

<div id="MainColumn">
<?php if (!isset($main_review)) { ?>
		<div class="BlueBox">
		<h2 class="Headline">No reviews</h2>
		<div class="Date"><?php echo(date('l, jS F Y')); ?></div>
		<p>Sorry, there are currently no reviews available for this section. Please check back soon.</p>
		</div>
<?php } else { ?>
		<div class="BlueBox">
			<?php $this->feedback_article_heading = 'Main Review Page: '.$main_review['organisation_name']; ?>
			<div style="float: right"><a href="<?php echo '/reviews/'.$main_review['content_type_codename'].'/'.$main_review['organisation_directory_entry_name']; ?>"><b>View Guide</b> <img src="/images/icons/book_go.png" /></a></div>
			<h2 class="Headline"><?php echo $main_review['organisation_name']; ?></h2>
			<?php if(count($main_review['slideshow']) > 0) { ?>
			<div style="float:right;margin-top:0;line-height:95%;">
				<div id="SlideShow" class="entry">
					<img src="<?php echo($main_review['slideshow'][0]['url']); ?>" id="SlideShowImage" alt="Slideshow" title="Slideshow" />
				</div>

				<script type="text/javascript">
			<?php foreach ($main_review['slideshow'] as $slide_photo) { ?>
				Slideshow.add('<?php echo($slide_photo['url']); ?>');
			<?php } ?>
				Slideshow.load();
				</script>
			</div>
			<?php } ?>
			<div class="Date"><?php echo($main_review['date']); ?></div>
			<div class="Author">
	<?php foreach($main_review['authors'] as $reporter) { ?>
				<a href="/contact"><?php echo($reporter['name']); ?></a>
	<?php } ?>
			</div>

	<?php if ($main_review['quote'] != '') { ?>
			<div class="SubText">
				<img src="/images/prototype/news/quote_open.png" />
				<?php echo($main_review['quote']); ?>
				<img src="/images/prototype/news/quote_close.png" />
			</div>
	<?php } ?>

			<h3>Rating</h3>
			<div>
			<?php
			$review_rating = $main_review['rating'];
			$star = 0;
			while ($star < floor($review_rating/2)) {
				echo('<img src="/images/prototype/reviews/star.png" alt="*" title="*" />');
				$star++;
			}
			if ($review_rating % 2 == 1) {
				echo('<img src="/images/prototype/reviews/halfstar.png" alt="-" title="-" />');
				$star++;
			}
			while ($star < 5) {
				echo('<img src="/images/prototype/reviews/emptystar.png" alt=" " title=" " />');
				$star++;
			}
			?>

			</div>

			<?php echo $main_review['text']; ?>
		</div>

		<?php
		// Comments if they're included
		if (isset($comments) && NULL !== $comments) {
			$comments->Load();
		}
}
?>
</div>