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

?>

<?php
if ($Thread['allow_ratings']) {
	echo('<div class="BlueBox">');
	echo('<h2>User comments/ratings</h2>');
	if (NULL !== $Thread['average_rating']) {
		echo('<p>Average rating of '.$Thread['average_rating'].' from '.
			$Thread['num_ratings'].' user ratings</p>');
	}
	if (!$LoggedIn) {
		echo('<p>You must <a href="'.$LoginUrl.'">log in</a> to rate this page</p>');
	} else {
		if (NULL !== $Thread['user_rating']) {
			echo('<p>You have rated this page as '.$Thread['user_rating'].'</p>');
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
/*} else {
	echo('<h2>User comments</h2>');*/
}
?>