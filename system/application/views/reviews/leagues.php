<div class='RightToolbar'>
	<h4>Other Leagues</h4>
	<div class='Entry'>
<?php
//Display leagues
if (isset($league_data))
{
	foreach ($league_data as $league_entry)
	{
		echo
		'
		<div class="LifestylePuffer">
		<a href="/reviews/leagues/'.$league_entry['league_codename'].'">
		<img src="'.$league_entry['league_image_path'].'" alt="'.$league_entry['league_name'].'" />
		</a>
		</div>
		';
	}
}
?>

	</div>
</div>

<div class='grey_box'>
	<h2 style="display: inline;"><?php if (isset($league_name) == 1) echo $league_name; else echo('League');?></h2><br />
	Read our latest reviews from all around york! <br />
</div>
<?php
	for($topten=0; ($topten<10) && ($topten < $max_entries); $topten++) {
		echo '<div class="blue_box" >';
		echo '		<div class="ReviewElementNumber" style="text-align: right; font-size: x-small; color: #f26a22;">';
		//Star display -- This may be better placed in the review_model or some library to be honest
		//Display stars
			for ($stars = 0; ($stars < floor($reviews['review_rating'][$topten]/2)); $stars++)
			{
				echo '<img src="/images/prototype/reviews/star.png" alt="*" title="*" />';
			}
			//Display Half Star
			if (($reviews['review_rating'][$topten] % 2) == 1)
			{
				echo '<img src="/images/prototype/reviews/halfstar.png" alt="-" title="-" />';
			}
			else
			{
	 			if ($reviews['review_rating'][$topten] != 10) echo '<img src="/images/prototype/reviews/emptystar.png" alt=" " title=" " />';
			}
			
			//Fill in the blanks
			for ($emptystars = 0; $emptystars < (4 - $stars); $emptystars++)
			{
				echo '<img src="/images/prototype/reviews/emptystar.png" alt=" " title=" " />';
			}
		//End of Print Stars
			
		if ($reviews['review_rating'][$topten] != 0)
		{
			echo '<br />Current rating: '.$reviews['review_rating'][$topten].'/10</div>';
		}
		else
		{
			echo '<br />Not yet rated</div>';
		}

		echo '<h3 style="display: inline;"><a style="color: #20c1f0;" href="'.$reviews['review_link'][$topten].'">'.($topten+1).' - '.$reviews['review_title'][$topten].'</a></h3><br />';
		echo '		<a href="'.$reviews['review_website'][$topten].'">'.$reviews['review_website'][$topten].'</a><br />';
		echo '		'.$reviews['review_blurb'][$topten].'';
		echo '	</div>';
		}
	?>
	<div class="Clear">&nbsp;</div>

