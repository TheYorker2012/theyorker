<h2>Recent reviews:</h2>
<?php
foreach ($organisation['reviews'] as $review) {
	$authors = '';
	foreach ($review['authors'] as $author) {
		$authors .= '<a href="mailto:'.$author['email'].'">'.$author['name'].'</a> ';
	}
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
		<?php echo $review['content']['blurb']; ?><br />
	</div>
<?php } ?>
