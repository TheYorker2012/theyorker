<div class='RightToolbar'>
	<h4>About</h4>
	<div class='Entry'>
		<?php echo $organisation_description; ?>
	</div>
	<h4>Details</h4>
	<div class='Entry'>
		<span class="ReviewDetailsTitle">Address</span><br />
			<span class="ReviewDetailsInfo"><?php echo $address_main; ?></span><br />
		<span class="ReviewDetailsTitle">Website</span><br />
			<span class="ReviewDetailsInfo"><a href="<?php echo $website; ?>" target="_new"><?php echo $website; ?></a></span><br />
		<span class="ReviewDetailsTitle">Email</span><br />
				<span class="ReviewDetailsInfo"><a href=><?php echo $email; ?></a></span><br />
		<span class="ReviewDetailsTitle">Telephone</span><br />
				<span class="ReviewDetailsInfo"><?php echo $telephone; ?></span><br />
		<span class="ReviewDetailsTitle">Opening Times</span><br />
				<span class="ReviewDetailsInfo"><?php echo $opening_times; ?></span><br />
		<span class="ReviewDetailsTitle">Serving Times</span><br />
				<span class="ReviewDetailsInfo"><?php echo $serving_times; ?></span><br />
<?php
		if ($deal != NULL)echo '<span class="ReviewDetailsTitle">Current Deal</span><br />
				<span class="ReviewDetailsInfo">'.$deal.'</span><br />';
?>
	</div>
	<h4>Tips</h4>
	<div class='Entry'>
		<span class="ReviewDetailsTitle">Yorker Recommends</span><br />
				<span class="ReviewDetailsInfo"><?php echo $yorker_recommendation; ?></span><br />
		<span class="ReviewDetailsTitle">Average Drink Price</span><br />
				<span class="ReviewDetailsInfo"><?php echo '£'.($average_price/100); ?></span><br />
	</div>
</div>

<div class="grey_box">
	<div style="float: right; width: 60%;">
		<?php echo '<img width="230px" style="float: right;" src="' . $review_image. '" style="width: 220px; margin-bottom: 3px;" />'; ?>
	</div>
	<div style="width:40%">
		<h2 style='margin-bottom: 5px;'><?php echo $review_title; ?></h2><br />
		<h5>"<?php echo $review_blurb; ?>"</h5><br />
		<h4>Rating</h4>
<?php
		//Star display
		//Display stars
		for ($stars = 0; ($stars < $review_rating/2); $stars++)
		{
			echo '<img src="/images/prototype/reviews/star.png" alt="*" title="*" />';
		}
		//Fill in the blanks
		for ($emptystars = 0; $emptystars < (5 - $stars); $emptystars++)
		{
			echo '<img src="/images/prototype/reviews/emptystar.png" alt=" " title=" " />';
		}
?>
	</div>
</div>
<div class="blue_box">
	<h2>reviews</h2>

<?php
	//Author's reviews - Loop through and display all of them

	for ($article_no = 0; $article_no < count($article); $article_no++)
	{

	echo '
		<img src="'.$article[$article_no]['article_photo'].'" alt="Reporter" title="Reporter" style="float: right;" />
		<span style="font-size: medium;"><b>'.$article[$article_no]['article_author'].'</b></span><br />
		'.$article[$article_no]['article_date'].'<br />
		<span style="color: #ff6a00;">Read more articles by this reporter</span>
	        <p>
			<span style="color:black;">'.$article[$article_no]['article_content'].'</span>
		</p>
		';
		if ($article_no < (count($article) - 1)) { echo '<hr>'; }
	}

?>

</div>
<div class="grey_box">
	<h2>your comments</h2>
	<?php
		if ($user_rating == null) echo 'No one has rated this place yet, you could be the first!';
		else { echo 'User Rating: <h5 style="display:inline;">'.$user_rating.'</h5>/10 (based on '.$user_based.' votes)';
	?>
	<hr>
<?php
	//If not empty
	if (! empty($comments))
	{
if ($this->uri->segment(4) != 'all')
	{
		//Show the last 5 comments
		for ($commentno = count($comments['comment_date']) - 1; ($commentno > -1) && ($commentno > count($comments['comment_date']) - 6); $commentno--)
		{
		echo '<b>'.strip_tags($comments['comment_author'][$commentno]).' | '.$comments['comment_date'][$commentno].' | <a href="/reviews/reportcomment/'.$comments['comment_id'][$commentno].'">Report</a></b><br />'.strip_tags($comments['comment_content'][$commentno]).'<hr>';
		}
	}
	else
	{
		//Show all comments
		for ($commentno = count($comments['comment_date']) - 1; ($commentno > -1); $commentno--)
		{
		echo '<b>'.strip_tags($comments['comment_author'][$commentno]).' | '.$comments['comment_date'][$commentno].'</b><br />'.strip_tags($comments['comment_content'][$commentno]).'<hr>';
		}
	}
		
	}
		

?>

<?php

//If not already doing so give option for displaying all comments
if ($this->uri->segment(4) != 'all')
{
	echo '<a href='.$this->uri->uri_string().'/all>View all comments</a><br /><br />';
}

}
echo '<br /><br />';

if ($this->user_auth->entityId > 0)
{
	echo '<h2>add comment</h2>';

	//Allow a user to add a comment
	echo form_open('reviews/addcomment');
	echo form_hidden('comment_type_id',$type_id);
	echo form_hidden('comment_organisation_id',$organisation_id);
	echo form_hidden('comment_article_id',$article_id[0]);
	echo form_hidden('comment_user_entity_id',$this->user_auth->entityId);
	echo form_hidden('return_page',$this->uri->uri_string());
	echo '<fieldset><textarea class="text" name="comment_text" rows="4" style="width: 90%; margin-left: 1em;"></textarea><br />';
	echo 'Rating: <SELECT name="comment_rating">
				<OPTION selected>1</OPTION>
				<OPTION>2</OPTION>
				<OPTION>3</OPTION>
				<OPTION>4</OPTION>
				<OPTION>5</OPTION>
				<OPTION>6</OPTION>
				<OPTION>7</OPTION>
				<OPTION>8</OPTION>
				<OPTION>9</OPTION>
				<OPTION>10</OPTION>
			</SELECT>&nbsp;';
	echo '<input type="submit" class="button" value="Add Comment"></fieldset></form>';
}
else
{
	echo '<i>You can add your own comment by <a href="/login/main'.$this->uri->uri_string().'">logging in</a></i>';
}
?>
	<br />
</div>
