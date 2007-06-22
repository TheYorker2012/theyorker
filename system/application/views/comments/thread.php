<?php

/**
 * @file views/comments/thread.php
 * @brief View for thread information and rater.
 *
 * @param $Thread array_thread Thread information with:
 *	- 'allow_anonymous_comments' bool Whether to allow comment to be anonymous.
 * @param $LoggedIn bool Whether a user is logged in.
 * @param $LoginUrl string Url of login page.
 * @param $RatingTarget string Target of rating form.
 */

if (!function_exists('star_rating_large')) {
	function star_rating_large ($rating,$text) {
		$xhtml = '';
		$star_count = 0;
		$rating_left = $rating;
	
		while ($rating_left >= 2) {
			$xhtml .= '<img src="/images/prototype/reviews/star.png" alt="'.$text.' Rating: '.$rating.'" title="'.$text.' Rating: '.$rating.'" />';
			$star_count++;
			$rating_left -= 2;
		}
		if ($rating_left == 1) {
			$xhtml .= '<img src="/images/prototype/reviews/halfstar.png" alt="'.$text.' Rating: '.$rating.'" title="'.$text.' Rating: '.$rating.'" />';
			$star_count++;
			$rating_left--;
		}
		while ($star_count < 5) {
			$xhtml .= '<img src="/images/prototype/reviews/emptystar.png" alt="'.$text.' Rating: '.$rating.'" title="'.$text.' Rating: '.$rating.'" />';
			$star_count++;
		}
		return $xhtml;
	}
}

if ($Thread['allow_ratings']) {
	echo('<div class="BlueBox">');
	echo('<h2>User comments/ratings</h2>');
	if (NULL !== $Thread['average_rating']) {
		echo('<p>Average rating of '.star_rating_large($Thread['average_rating'],'Average').
			$Thread['num_ratings'].' user ratings</p>');
	}
	if (!$LoggedIn) {
		echo('<p>You must <a href="'.$LoginUrl.'">log in</a> to rate this page</p>');
	} else {
		if (NULL !== $Thread['user_rating']) {
			echo('<p>Your Rating: '.star_rating_large($Thread['user_rating'],'Your').'</p>');
		} else {
			echo('<p>You have not yet rated this page</p>');
		}
		?>
		<form class="form" method="post" accept="<?php echo $RatingTarget; ?>">
			<fieldset>
				<label for="UserRatingValue">Your rating:</label>
				<select name="UserRatingValue">
					<option value="no">no rating</option>
					<?php
					for ($rating_value = 1; $rating_value <= 10; ++$rating_value) {
						echo('<option value="'.$rating_value.'" ');
						if ($rating_value == $Thread['user_rating']) {
							echo('selected="selected" ');
						}
						echo('>'.$rating_value.'</option>');
					}
					?>
				</select>
				<input type="submit" class="button" value="Submit" name="UserRatingSubmit" />
			</fieldset>
		</form>
		<?php
	}
	echo('</div>');
}
?>