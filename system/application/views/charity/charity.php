<div id="RightColumn">
	<h2 class="first"><?php echo($sections['sidebar_about']['title']); ?></h2>
	<div class="Entry">
		<?php echo($sections['sidebar_about']['text']); ?>
	</div>

	<h2><?php echo($sections['sidebar_help']['title']); ?></h2>
	<div class="Entry">
		<?php echo($sections['sidebar_help']['text']); ?>
	</div>

<?php
if (count($sections['article']['related_articles']) > 0) {
?>
	<h2><?php echo($sections['sidebar_related']['title']); ?></h2>
	<div class="Entry">
		<ul>
<?php
	foreach ($sections['article']['related_articles'] as $related_articles) {
		echo('			');
		echo('<li><a href="/news/uninews/'.$related_articles['id'].'">'.$related_articles['heading'].'</a></li>'."\n");
	}
?>
		</ul>
	</div>
<?php
}
?>

<?php
if (count($sections['article']['links']) > 0) {
?>
	<h2><?php echo($sections['sidebar_external']['title']); ?></h2>
	<div class="Entry">
		<ul>
<?php
	foreach ($sections['article']['links'] as $link) {
		echo('			');
		echo('<li><a href="'.$link['url'].'" target="_blank">'.$link['name'].'</a></li>'."\n");
	}
?>
		</ul>
	</div>
<?php
}
?>
</div>

<div id="MainColumn">
	<div id="HomeBanner">
		<?php echo($banner) ?>
	</div>
	<div class="BlueBox">
		<h2><?php echo($sections['article']['heading']); ?></h2>
		<?php echo($sections['article']['text']); ?>
	</div>

	<div class="BlueBox">
		<h2><?php echo($sections['funding']['title']); ?></h2>
		<div class="Entry">
			<?php echo($sections['funding']['text']); ?>
		</div>
		<div class="Entry">
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
				<fieldset>
					<input type="hidden" name="cmd" value="_s-xclick" />
					<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" />
					<img alt="" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1" />
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----" />
				</fieldset>
			</form>
			<br />
			<!--<a href="/charity/donate">Alternative methods to donate.</a>-->
		</div>
	</div>

<?php
if (isset($sections['progress_reports']['entries'])) {
	echo('	<div class="BlueBox">'."\n");
	echo('		<h2>'.$sections['progress_reports']['title'].'</h2>'."\n");
	foreach ($sections['progress_reports']['entries'] as $pr_entry) {
		echo('		<h3>'.$pr_entry['date'].'</h3>'."\n");
		echo($pr_entry['text']."\n");
	}
	if ($sections['progress_reports']['totalcount'] > 3)
		echo('<p><a href="/charity/preports/">There are older reports click here to view all progress reports.</a></p>'."\n");
	echo '</div>';
}
?>

</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>
