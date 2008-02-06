<div id="RightColumn">
	<div class="first">
		<h2>Links</h2>
		<a href="/culture">Back to Culture</a>
		<a href="/reviews">Back to Reviews</a>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo $crawl_title; ?></h2><img alt="Wasted Man" style="float: right" src="<?php echo $crawl_image; ?>" />
		<p><?php echo $crawl_rating; ?></p>
		<p><strong><?php echo $crawl_blurb; ?></strong></p>
		<p><?php echo $crawl_content; ?></p>
	</div>
	<div class="BlueBox">
		<h2>Author Reviews</h2>
		<h3>Dan Ashbytonville</h3>
		<p><a href="mailto:doggydan@woof.com">DoggyDan@woof.com</a> | <b>Editor</b> | Posted on 3rd December 2002</p>
		<p>I didn't like this. It sucked ass. Yo suck ass. Said the Farmer. The farmer doesn't like dan. He doesn't know dan. Dan doesn't know the farmer. Barry Scott sells cillit bang.</p>
		</div>
	
<h2>Have Your Say</h2>
</div>
	</div>
	<div class="ReviewInfoRight">
		<h3>Cost:</h3>
		£12
		<h3>List of Pubs</h3>
		<ul>
			<li><?php echo $pub_list[0]; ?>
			<li><?php echo $pub_list[1]; ?>
			<li><?php echo $pub_list[2]; ?>
		</ul>
		<h3>Directions</h3>
		<?php echo $crawl_directions; ?>
		<h3>The Drink Guide</h3>
		<table width=100%>
		<tr>
		<td><h3>Pub</h3></td>
		<td><h3>Recommended Drink</h3></td>
		<td><h3>Cost</h3></td>
		</tr>
<?php 

	for ($item = 0; $item < count($drink_guide); $item++)
	{
		echo '	<tr>
				<td>'.$drink_guide[$item][0].'<br /></td>
				<td>'.$drink_guide[$item][1].'<br /></td>
				<td>'.$drink_guide[$item][2].'<br /></td>
				</tr>
			 ';
	}

?>
		</table>
		
	</div>
<div class="Clear">&nbsp;</div>
</div>

<div class="ReviewInfo">
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
</div>
	<div class="MakeComment">
<?php
	//Allow a user to add a comment - As stolen from the codeigniter video, frb501
	echo form_open('reviews/addcomment');
	echo form_hidden('comment_page_id',$page_id);
	echo form_hidden('comment_article_id',$article_id);
	$userid = 1337;
	echo form_hidden('comment_user_entity_id',$userid);

	echo '<br />Comment: <br />';

	echo form_hidden('return_page',$this->uri->uri_string());
	echo '<textarea name="comment_text" rows="5"></textarea><br />';
	echo '<input type="submit" value="Add Comment"><br />';
?>
			<a href=#>Review this Place</a><br />
	</div>
</div>
