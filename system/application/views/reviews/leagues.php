<div class='RightToolbar'>
	<h4>Other Leagues</h4>
	<div class='Entry'>
<?php
//Display leagues

if (isset($league_data) == 1)
{
	foreach ($league_data as $league_entry)
	{
		if ($league_entry['league_image_id'] != 0) //Don't display if no image otherwise alt text floods out
		{
		echo
		"
		<div class='LifestylePuffer'>
		<a href='/reviews/leagues/".$league_entry['league_codename']."'>
		<img src='/images/images/".$league_entry['league_image_id'].".gif' alt='".$league_entry['league_name']."' />
		</a>
		</div>
		";
		}
	}
}
?>

	</div>
</div

<div class='grey_box'>
	<h2 style="display: inline;"><?php if (isset($league_name) == 1) echo $league_name; ?></h2><br />
	Read our latest reviews from all around york! <br />
	</div>
<?php
		for($topten=0; ($topten<10) && ($topten < $max_entries); $topten++) {
			echo '<div class="blue_box" >';
			echo '<h3 style="display: inline;"><a style="color: #20c1f0;" href="'.$reviews['review_link'][$topten].'">'.($topten+1).' - '.$reviews['review_title'][$topten].'</a></h3><br />';
			echo '		<div class="ReviewElementNumber" style="text-align: right; font-size: x-small; color: #f26a22;">
';
			//Star display

			//Display stars
			for ($stars = 0; ($stars < $reviews['review_rating'][$topten]/2); $stars++)
				{
						echo '<img src="/images/prototype/reviews/star.png" alt="*" title="*" />';
				}
			//Fill in the blanks
			for ($emptystars = 0; $emptystars < (5 - $stars); $emptystars++)
				{
						echo '<img src="/images/prototype/reviews/emptystar.png" alt=" " title=" " />';
				}

			echo 'User rating: '.$reviews['review_rating'][$topten].'/10<br />
					</div>';
			echo '		<img style="float: left; padding: 0.5em;" src="'.$reviews['review_image'][$topten].'" alt="#" />';
			echo '		<a href="'.$reviews['review_website'][$topten].'">'.$reviews['review_website'][$topten].'</a><br />';
			echo '		'.$reviews['review_blurb'][$topten].'';
			echo '	</div>';
		}
	?>
	<div class="Clear">&nbsp;</div>

