<div class='RightToolbar'>
<?php echo '<img src="' . $review_image. '" style="width: 220px; margin-bottom: 3px;" />'; ?>
		<h4>User Rating</h4>
		STARS!
		<h4>User Reviews</h4>
	<?php
	//No comments
	if (empty($comments))
	{
		echo '<br /><B>No comments at the moment<br />Why not make one it works<br /></B>';
	}
	else
	{
	//Display comments by users - Last 3
	for ($commentno = count($comments['comment_date']) - 1; ($commentno > -1) && ($commentno > count($comments['comment_date']) - 4); $commentno--)
		{
			echo '<div class="WhyNotTry"><b>'.$comments['comment_author'][$commentno].'</b> | '.$comments['comment_date'][$commentno].'<br /> '.$comments['comment_content'][$commentno].'</div><br />';
		}

	}
	
?>
		<div>
<?php
	//Allow a user to add a comment - As stolen from the codeigniter video, frb501
	echo form_open('reviews/addcomment');
	echo form_hidden('comment_page_id',$page_id);
	echo form_hidden('comment_article_id',$article_id);
	$userid = 1337;
	echo form_hidden('comment_user_entity_id',$userid);

	echo '<br />Comment: <br />';

	echo form_hidden('return_page',$this->uri->uri_string());
	echo '<textarea name="comment_text" rows="5" style="width: 220px;"></textarea><br />';
	echo '<input type="submit" value="Add Comment"><br />';
?>
		</div>
		<div class="AverageRating">
			<a href="#">View All Comments</a><br />
		</div>
		</div>

<div class='grey_box'>
			<h2 style='margin-bottom: 5px;'><?php echo $review_title; ?></h2>
			<img src="/images/prototype/reviews/review_stars.gif" alt="3.5 Stars" title="3 and a Half Stars" />
	        <p><?php echo $review_blurb; ?></p>
		<table class="ReviewDetails">
			<tr>
				<td class="ReviewDetailsTitle">Address</td>
				<td>
					<?php echo $address_main; ?><br />
					<?php echo $address_postcode; ?>
				</td>
			</tr>
			<tr>
				<td class="ReviewDetailsTitle">Website</td>
				<td><a href="<?php echo $website; ?>" target="_new"><?php echo $website; ?></a></td>
			</tr>
			<tr>
				<td class="ReviewDetailsTitle">Email</td>
				<td><a href=><?php echo $email; ?></a></td>
			</tr>
			<tr>
				<td class="ReviewDetailsTitle">Book Online</td>
				<td><a href=#>Click Here/Not Available</a></td>
			</tr>
			<tr>
				<td class="ReviewDetailsTitle">Telephone</td>
				<td><?php echo $telephone; ?></td>
			</tr>
			<tr>
				<td class="ReviewDetailsTitle">Opening Times</td>
				<td><?php echo $opening_times; ?></td>
			</tr>
			<tr>
				<td class="ReviewDetailsTitle">Yorker Recommends</td>
				<td><?php echo $yorker_recommendation; ?></td>
			</tr>
			<tr>
				<td class="ReviewDetailsTitle">Average Drink Price</td>
				<td><?php echo $average_price; ?></td>
			</tr>
			<tr>
				<td class="ReviewDetailsTitle">Expense Rating</td>
				<td><?php echo $price_rating; ?></td>
			</tr>
		</table>


</div>
<div class='blue_box'>
		<img src='/images/prototype/news/benest.png' alt='Reporter' title='Reporter' style='float: right;' />
		<h2 style='margin-bottom: 0px; '>Chris Travis</h2>
		<div style='font-size: x-small; margin-bottom: 2px;'>Thursday, 25th January 2007</div>
		I didn't like this. It sucked ass. Yo suck ass. Said the Farmer. The farmer doesn't like dan. He doesn't know dan. Dan doesn't know the farmer. Barry Scott sells cillit bang.<br />
</div>
<div class='blue_box'>
		<img src='/images/prototype/news/benest.png' alt='Reporter' title='Reporter' style='float: right;' />
		<h2 style='margin-bottom: 0px; '>Chris Travis</h2>
		<div style='font-size: x-small; margin-bottom: 2px;'>Thursday, 25th January 2007</div>
		I didn't like this. It sucked ass. Yo suck ass. Said the Farmer. The farmer doesn't like dan. He doesn't know dan. Dan doesn't know the farmer. Barry Scott sells cillit bang.<br />
</div>
<div class='blue_box'>
		<img src='/images/prototype/news/benest.png' alt='Reporter' title='Reporter' style='float: right;' />
		<h2 style='margin-bottom: 0px; '>Chris Travis</h2>
		<div style='font-size: x-small; margin-bottom: 2px;'>Thursday, 25th January 2007</div>
		I didn't like this. It sucked ass. Yo suck ass. Said the Farmer. The farmer doesn't like dan. He doesn't know dan. Dan doesn't know the farmer. Barry Scott sells cillit bang.<br />
</div>
