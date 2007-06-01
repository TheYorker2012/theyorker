<?php
function nls2p($str)
{
  return str_replace('<p></p>', '',
        preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p>$0<p>', $str)
        );
}
?>

<script type="text/javascript" src="/javascript/prototype.js"></script>
<script type="text/javascript" src="/javascript/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="/javascript/slideshow_new.js"></script>


<div id="RightColumn">
	<h2 class="first">Information</h2>
	<?php if(count($organisation['slideshow']) > 0) { ?>
		<div id="SlideShow" class="entry">
			<img src="<?php echo($organisation['slideshow'][0]['url']); ?>" id="SlideShowImage" alt="Slideshow" title="Slideshow" />
		</div>

		<script type="text/javascript">
	<?php foreach ($organisation['slideshow'] as $slide_photo) { ?>
		Slideshow.add('<?php echo($slide_photo['url']); ?>');
	<?php } ?>
		Slideshow.load();
		</script>
	<?php } ?>
	<div class="Entry">
	<table>
<?php
if (!empty($organisation['website'])) {
	echo('		<tr><td><img alt="Website" title="Website" src="/images/prototype/directory/link.gif" /></td><td>');
	echo('<a href="'.$organisation['website'].'">');
	echo('Our Website');
	echo('</a></td></tr>'."\n");
}
if (!empty($organisation['email_address'])) {
	echo('		<tr><td><img alt="Email" title="Email" src="/images/prototype/directory/email.gif" /></td> <td>');
	echo($organisation['email_address'].'</td></tr>'."\n");
}
if (!empty($organisation['phone_external'])) {
	echo('		<tr><td><img alt="Phone Number" title="Phone Number" src="/images/prototype/directory/phone.gif" /></td><td> ');
	echo($organisation['phone_external'].'</td></tr>'."\n");
}
if (!empty($organisation['phone_internal'])) {
	echo('		<tr><td><img alt="Phone Number" title="Phone Number" src="/images/prototype/directory/phone.gif" /></td><td> ');
	echo($organisation['phone_internal'].'</td></tr>'."\n");
}
if (!empty($organisation['fax_internal'])) {
	echo('		<tr><td><img alt="Fax Number" title="Fax Number" src="/images/prototype/directory/phone.gif" /></td><td> ');
	echo($organisation['fax_internal'].'</td></tr>'."\n");
}
/*if (!empty($organisation['location'])) {
	echo('		<tr><td><img alt="Location" title="Location" src="/images/prototype/directory/flag.gif" /></td><td> ');
	echo($organisation['location'].'</td></tr>'."\n");
}*/
if (!empty($organisation['open_times'])) {
	echo('		<tr><td><img alt="Opening Times" title="Opening Times" src="/images/prototype/directory/clock.gif" /></td><td> ');
	echo(nl2br($organisation['open_times']).'</td></tr>'."\n");
}
if (!empty($organisation['postal_address'])) {
	echo('		<tr><td><img alt="Address" title="Address" src="/images/prototype/directory/address.gif" /></td><td> ');
	echo(nl2br($organisation['postal_address']).'</p>');
	if (!empty($organisation['postcode']))
		echo('<p>'.$organisation['postcode'].'</td></tr>');
}
if ($organisation['yorkipedia'] !== NULL) {
	echo('		<tr><td><img alt="Yorkipedia Entry" title="Yorkipedia Entry" src="/images/prototype/directory/yorkipedia.gif" /></td><td> ');
	echo('<a href="'.$organisation['yorkipedia']['url'].'">');
	echo($organisation['yorkipedia']['title']);
	echo('</a></td></tr>'."\n");
}
?>
	</table>
	</div>

<?php
if (!empty($organisation['reviews_by_type'])) {
?>
	<!-- TODO: Rewrite this section -->
	<h2>Reviews</h2>
	<div class="Entry">
<?php
	foreach ($organisation['reviews_by_type'] as $review_type_name => $reviews) {
		echo('		<h5>'.$review_type_name.' reviews</h5>'."\n");
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
			echo('<a href="'.$review['link'].'">By ');
			foreach ($review['authors'] as $author) {
				/* $author is made up of:
				 *	name
				 *	email
				 */
				echo($author['name'].', ');
			}
			echo($review['publish_date'].'</a><br />'."\n");
		}
	}
?>
	</div>
<?php
}
?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>about us</h2>
		<?php echo('<p>'.nls2p($organisation['description']).'</p>'); ?>
	</div>
<?php
if($organisation['location_lat'] !== NULL) {
?>
	<div class="BlueBox">
		<h2>finding us</h2>
		<div id="googlemaps" style="height: 300px"></div>
	</div>
<?php
}
?>
</div>
