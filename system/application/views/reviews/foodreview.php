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
		<img src='/images/prototype/news/benest.png' alt='Reporter' title='Reporter' style='float: right;' />
		<span style='font-size: medium;'><b>Chris Travis</b></span><br />
		25th March 200<br />
		<span style='color: #ff6a00;'>Read more articles by this reporter</span>
	        <p>
			<span style="color:black;">A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks about nothing.</span>
		</p>
		<hr>
		<img src='/images/prototype/news/benest.png' alt='Reporter' title='Reporter' style='float: right;' />
		<span style='font-size: medium;'><b>Chris Travis^2</b></span><br />
		25th March 200<br />
		<span style='color: #ff6a00;'>Read more articles by this reporter</span>
	        <p>
			<span style="color:black;">A stupid whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks about nothing.</span>
		</p>
</div>
<div class="grey_box">
	<h2>your comments</h2>
	User Rating: <h5 style="display:inline;">5.7</h5>/10 (based on 12 votes)
	<hr>
	<b>some dousche | 03-01-2007</b><br />
	The guy talks about some shit and someone disagrees with it. Then someone sees this and agrees with it, and argues with the guy who was sad enough to post about disagreeing.	<hr>
	<b>some dousche | 03-01-2007</b><br />
	The guy talks about some shit and someone disagrees with it. Then someone sees this and agrees with it, and argues with the guy who was sad enough to post about disagreeing.	<hr>
	<b>some dousche | 03-01-2007</b><br />
	The guy talks about some shit and someone disagrees with it. Then someone sees this and agrees with it, and argues with the guy who was sad enough to post about disagreeing.
	<hr>
	<a href=#>View all comments</a><br /><br />
	<h2>add comment</h2>
<?php
	//Allow a user to add a comment - As stolen from the codeigniter video, frb501
	echo form_open('reviews/addcomment');
	echo form_hidden('comment_page_id',$page_id);
	echo form_hidden('comment_article_id',$article_id);
	$userid = 1337;
	echo form_hidden('comment_user_entity_id',$userid);
	echo form_hidden('return_page',$this->uri->uri_string());
	echo '<textarea name="comment_text" rows="4" style="width: 90%; margin-left: 1em;"></textarea><br />';
	echo '&nbsp;&nbsp;&nbsp;Rating:	<SELECT name="component-select">
				<OPTION selected>1</OPTION>
				<OPTION >2</OPTION>
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
