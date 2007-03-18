<div id="RightColumn">
	<h2 class="first"><?php echo($sidebar_ask['title']); ?></h2>
	<div class="Entry">
		<?php echo($sidebar_ask['text']); ?>
		<form action="/howdoi/ask" method="post" >
			<fieldset>
				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
				<textarea name="a_question" cols="24" rows="5">How Do I?</textarea>
				<input type="submit" class="button" value="Ask" name="r_submit_ask" />
			</fieldset>
		</form>
	</div>

	<h2><?php echo($sidebar_question_categories['title']); ?></h2>
	<div class="Entry">
		<ul>
<?php
	foreach ($categories as $category) {
		echo('			');
		echo('<li><a href="/howdoi/'.$category['codename'].'">'.$category['name'].'</a></li>'."\n");
	}
?>
		</ul>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo($categories[$parameters['category']]['name']); ?></h2>
		<p><?php echo($categories[$parameters['category']]['blurb']); ?></p>
	</div>

<?php
if (isset($categories[$parameters['category']]['articles'])) {
	echo('	<div class="BlueBox">'."\n");
	echo('		<h2>'.$question_jump['title'].'</h2>'."\n");
	echo('		<ul>'."\n");
	foreach ($categories[$parameters['category']]['articles'] as $questions) {
		echo('			');
		echo('<li><a href="'.$parameters['codename'].'#q'.$questions['id'].'">'.$questions['heading'].'</a></li>'."\n");
	}
	echo('		</ul>'."\n");
	echo('	</div>'."\n");
}
?>

<?php
if (isset($categories[$parameters['category']]['articles'])) {
	foreach ($categories[$parameters['category']]['articles'] as $questions)
	{
		if (($parameters['article'] <= 0) OR ($questions['id'] == $parameters['article']))
		{
			echo('	<div class="BlueBox" id="q'.$questions['id'].'">'."\n");
			echo('		<h2>'.$questions['heading'].'</h2>'."\n");
			echo('		'.$questions['text']."\n");
			echo('	</div>'."\n");
		}
	}
}
?>
</div>
