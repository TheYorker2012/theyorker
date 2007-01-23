<?php
if(empty($organisation['reviews'])) {
?>
<div align="center" style='padding: 100px 0px 50px 0px;'>
	<b>This organisation has not been reviewed yet.</b>
</div>
<?php
}
foreach ($organisation['reviews'] as $review) {
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
<?php } ?>
