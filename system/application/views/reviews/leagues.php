<div class='ArticleColumn' style="width: 100%">
	<h2 style="display: inline;">Best for Romance</h2><br />
	We've been all around York and found the top ten places for a nice romantic meal. Check it!<br /><br />
	<?php
		for($i=1;$i<=10;$i++) {
			echo '	<div class="ReviewElement" >';
			echo '		<img src="/images/prototype/news/thumb9.jpg" alt="#" />';
			echo '		<div class="ReviewElementNumber"><h3>'.$i.'</h3></div>';
			echo '		<h3 style="display: inline;"><a href="/reviews/foodreview/">'.$i.'Name'.$i.'</a></h3><br />';
			echo '		<a href="http://www.website.co.uk">www.website.co.uk</a><br />';
			echo '		Average user rating: <a href=#>'.$i.'/10</a><br />';
			echo '		The most romantic place in york is the blue bicycle. A wonderful place to go. I\'ve had some romantic nights in here before. Believe me!';
			echo '	</div><br />';
		}
	?>
	<div class="Clear">&nbsp;</div>
</div>

