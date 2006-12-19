	<div id='navbar'>
		<ul>
		<li><a href='/news/' class='current'><img src='/images/prototype/news/uk.png' alt='Campus News' title='Campus News' /> Campus News</a></li>
		<li><a href='/news/national/'><img src='/images/prototype/news/earth.png' alt='National News' title='National News' /> National News</a></li>
		<li><a href='/news/features/'><img src='/images/prototype/news/feature.gif' alt='Features' title='Features' /> Features</a></li>
		<li><a href='/news/lifestyle/'><img src='/images/prototype/news/feature.gif' alt='Lifestyle' title='Lifestyle' /> Lifestyle</a></li>
		<li><a href='/news/archive/'><img src='/images/prototype/news/archive.png' alt='Archive' title='Archive' /> Archive</a></li>
		</ul>
	</div>
	<div class='clear'>&nbsp;</div>

	<div class='ArticleColumn' style='width: 130px;'>
		<div class='ArticleBox'>
			<div class='Header'>
				Other Articles
			</div>
			<ul class='Content'>
				<?php
				foreach ($related_articles as $related) { ?>
				<li><?php echo anchor('news/article/'.$related['id'], $related['headline']); ?></li>
				<?php } ?>
			</ul>
		</div>
		<h3><?php echo $factbox_title; ?></h3>
		<?php echo $factbox_contents; ?>
	</div>
	<div class='ArticleColumn'>
		<h1><?php echo $headline; ?></h1>
		<h2><?php echo $subheading; ?></h2>
		<div class='clear'>&nbsp;</div>
		<div style='background-color: #DDDDDD;'>
			<div id='Byline'>
				<div class='RoundBoxBL'>
					<div class='RoundBoxBR'>
						<div class='RoundBoxTL'>
							<div class='RoundBoxTR'>
								<div id='BylineText' style='vertical-align: baseline;'>
									<img src='<?php echo $writer_image; ?>' alt='' title='' style='float:left' />
									Written by<br />
									<span class='name' style='vertical-align: baseline;'><?php echo anchor('directory/', $writer_name); ?></span><br />
									<span class='links' style='vertical-align: baseline;'>
										<?php echo anchor('news/archive/reporter/'.$writer_id, 'See more articles by this reporter'); ?>
									</span>
									<div style='clear:both'></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php echo $body; ?>
		<div class='clear'>&nbsp;</div>
		<div style='width:130px; float: left; text-align:center;'>
			<a href='/news/rss'><img src='/images/prototype/news/icon_rss.jpg' alt='Subscribe to RSS Feed' title='Subscribe to RSS Feed' /></a>
		</div>
		<div style='width:130px; float: left; text-align:center;'>
			<a href='/news/national'><img src='/images/prototype/news/icon_national.jpg' alt='National News' title='National News' /></a>
		</div>
		<div style='width:130px; float: left; text-align:center;'>
			<a href='/listings'><img src='/images/prototype/news/icon_events.jpg' alt='Events' title='Events' /></a>
		</div>
		<div style='width:130px; float: left; text-align:center;'>
			<a href='http://www.websudoku.com/'><img src='/images/prototype/news/icon_sudoku.jpg' alt='Play Sudoku' title='Play Sudoku' /></a>
		</div>
		<div class='clear'>&nbsp;</div>
	</div>
	<div class='ArticleColumn' style='width: 130px; text-align: center;'>
		<a href='' target='_blank'><img src='/images/adverts/3-120x600.gif' alt='Advert' title='Advert' /></a>
		<br /><br /><br />
		<?php
		foreach ($pull_quotes as $quote)
		{
			echo '<div class=\'quote\'>
				<img src=\'/images/prototype/news/quote_open.png\' alt=\'Quote\' title=\'Quote\' />
				'.$quote['text'].'
				<img src=\'/images/prototype/news/quote_close.png\' alt=\'Quote\' title=\'Quote\' />
				<br /><span class=\'author\'>'.$quote['name'].'</span>
				</div>
				<br /><br /><br /><br /><br /><br />';
		} ?>
		<div class='ArticleBox'>
			<div class='Header'>
				Related Articles
			</div>
			<ul class='Content'>
				<?php
				foreach ($related_articles as $related) { ?>
				<li><?php echo anchor('news/article/'.$related['id'], $related['headline']); ?></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class='clear'>&nbsp;</div>