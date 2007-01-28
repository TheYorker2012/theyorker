	<div class='RightToolbar'>
		<h4>More Features</h4>
		<div class='LifestylePuffer' style='background-color: #04669c;'>
			<a href='/news/article/1'>
			<img src='/images/prototype/news/puffer1.jpg' alt='Ashes' title='Ashes' />
			<h3>Ashes</h3>
			<p>Aussie press marvels as England suffer last-day disaster</p>
			</a>
			<div style='clear:both'></div>
		</div>
		<div class='LifestylePuffer' style='background-color: #a38b69;'>
			<a href='/news/article/1'>
			<img src='/images/prototype/news/puffer2.jpg' alt='Cooking' title='Cooking' />
	 	    <h3>Cooking</h3>
			<p>This week an awesome recipe for a chocolate cake</p>
			</a>
			<div style='clear:both'></div>
		</div>
		<div class='LifestylePuffer' style='background-color: #000;'>
			<a href='/news/article/1'>
			<img src='/images/prototype/news/puffer3.jpg' alt='Workout' title='Workout' />
	 	    <h3>Workout</h3>
			<p>This week we look at using weights and other heavy stuff</p>
			</a>
			<div style='clear:both'></div>
		</div>
		<div class='LifestylePuffer' style='background-color: #ef7f94;'>
			<a href='/news/article/1'>
			<img src='/images/prototype/news/puffer4.jpg' alt='Love' title='Love' />
	 	    <h3>Romance</h3>
			<p>This week we review what is the best valentine day's present</p>
			</a>
			<div style='clear:both'></div>
		</div>
		<div class='LifestylePuffer' style='background-color: #000;'>
			<a href='/news/article/1'>
			<img src='/images/prototype/news/puffer3.jpg' alt='Workout' title='Workout' />
	 	    <h3>Workout</h3>
			<p>This week we look at using weights and other heavy stuff</p>
			</a>
			<div style='clear:both'></div>
		</div>
		<div class='LifestylePuffer' style='background-color: #a38b69;'>
			<a href='/news/article/1'>
			<img src='/images/prototype/news/puffer2.jpg' alt='Cooking' title='Cooking' />
	 	    <h3>Cooking</h3>
			<p>This week an awesome recipe for a chocolate cake</p>
			</a>
			<div style='clear:both'></div>
		</div>
		<div style='padding-bottom: 150px;'>&nbsp;</div>
		<?php foreach ($main_article['fact_boxes'] as $fact_box) {
			echo '<div class=\'orange_box\'>';
   			echo '<h2>facts</h2>'.$fact_box;
			echo '</div>';
		} ?>
	</div>
	<div class='blue_box'>
		<img src='/images/prototype/news/benest.png' alt='Reporter' title='Reporter' style='float: right;' />
		<h2 style='margin-bottom: 5px;'>reported by...</h2>
		<span style='font-size: medium;'><b>Chris Travis</b></span><br />
		<?php echo $main_article['date']; ?><br />
		<span style='color: #ff6a00;'>Read more articles by this reporter</span>
	</div>
	<div class='grey_box'>
		<div class='ArticleColumn'>
			<h1><?php echo $main_article['heading']; ?></h1>
	        <p><?php echo $main_article['text']; ?></p>
			<br style='clear: both;' />
		</div>
	</div>
