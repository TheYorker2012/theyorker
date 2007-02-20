<div class="RightToolbar">
	<h4><?php echo $sidebar_ask['title']; ?></h4>
	<?php echo $sidebar_ask['text']; ?>
	<form class="form" action="/howdoi/ask" method="post" >
		<fieldset>
			<?php echo '<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'; ?>
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
  <h2><?php echo $section_howdoi['title']; ?></h2>
  <?php echo $section_howdoi['text']; ?>
</div>

<div class="blue_box">
<?php
	echo '<h2>'.$question_categories['title'].'</h2>';
	foreach ($categories as $key => $category)
	{
		//echo '<h5><a href="'.$category['codename'].'/">'.$category['name'].'</a><br /></h5>';
		echo '<h5>'.$category['name'].'</h5>';
		foreach ($categories[$key]['articles'] as $articles)
		{
			echo '<a href="/howdoi/'.$category['codename'].'#'.$articles['id'].'">'.$articles['heading'].'</a><br />';
		}
	}
?>
</div>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>