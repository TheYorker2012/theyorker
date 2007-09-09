<div id="RightColumn">
	<h2 class="first">
		<?php echo $links_heading; ?>
	</h2>
	<div class="Entry">
		Links
	</div>
</div>
<div id="MainColumn">
	<div id="HomeBanner">
		<?php echo($banner) ?>
	</div>
	<?php
	$this->homepage_boxes->print_box_with_picture_list($main_sport,$latest_heading,'news');
	$this->homepage_boxes->print_box_of_category_lists($more_heading,$show_sports,$sport_lists);
	?>
</div>