<div class='RightToolbar'>
	<h4>Other Leagues</h4>
	<div class='Entry'>
		<div class='LifestylePuffer' style='background-color: #04669c;'>
			<a href='/reviews/leagues'>
			<img src='/images/prototype/news/puffer1.jpg' alt='Ashes' title='Ashes' />
			<h3>Awesome Food</h3>
			<p>This guy is happy becuase he has visited our top ten foods places</p>
			</a>
		</div>
			<div class='LifestylePuffer' style='background-color: #a38b69;'>
			<a href='/reviews/leagues'>
			<img src='/images/prototype/news/puffer2.jpg' alt='Cooking' title='Cooking' />
			<h3>Desert</h3>
			<p>We've been all around York trying chocolate cakes...</p>
			</a>
		</div>
		<div class='LifestylePuffer' style='background-color: #000000;'>
			<a href='/reviews/leagues'>
			<img src='/images/prototype/news/puffer3.jpg' alt='Workout' title='Workout' />
			<h3>Lbs of Meat!</h3>
			<p>Want an all you can eat? Be sure to head on over to our ten best all you can eats.</p>
			</a>
		</div>
		<div class='LifestylePuffer' style='background-color: #000000;'>
			<a href='/reviews/leagues'>
			<img src='/images/prototype/news/puffer3.jpg' alt='Workout' title='Workout' />
			<h3>Beefcake</h3>
			<p>I tried this once and became a lard. Lots and lots of lard</p>
			</a>
		</div>
	</div>
</div

<div class='grey_box'>
	<h2 style="display: inline;">Best for Romance</h2><br />
	We ve been all around York and found the top ten places for a nice romantic meal. Check it!<br />
	</div>
<?php
		for($topten=0; $topten<10; $topten++) {
			echo '	<div class="blue_box" >';
			echo '		<div class="ReviewElementNumber"><h3>*****</h3></div>';
			echo '		<img style="float: left; padding: 0.5em;" src="'.$reviews['review_image'][$topten].'" alt="#" />';
			echo '		<h3 style="display: inline;"><a href="/context/evil_eye_lounge/food">'.($topten+1).' - '.$reviews['review_title'][$topten].'</a></h3><br />';
			echo '		<a href="'.$reviews['review_website'][$topten].'">www.website.co.uk</a><br />';
			echo '		'.$reviews['review_blurb'][$topten].'';
			echo '	</div>';
		}
	?>
	<div class="Clear">&nbsp;</div>

