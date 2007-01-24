<?php
/*
 * Input
 * $organisation [
 *	'reviews_untyped' [review1, review2....]
 *	'reviews_by_type' [
 *		type1name => [review1, review2...],
 *		type2name => [review1, review2...]
 *	]
 * ]
 */
if (empty($organisation['reviews_by_type'])) {
} else {
	foreach ($organisation['reviews_by_type'] as $review_type_name => $reviews) {
		// $review_type_name: name of review type e.g. food, drink...
		foreach ($reviews as $review) {
			/*
			 * $review is made up of:
			 *	type - same as $review_type_name
			 *	publish_date
			 *	content - as parsed wikitext (html)
			 *	link - where to link (not sure where this is supposed to link
			 *	authors - array of authors, each with:
			 */
			foreach ($authors as $author) {
				/* $author is made up of:
				 *	name
				 *	email
				 */
				
			}
		}
	}
}
if (empty($organisation['reviews_untyped'])) {
?>
	<div align="center" style='padding: 100px 0px 50px 0px;'>
		<b>This organisation has not been reviewed yet.</b>
	</div>
<?php
} else {
	foreach ($organisation['reviews_untyped'] as $review) {
		$author_links = array();
		foreach ($review['authors'] as $author) {
			$author_links[] = '<a href="mailto:'.$author['email'].'">'.$author['name'].'</a>';
		}
		$authors = implode(', ', $author_links);
?>
		<div class="WhyNotTry">
			<div class="ArticleColumn" style="width: 100%;">
				<div style='background-color: #DDDDDD;'>
					<div id='Byline' style="background-image: url('/images/prototype/news/benest.png');">
						<div class='RoundBoxBL'>
							<div class='RoundBoxBR'>
								<div class='RoundBoxTL'>
									<div class='RoundBoxTR'>
										<div id='BylineText'>
											Written by<br />
											<span class='name'><?php echo $authors; ?></span><br />
											<?php echo $review['publish_date']; ?><br />
											<span class='links'>
												<?php echo anchor($review['link'], 'See more...'); ?>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php echo $review['content']; ?><br />
		</div>
<?php
	}
}
?>
