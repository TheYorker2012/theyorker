<div id='newsnav'>
	<ul id='newsnavlist'>
	<li><a href='/reviews/' id='current'><img src='/images/prototype/news/uk.png' alt='Reviews' title='Reviews' /> Reviews</a></li>
	<li><a href='/reviews/food/'><img src='/images/prototype/news/feature.gif' alt='Food' title='Food' /> Food</a></li>
	<li><a href='/reviews/food/'><img src='/images/prototype/news/earth.png' alt='Drink' title='Drink' /> Drink</a></li>
	<li><a href='/reviews/culture/'><img src='/images/prototype/news/archive.png' alt='Culture' title='Culture' /> Culture</a></li>
	<li><a href='/reviews/table/'><img src='/images/prototype/news/archive.png' alt='A to Z' title='A to Z' /> A to Z</a></li>
	</ul>
</div>
<div class="ArticleColumn" style="width: 25%;">
	<div class="WhyNotTry">
		<h3 style="display: inline;">Leagues</h3>
		<ul>
			<li>Best for Romance
			<li>Awesome food place
			<li>Best Deserts in Town
			<li>Best all you can eat
			<li>Take-aways
		</ul>
	</div>
	<div class="WhyNotTry">
		<h3 style="display: inline;">Recommended</h3>
		<ul>
			<li><a href=#>Top 10 Places to Eat</a>
			<li><a href=#>Top 10 Places to Drink</a>
			<li><a href=#>Top 10 Cultural activities</a>
			<li><a href=#>Top 10 Voted by you</a>
			<li><a href=#>Your favourites</a>
		</ul>
	</div>
</div>
<div class='ArticleColumn' style='width: 50%;'>
	<h2 style="display: inline;">Best for Romance</h2><br />
	We've been all around York and found the top ten places for a nice romantic meal. Check it!<br /><br />
	<?php
		for($i=1;$i<=10;$i++) {
			echo '	<h3 style="display: inline;">'.$i.'</h3>';
			echo '	<div class="ReviewElement">';
			echo '		<img src="/images/prototype/news/thumb9.jpg" alt="#" />';
			echo '		<h3 style="display: inline;"><a href="/reviews/foodreview/">'.$i.'Name'.$i.'</a></h3><br />';
			echo '		<a href="http://www.website.co.uk">www.website.co.uk</a><br />';
			echo '		Average user rating: <a href=#>'.$i.'/10</a><br />';
			echo '		The most romantic place in york is the blue bicycle. A wonderful place to go. I\'ve had some romantic nights in here before. Believe me!';
			echo '	</div><br />';
		}
	?>
	<div class="Clear">&nbsp;</div>
</div>
<div class="ArticleColumn" style="width: 25%;">
	<div class="WhyNotTry">
		Advertisement<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />Ads By Goooooooogle<br />
	</div>
</div>

