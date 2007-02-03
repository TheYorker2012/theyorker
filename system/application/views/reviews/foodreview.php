<div class='RightToolbar'>
	<h4>About</h4>
	<div class='Entry'>
		About said review place. Give description about your face
	</div>
	<h4>Details</h4>
	<div class='Entry'>
		<span class="ReviewDetailsTitle">Address</span><br />
			<span class="ReviewDetailsInfo"><?php echo $address_main; ?></span><br />
		<span class="ReviewDetailsTitle">Website</span><br />
			<span class="ReviewDetailsInfo"><a href="<?php echo $website; ?>" target="_new"><?php echo $website; ?></a></span><br />
		<span class="ReviewDetailsTitle">Email</span><br />
				<span class="ReviewDetailsInfo"><a href=><?php echo $email; ?></a></span><br />
		<span class="ReviewDetailsTitle">Book Online</span><br />
				<span class="ReviewDetailsInfo"><a href=#>Click Here/Not Available</a></span><br />
		<span class="ReviewDetailsTitle">Telephone</span><br />
				<span class="ReviewDetailsInfo"><?php echo $telephone; ?></span><br />
		<span class="ReviewDetailsTitle">Opening Times</span><br />
				<span class="ReviewDetailsInfo"><?php echo $opening_times; ?></span><br />
	</div>
	<h4>Tips</h4>
	<div class='Entry'>
		<span class="ReviewDetailsTitle">Yorker Recommends</span><br />
				<span class="ReviewDetailsInfo"><?php echo $yorker_recommendation; ?></span><br />
		<span class="ReviewDetailsTitle">Average Drink Price</span><br />
				<span class="ReviewDetailsInfo"><?php echo $average_price; ?></span><br />
		<span class="ReviewDetailsTitle">Expense Rating</span><br />
				<span class="ReviewDetailsInfo"><?php echo $price_rating; ?></span><br />
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
		<img src="/images/prototype/reviews/star.png" alt="*" title="*" />
		<img src="/images/prototype/reviews/star.png" alt="*" title="*" />
		<img src="/images/prototype/reviews/star.png" alt="*" title="*" />
		<img src="/images/prototype/reviews/emptystar.png" alt=" " title=" " />
		<img src="/images/prototype/reviews/emptystar.png" alt=" " title=" " /><br /><br />
	</div>
</div>
<div class="blue_box">
	<h2>reviews</h2>

<?php
	//Author's reviews - Loop through and display all of them

	for ($article_no = 0; $article_no < count($article); $article_no++)
	{
	echo "
		<img src='".$article[$article_no]['article_photo']."' alt='Reporter' title='Reporter' style='float: right;' />
		<span style='font-size: medium;'><b>".$article[$article_no]['article_author']."</b></span><br />
		".$article[$article_no]['article_date']."<br />
		<span style='color: #ff6a00;'>Read more articles by this reporter</span>
	        <p>
			<span style='color:black;'>".$article[$article_no]['article_content']."</span>
		</p>
		<hr>
		";
	}

?>

</div>
<div class="grey_box">
	<h2>your comments</h2>

	User Rating: <h5 style="display:inline;">5.7</h5>/10 (based on 12 votes)
	<hr>
<?php
	//If not empty
	if (! empty($comments))
	{
		//Show the last 5 comments
		for ($commentno = count($comments['comment_date']) - 1; ($commentno > -1) && ($commentno > count($comments['comment_date']) - 6); $commentno--)
		{
		echo '<b>'.strip_tags($comments['comment_author'][$commentno]).' | '.$comments['comment_date'][$commentno].'</b><br />'.strip_tags($comments['comment_content'][$commentno]).'<hr>';
		}
	}

?>

	<a href=#>View all comments</a><br /><br />
	<h2>add comment</h2>
<?php
	//Allow a user to add a comment - As stolen from the codeigniter video, frb501
	echo form_open('reviews/addcomment');
	echo form_hidden('comment_page_id',$page_id);
	echo form_hidden('comment_article_id',$article_id[0]);
	$userid = 1337;
	echo form_hidden('comment_user_entity_id',$userid);
	echo form_hidden('return_page',$this->uri->uri_string());
	echo '<textarea name="comment_text" rows="4" style="width: 90%; margin-left: 1em;"></textarea><br />';
	echo '&nbsp;&nbsp;&nbsp;Rating:	<SELECT name="component-select">
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
	echo '<input type="submit" value="Add Comment"><br />';
?>
	<br />
</div>
