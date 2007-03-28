<div id="RightColumn">
	<h2 class="first">About</h2>
	<div class="Entry">
		<?php echo($organisation_description); ?>
	</div>

	<h2>Details</h2>
	<div class="Entry">
		<p>
<?php
$address = $address_main;
$address = str_replace("\r", '', $address);
$address = str_replace("\n", ', ', $address);
$address = htmlspecialchars($address);
?>
			Address: <?php echo($address); ?><br />
			Website: <?php echo(htmlspecialchars($website)); ?><br />
			Email: <?php echo(htmlspecialchars($email)); ?><br />
			Telephone: <?php echo(htmlspecialchars($telephone)); ?><br />
			Opening Times: <?php echo(htmlspecialchars($opening_times)); ?><br />
			Serving Times: <?php echo(htmlspecialchars($serving_times)); ?><br />
<?php
if ($deal != NULL) {
?>
			Current Deal: <?php echo(htmlspecialchars($deal)); ?><br />
<?php
}
?>
		</p>
	</div>

	<h2>Tips</h2>
	<div class="Entry">
			Yorker Recommends: <?php echo(htmlspecialchars($yorker_recommendation)); ?><br />
			Average Drink Price: £<?php echo(htmlspecialchars($average_price/100)); ?><br />
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo($review_title); ?></h2>
		<img src="<?php echo($review_image); ?>" alt="<?php echo($review_title); ?>" />
		<p><?php echo($review_blurb); ?></p>
		<h3>Rating</h3>
		<div>
<?php
echo('			');
$star = 0;
while ($star < floor($review_rating/2)) {
	echo('<img src="/images/prototype/reviews/star.png" alt="*" title="*" />');
	$star++;
}
if ($review_rating % 2 == 1) {
	echo('<img src="/images/prototype/reviews/halfstar.png" alt="-" title="-" />');
	$star++;
}
while ($star < 5) {
	echo('<img src="/images/prototype/reviews/emptystar.png" alt=" " title=" " />');
	$star++;
}
?>

		</div>
	</div>

	<div class="BlueBox">
		<h2>reviews</h2>
<?php
foreach($article as $a) {
	$this->byline->AddReporter($a['article_authors']);
	$this->byline->SetDate($a['article_date']);
	$this->byline->load();
	$this->byline->Reset();

	echo($a['article_content']);
}
?>
	</div>

	<div class="BlueBox">
		<h2>your comments</h2>
		<p>
<?php
echo('			');
if ($user_rating == null)
	echo('No one has rated this place yet, you could be the first!');
else
	echo('User Rating: '.$user_rating.'/10 (based on '.$user_based.' votes)');
?>

		</p>
		<hr />
<?php
for ($i = 0; ($i < 5 || $this->uri->segment(4) == 'all') & $i < count($comments); $i++) {
	echo('		<h3>'.htmlentities($comments[$i]['comment_author']));
	echo(' | '.htmlentities($comments[$i]['comment_date']));
	echo(' |  <a href="/reviews/reportcomment/'.$comments[$i]['comment_id'].'">Report</a></h3>'."\n");
	echo('		<p>'.htmlentities($comments[$i]['comment_content']).'</p>'."\n");
}

if ($this->uri->segment(4) != 'all')
	echo('<p><a href='.$this->uri->uri_string().'/all>View all comments</a></p>');

if (!$this->user_auth->isLoggedIn) {
?>
		<p>Please <a href="/login/main<?php echo($this->uri->uri_string()); ?>">log in</a> to add comments</p>
	</div>
<?php
} else {
?>
	</div>

	<div class="BlueBox">
		<h2>add comment</h2>
		<form action="/reviews/addcomment" method="post">
			<fieldset>
				<input type="hidden" name="comment_type_id" value="<?php echo($type_id); ?>" />
				<input type="hidden" name="comment_organisation_id" value="<?php echo($organisation_id); ?>" />
				<input type="hidden" name="comment_article_id" value="<?php echo($article_id[0]); ?>" />
				<input type="hidden" name="comment_user_entity_id" value="<?php echo($this->user_auth->entityId); ?>" />
				<input type="hidden" name="return_page" value="<?php echo($this->uri->uri_string()) ?>" />
				<label for="comment_text">Comments: </label>
					<textarea name="comment_text"></textarea><br />
				<label for="comment_rating">Rating: </label>
					<select name="comment_rating">
						<option>1</option>
						<option>2</option>
						<option>3</option>
						<option>4</option>
						<option>5</option>
						<option>6</option>
						<option>7</option>
						<option>8</option>
						<option>9</option>
						<option>10</option>
					</select>
			</fieldset>
			<fieldset>
				<input type="submit" value="AddComment" class="button" />
			</fieldset>
		</form>
	</div>
<?php
}
?>
</div>
