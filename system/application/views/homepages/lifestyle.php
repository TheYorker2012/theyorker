<div id="RightColumn">
	<h2 class="first">
		<?php echo $links_heading; ?>
	</h2>
	<div class="Entry">
		<?php $this->homepage_boxes->print_puffer_column($puffers); ?>
	</div>
</div>
<div id="MainColumn">
	<div id="HomeBanner">
		<?php echo($banner) ?>
	</div>
	<?php
	$this->homepage_boxes->print_box_with_picture_list($main_articles,$latest_heading,'news');
	if(!empty($more_article_types)) $this->homepage_boxes->print_box_of_category_lists($more_heading,$more_article_types,$lists_of_more_articles);
	if($show_featured_puffer) $this->homepage_boxes->print_puffer_box($featured_puffer_title,$featured_puffer);
	?>
</div>