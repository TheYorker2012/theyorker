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
			<?php if(strlen(trim($address)) > 0) { ?><b>Address:</b>  <?php echo($address); ?><br />
			<?php if(strlen(trim($website)) > 0) { ?><b>Website:</b>  <a href="<?php echo(htmlspecialchars($website)); ?>">Click Here</a><br /><?php } ?>
			<?php if(strlen(trim($email)) > 0) { ?><b>Email:</b>  <a href="mailto:<?php echo(htmlspecialchars($email)); ?>"><?php echo(htmlspecialchars($email)); ?></a><br /><?php } ?>
			<?php if(strlen(trim($telephone)) > 0) { ?><b>Telephone:</b>  <?php echo(htmlspecialchars($telephone)); ?><br /><?php } ?>
			<?php if(strlen(trim($opening_times)) > 0) { ?><b>Opening Times:</b>  <?php echo(htmlspecialchars($opening_times)); ?><br /><?php } ?>
			<?php if(strlen(trim($serving_times)) > 0) { ?> <b>Serving Times:</b> <?php echo(htmlspecialchars($serving_times)); ?><br /><?php } ?>
		</p>
	</div>

	<?php if(strlen(trim($yorker_recommendation)) > 0 || $average_price > 0) { ?>
	<h2>Tips</h2>
	<div class="Entry">
			<?php if(strlen(trim($yorker_recommendation)) > 0) { ?><b>We Recommend:</b>  <?php echo(htmlspecialchars($yorker_recommendation)); ?><br /><?php } ?>
			<?php ($average_price > 0) { ?><b>Average Drink Price:</b> &pound;<?php echo(htmlspecialchars($average_price/100)); ?><br /><?php } ?>
	</div>
	<?php } ?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<?php if(count($slideshow) > 0) { ?>
		<div style="float:right;margin-top:0;line-height:95%;">
			<div id="SlideShow" class="entry">
				<img src="<?php echo($slideshow[0]['url']); ?>" id="SlideShowImage" alt="Slideshow" title="Slideshow" />
			</div>

			<script type="text/javascript">
		<?php foreach ($slideshow as $slide_photo) { ?>
			Slideshow.add('<?php echo($slide_photo['url']); ?>');
		<?php } ?>
			Slideshow.load();
			</script>
		</div>
		<?php } ?>

		<h2><?php echo($review_title); ?></h2>

	<?php if ($review_quote != '') { ?>
		<p>
		<img src="/images/prototype/news/quote_open.png" />
		<?php echo($review_quote); ?>
		<img src="/images/prototype/news/quote_close.png" />
		</p>
	<?php } ?>
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
	$this->byline->AddReporter($a['authors']);
	$this->byline->SetDate($a['date']);
	$this->byline->load();
	$this->byline->Reset();

	echo($a['text']);
}
?>
	</div>

</div>
