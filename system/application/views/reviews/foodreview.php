	<div id='newsnav'>
		<ul id='newsnavlist'>
		<li><a href='/reviews/'><img src='/images/prototype/news/uk.png' alt='Reviews' title='Reviews' /> Reviews</a></li>
		<li><a href='/reviews/food/' id='current'><img src='/images/prototype/news/feature.gif' alt='Food' title='Food' /> Food</a></li>
		<li><a href='/reviews/drink/'><img src='/images/prototype/news/earth.png' alt='Drink' title='Drink' /> Drink</a></li>
		<li><a href='/reviews/culture/'><img src='/images/prototype/news/archive.png' alt='Culture' title='Culture' /> Culture</a></li>
		<li><a href='/atoz/directory/'><img src='/images/prototype/news/archive.png' alt='A to Z' title='A to Z' /> A to Z</a></li>
		</ul>
	</div>
	<div class="ReviewInfoLeft">
		<h1 class="reviewHeader"><?php echo $article_title; ?></h1><br />
		also does
<?php 
	//Display the correct also menu depending on the value of $also_does_state
	if ($also_does_state >= 4)
		{
		echo '<a href="#">&gt;Food</a>&nbsp;'; //Food - 4
		$also_does_state -= 4;
		}

	if ($also_does_state >= 2)	
		{
		echo '&nbsp;<a href="#">&gt;Drink</a><br />'; //Drink - 2
		$also_does_state -= 2;
		}

	if ($also_does_state == 1)
		{
		echo '&nbsp;<a href="#">&gt;Culture</a><br />'; //Culture - 1
		}

?>

		<img src="/images/prototype/reviews/review_stars.gif" alt="3.5 Stars" title="3 and a Half Stars" /><br />
		<span class="ReviewSubLine"><img src="/images/prototype/news/quote_open.png" alt="''"><?php echo $article_blurb; ?><img src="/images/prototype/news/quote_close.png" alt="''"></span><br />
		<table class="ReviewDetails">
			<tr>
				<td colspan="2"><hr /></td>
			</tr>
			<tr>
				<td class="ReviewDetailsTitle">Address</td>
				<td>
					<?php echo $address_line_1; ?><br />
					<?php echo $address_line_2; ?><br />
					<?php echo $address_line_3; ?><br />
					<?php echo $address_postcode; ?>
				</td>
			</tr>
			<tr>
				<td class="ReviewDetailsTitle">Website</td>
				<td><a href=<?php echo $website; ?>><?php echo $website; ?></a></td>
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
				<td colspan="2"><hr /></td>
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
			<tr>
				<td colspan="2"><hr /></td>
			</tr>
		</table>
		<div class="YourComments">
			<h2>Your Comments</h2>
		</div>

		<div class="AverageRating">
			Average Rating: <span class="AverageRatingSpan">5.7</span>/10<br /><span class="SmallSpanText">(based on 16 votes)</span>
		</div>
<?php
	//No comments
	if ($comments == 'empty')
	{
		echo '<br /><B>No comments at the moment<br />Why not make one it works<br /></B>';
	}
	else
	{
	//Display comments by users - Last 3

		for ($commentno = count($comments['comment_date']) - 1; ($commentno > -1) && ($commentno > count($comments['comment_date']) - 4); $commentno--)
			{
			echo '<div class="WhyNotTry"><b>'.$comments['comment_author'][$commentno].
				 '</b> | '.$comments['comment_date'][$commentno].'<br /> '.$comments['comment_content'][$commentno].
				'</div><br />';
			}

	}
?>
		<div>
		<div class="RateReview">
			Rate this: 
			<select name="comment_rating">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
			</select>
			<input type="Submit" value="Vote" />			

		</div>

		<div>
		<div class="MakeComment">
<?php
	//Allow a user to add a comment - As stolen from the codeigniter video, frb501
	echo form_open('reviews/addcomment');
	echo form_hidden('comment_page_id',$page_id);
	echo form_hidden('comment_subject_id',1);
// Needs to be integereated with login hence psudo code for now...
//if $user = 'logged in' then
	//	echo form_hidden('comment_user_entity_id',$userid)
	//	echo form_hidden('comment_author_name',null);
	//	echo form_hidden('comment_author_email',null);
// else
	echo form_hidden('comment_user_entity_id',null);
	echo '<br />Author Name: <input type="text" name="comment_author_name"><br />';
	echo 'Author Email: <input type="text" name="comment_author_email"><br />Comment: <br />';
// end if
	echo form_hidden('return_page',$this->uri->uri_string());
	echo '<textarea name="comment_text" rows="5"></textarea><br />';
	echo '<input type="submit" value="Add Comment"><br />';

?>	
			<a href=#>Review this Place</a><br />
		</div>
		<div class="AverageRating">
			<a href=#>View All Comments</a><br />
		</div>
		</div>
	</div>


	<div class="ReviewInfoLeft">
		<div class="ReviewInfoLeftImg"><img alt="The Blue Bicycle Image" src="/images/prototype/reviews/reviews_07.jpg" /></div><br />
		<?php echo $article_content; ?>
<br /><br /> 
		<div class="YourComments">
			<h2>Author Reviews</h2>
		</div>
		<div class="WhyNotTry">
			<div class="ArticleColumn" style="width: 100%;">
				<div style='background-color: #DDDDDD;'>
					<div id='Byline' style="background-image: url('/images/prototype/news/benest.png');">
						<div class='RoundBoxBL'>
							<div class='RoundBoxBR'>
								<div class='RoundBoxTL'>
									<div class='RoundBoxTR'>
										<div id='BylineText'>
											Written by<br />
											<span class='name'>IAN BENEST</span><br />
											4/12/2006<br />
											<span class='links'>
												<?php echo anchor('directory/', 'Directory Entry'); ?> | 
												<?php echo anchor('mailto:fred@fred.com', 'Email'); ?> | 
												<?php echo anchor('news/archive/reporter/2/', 'See more...'); ?>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			I didn't like this. It sucked ass. Yo suck ass. Said the Farmer. The farmer doesn't like dan. He doesn't know dan. Dan doesn't know the farmer. Barry Scott sells cillit bang.<br />
		</div>
		<div class="WhyNotTry">
			<div class="ArticleColumn" style="width: 100%;">
				<div style='background-color: #DDDDDD;'>
					<div id='Byline' style="background-image: url('/images/prototype/news/benest.png');">
						<div class='RoundBoxBL'>
							<div class='RoundBoxBR'>
								<div class='RoundBoxTL'>
									<div class='RoundBoxTR'>
										<div id='BylineText'>
											Written by<br />
											<span class='name'>DETLEF PLUMP</span><br />
											5/12/2006<br />
											<span class='links'>
												<?php echo anchor('directory/', 'Directory Entry'); ?> | 
												<?php echo anchor('mailto:fred@fred.com', 'Email'); ?> | 
												<?php echo anchor('news/archive/reporter/2/', 'See more...'); ?>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			Another review here. Here be another review. It be here really. Yarr, tharr be reviews here. Another review here. Here be another review. It be here really. Yarr, tharr be reviews here. Another review here. Here be another review. It be here really. Yarr, tharr be reviews here. Another review here. Here be another review. It be here really. Yarr, tharr be reviews here. <br />
		</div>
	</div>
