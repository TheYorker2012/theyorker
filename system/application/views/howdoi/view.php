<div class="RightToolbar">
	<h4><?php echo $sidebar_ask['title']; ?></h4>
	<?php echo $sidebar_ask['text']; ?>
	<form class="form" action="/howdoi/ask" method="post" >
		<fieldset>
			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
			<textarea name="a_question" cols="24" rows="5">How Do I?</textarea>
			<input type="submit" class="button" value="Ask" name="r_submit_ask" />
		</fieldset>
	</form>
	<br />
	<h4><?php echo $sidebar_question_categories['title']; ?></h4>
<?php
	foreach ($categories as $category)
	{
		echo '<a href="/howdoi/'.$category['codename'].'">'.$category['name'].'</a><br />';
	}
?>
</div>

<div class="grey_box">
	<h2><?php echo $categories[$parameters['category']]['name']; ?></h2>
	<?php echo $categories[$parameters['category']]['blurb']; ?>
</div>

<?php
	if (isset($categories[$parameters['category']]['articles']))
	{
		echo '<div class="blue_box">';
		echo '<h2>'.$question_jump['title'].'</h2>';
		foreach ($categories[$parameters['category']]['articles'] as $questions)
		{
			echo '<a href="'.$parameters['codename'].'#'.$questions['id'].'">'.$questions['heading'].'</a><br />';
		}
		echo '</div>';
	}
?>

<?php
	if (isset($categories[$parameters['category']]['articles']))
	{
		foreach ($categories[$parameters['category']]['articles'] as $questions)
		{
			if (($parameters['article'] <= 0) OR ($questions['id'] == $parameters['article']))
			{
				echo '<a name="'.$questions['id'].'"></a><div class="grey_box">';
				echo '<h2>'.$questions['heading'].'</h2>';
				echo $questions['text'];
				echo '<br />'; //<br />
				//<img src="/images/prototype/directory/about/gmapwhereamI.png" width="400" height="296" alt="" />
				echo '</div>';
			}
		}
	}
?>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>
