<script type='text/javascript' src='/javascript/prototype.js'></script>
<script type='text/javascript' src='/javascript/scriptaculous.js'></script>
<script type='text/javascript' src='/javascript/slideshow.js'></script>
<script type='text/javascript'>
<?php foreach ($organisation['slideshow'] as $slideshow_image){ ?>
Slideshow.add('<?php echo $slideshow_image['id']; ?>');
<?php } ?>
Slideshow.load();
</script>
<div class='RightToolbar'>
	<h4>Information</h4>
	<div id='ss' style='text-align:left;'>
		<img id='changeme' src='/images/prototype/prefs/image_load.jpg' alt='Directory' title='Directory' />
	</div>
	<p>
		<?php if (!empty($organisation['website'])) {
			echo '<img alt="Website" name="Website" src="/images/prototype/directory/link.gif" /> <a href="'.
				$organisation['website'].'">'.$organisation['website'].'</a><br />';
		} ?>
		<?php if (!empty($organisation['email_address'])) {
			echo '<img alt="Email" name="Email" src="/images/prototype/directory/email.gif" /> '.$organisation['email_address'].'<br />';
		} ?>
		<?php if (!empty($organisation['phone_external']) or !empty($organisation['phone_internal']) or !empty($organisation['fax_number'])) {
			echo '<img alt="Phone Number" name="Phone Number" src="/images/prototype/directory/phone.gif" /> ';
			if (!empty($organisation['phone_external'])) {
				echo $organisation['phone_external'].', ';
			}
			if (!empty($organisation['phone_internal'])) {
				echo $organisation['phone_internal'].', ';
			}
			if (!empty($organisation['fax_number'])) {
				echo $organisation['fax_number'].', ';
			}
			echo "<br />";
		}
		?>
		<?php if (!empty($organisation['location']) && !empty($organisation['postcode'])) {
			echo '<img alt="Location" name="Location" src="/images/prototype/directory/flag.gif" /> '.$organisation['location'].','.$organisation['postcode'].'<br />';
		} ?>
		<?php if (!empty($organisation['open_times'])) {
			echo '<img alt="Opening Times" name="Opening Times" src="/images/prototype/directory/clock.gif" /> '.$organisation['open_times'].'<br />';
		} ?>
		<?php if (!empty($organisation['postal_address'])) {
					echo '<img alt="Address" name="Address" src="/images/prototype/directory/address.gif" /> '.$organisation['postal_address'].'<br />';
		} ?>
		<?php if (NULL === $organisation['yorkipedia']){}else{
		echo '<img alt="Yorkipedia Entry" name="Yorkipedia Entry" src="/images/prototype/directory/yorkipedia.gif" /> <a href="'.$organisation['yorkipedia']['url'].'">'.$organisation['yorkipedia']['title'].'</a>';
		}
		?>
	</p>
	<?php
if (!empty($organisation['reviews_by_type'])) {
?>
	<h4>Reviews</h4>
	<div style='padding: 10px 5px 10px 5px;'>
<?php
	foreach ($organisation['reviews_by_type'] as $review_type_name => $reviews) {
		echo '<h5>'.$review_type_name.' reviews:</h5>';
		// $review_type_name: name of review type e.g. food, drink...
		foreach ($reviews as $review) {
			/*
			 * $review is made up of:
			 *	type - same as $review_type_name
			 *	publish_date
			 *	content - as parsed wikitext (html)
			 *	link - where to link (not sure where this is supposed to link
			 *	authors - array of authors, each with:
			 */
			echo '<a href="'.$review['link'].'">By ';
			foreach ($review['authors'] as $author) {
				/* $author is made up of:
				 *	name
				 *	email
				 */
				echo $author['name'].', ';
			}
			echo $review['publish_date'].'</a><br />';
		}
	}
?>
</div>
<?php
}
?>
	<h4>Related articles</h4>
	<div style='padding: 10px 5px 10px 5px;'>
			<h5>Article title 1</h5>
			<a href='#'>A brief description of the article...</a>
			<h5>Article title 2</h5>
			<a href='#'>A brief description of the article...</a>
			<h5>Article title 3</h5>
			<a href='#'>A brief description of the article...</a>
	</div>
</div>
<div style="padding:0px 3px 0px 0px; width: 420px; margin: 0px;">
	<div style="border: 1px solid #2DC6D7; padding: 5px; font-size: small; margin-bottom: 4px; ">
		<span style="font-size: x-large;  color: rgb(32, 193, 240); ">about us</span>
		<p><?php echo $organisation['description']; ?></p>
	</div>
	<div style="border: 1px solid #999; padding: 5px; font-size: small; margin-bottom: 4px; ">
		<h2>finding us</h2>
		<div id="googlemaps" style="width: 100%; height: 300px"></div>
	</div>
</div>
