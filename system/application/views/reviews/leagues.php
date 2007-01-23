<div class='ArticleColumn' style="width: 100%">
	<h2 style="display: inline;">Best for Romance</h2><br />
	We ve been all around York and found the top ten places for a nice romantic meal. Check it!<br /><br />
	<?php
		for($topten=0; $topten<10; $topten++) {
			echo '	<div class="ReviewElement" >';
			echo '		<img src="'.$reviews['review_image'][$topten].'" alt="#" />';
			echo '		<div class="ReviewElementNumber"><h3>'.($topten+1).'</h3></div>';
			echo '		<h3 style="display: inline;"><a href='.$review_link[$topten].'">'.($topten+1).$reviews['review_title'][$topten].'</a></h3><br />';
			echo '		<a href="'.$reviews['review_website'][$topten].'">www.website.co.uk</a><br />';
			echo '		Average user rating: <a href=#>'.$reviews['review_rating'][$topten].'/10</a><br />';
			echo '		'.$reviews['review_blurb'][$topten].'';
			echo '	</div><br />';
		}
	?>
	<div class="Clear">&nbsp;</div>
</div>

