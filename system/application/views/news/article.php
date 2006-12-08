	<div id='newsnav'>
		<ul id='newsnavlist'>
		<li><a href='<?php echo site_url('news/'); ?>' id='current'><img src='/images/prototype/news/uk.png' alt='Campus News' title='Campus News' /> Campus News</a></li>
		<li><a href='<?php echo site_url('news/national/'); ?>'><img src='/images/prototype/news/earth.png' alt='National News' title='National News' /> National News</a></li>
		<li><a href='<?php echo site_url('news/features/'); ?>'><img src='/images/prototype/news/feature.gif' alt='Feature' title='Feature' /> Features</a></li>
		<li><a href='<?php echo site_url('news/lifestyle/'); ?>'><img src='/images/prototype/news/feature.gif' alt='Lifestyle' title='Lifestyle' /> Lifestyle</a></li>
		<li><a href='<?php echo site_url('news/archive/'); ?>'><img src='/images/prototype/news/archive.png' alt='Archive' title='Archive' /> Archive</a></li>
		</ul>
	</div>

	<div align='center'>
		 <h1><?php echo $headline; ?></h1>
		 <h2><?php echo $subheading; ?></h2>
	</div>

	<div class='ArticleColumn' style='width: 20%;'>
	 	<br /><br /><br /><br />
		 <h3><?php echo $factbox_title; ?></h3>
         <?php echo $factbox_contents; ?>
	</div>
	<div class='ArticleColumn' style='width: 60%;'>

		<div class='clear'>&nbsp;</div>
		<div style='background-color: #DDDDDD;'>
			<div id='Byline' style="background-image: url('<?php echo $writer_image; ?>');">
				<div class='RoundBoxBL'>
					<div class='RoundBoxBR'>
						<div class='RoundBoxTL'>
							<div class='RoundBoxTR'>
								<div id='BylineText'>
									Written by<br />
									<span class='name'><?php echo $writer_name; ?></span><br />
									<span class='links'>
										<?php echo anchor('directory/', 'Directory Entry'); ?><br />
										<?php echo anchor('news/archive/reporter/'.$writer_id, 'See more articles by this reporter'); ?>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?php echo $body; ?>

		<p class='SubLinks'><?php echo anchor('news/archive/', 'News Archive'); ?>&nbsp;&nbsp;&nbsp;<?php echo anchor('news/archive/', 'Related Articles'); ?></p>
	</div>
	<div id='QuotesCol' class='ArticleColumn'>
		 <br /><br /><br /><br /><br />
		 <?php
		 foreach ($pull_quotes as $quote)
		 {
    		 echo '<div class=\'quote\'>
    		     <img src=\'/images/prototype/news/quote_open.png\' alt=\'Quote\' title=\'Quote\' />
    		     '.$quote['text'].'
    		 	 <img src=\'/images/prototype/news/quote_close.png\' alt=\'Quote\' title=\'Quote\' />
    		 	 <br /><span class=\'author\'>'.$quote['name'].'</span>
    	 	 </div>
    	 	 <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />';
     	 }
     	 ?>
	</div>