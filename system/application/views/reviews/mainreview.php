<div id="RightColumn">
	<h2 class="first">About</h2>
	<div class="Entry">
		<?php echo($review_blurb); ?>
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
		<?$this->image->getThumb($review_image, 'slideshow', false, array('class' => 'Right'))?>
		<h2><?php echo($review_title); ?></h2>
		<p><?php echo($review_quote); ?></p>
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

</div>
