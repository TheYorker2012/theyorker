<div id="RightColumn">
	<h2 class="first"><?php echo($sidebar_petition['title']); ?></h2>
	<div class="Entry">
		<?php echo(str_replace('%%count%%', $campaign['signatures'], $sidebar_petition['text'])); ?>

	</div>

	<h2><?php echo($sidebar_sign['title']); ?></h2>
	<div class="Entry">
<?php
if ($user != FALSE) {
	if ($user['vote_id'] == FALSE) {
		echo('		');
		echo(str_replace('%%name%%', $user['firstname'].' '.$user['surname'], $sidebar_sign['new_text']));
		echo("\n");
?>
		<form name="sign" action="/campaign/signpetition" method="post">
			<fieldset>
				<input type="hidden" name="r_redirecturl" value="<?php echo($_SERVER['REQUEST_URI']); ?>" />
				<input type="password" name="a_password" />
			</fieldset>
			<fieldset>
				<input type="submit" value="Sign" class="button" name="r_sign" />
			</fieldset>
		</form>
<?php
	} else {
		echo('		');
		echo(str_replace('%%name%%', $user['firstname'].' '.$user['surname'], $sidebar_sign['withdraw_text']));
		echo("\n");
?>
		<form name="sign" action="/campaign/withdrawsignature" method="post">
			<fieldset>
				<input type="hidden" name="r_redirecturl" value="<?php echo($_SERVER['REQUEST_URI']); ?>" />
			</fieldset>
			<fieldset>
				<input type="submit" value="Withdraw" class="button" name="r_withdraw" />
			</fieldset>
		</form>
<?php
	}
} else {
		echo('		'.$sidebar_sign['not_logged_in']."\n");
}
?>
	</div>

	<h2><?php echo($sidebar_more['title']); ?></h2>
	<div class="Entry">
		<?php echo($sidebar_more['text']); ?>
	</div>

<?php
if(count($article['related_articles']) > 0) {
	echo('	<h2>'.$sidebar_related['title'].'/h2>'."\n");
	echo('	<div class="Entry">'."\n");
	echo('		<ul>'."\n");
	foreach ($article['related_articles'] as $related_articles) {
		echo('		');
		echo('<li><a href="http://www.google.com/">'.$related_articles['heading'].'</a></li>'."\n");
	}
	echo('		</ul>'."\n");
	echo('	</div>'."\n");
}
?>

<?php
if(count($article['links']) > 0) {
	echo('	<h2>'.$sidebar_external['title'].'/h2>'."\n");
	echo('	<div class="Entry">'."\n");
	echo('		<ul>'."\n");
	foreach ($article['links'] as $link) {
		echo('		');
		echo('<li><a href="'.$link['url'].'">'.$link['name'].'</a></li>'."\n");
	}
	echo('		</ul>'."\n");
	echo('	</div>'."\n");
}
?>

    	<h2><?php echo($sidebar_comments['title']); ?></h2>
	<div class="Entry">
		#TODO
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo($our_campaign['title']); ?></h2>
		<?php echo($article['text']); ?>
	</div>

<?php
foreach ($article['fact_boxes'] as $fact_box) {
	echo('	<div class="BlueBox">'."\n");
	echo('		<h2>'.$fact_box['title'].'</h2>'."\n");
	echo($fact_box['wikitext']);
	echo('	</div>'."\n");
}

if (isset($sections['progress_reports']['entries'])) {
	echo('	<div class="BlueBox">'."\n");
	echo('		<h2>'.$progress_reports['title'].'</h2>');
	foreach ($sections['progress_reports']['entries'] as $pr_entry) {
		echo('			');
		echo('<h3>'.$pr_entry['date'].'</h3>'."\n");
		echo('			');
		echo($pr_entry['text']);
	}
	echo('	</div>'."\n");
}
?>
</div>


<?php /*
	if (isset($progress_reports['entries']))
	{
		echo '<div class="grey_box">';
		echo '<span style="font-size: x-large;  color: #BBBBBB; ">'.$progress_reports['title'].'</span><br />';
		foreach ($progress_reports['entries'] as $pr_entry)
		{
			echo '<br>';
			echo '<span style="font-size: large;  color: #BBBBBB; ">'.$pr_entry['date'].'</span><br />';
			echo $pr_entry['text'].'<br />';
		}
		echo '</div>';
} */
?>
