<div class="RightToolbar">
  <h4><?php echo $sidebar_ask['title']; ?></h4>
  <?php echo $sidebar_ask['text']; ?>
  <br /><br />
  Give us a challenge
  <form name='' id='' action='' method='' class='form'>
    <fieldset>
      <textarea>How Do I?</textarea>
      <input type="submit" value="Ask" class="button" />
    </fieldset>
  </form>
  <br />
<?php
	echo '<h4>Question Categories</h4>';
	foreach ($categories as $category)
	{
		echo '<a href="'.$category['codename'].'">'.$category['name'].'</a><br />';
	}
?>
<?php
	echo '<h4>Quick Question Jump</h4>';
	foreach ($categories[$parameters['category']]['articles'] as $questions)
	{
		echo '<a href="'.$questions['id'].'">'.$questions['heading'].'</a><br />';
	}
?>
</div>

<div class="grey_box">
	<h2><?php echo $categories[$parameters['category']]['name']; ?></h2>
	<?php echo $categories[$parameters['category']]['blurb']; ?>
</div>

<?php
	foreach ($categories[$parameters['category']]['articles'] as $questions)
	{
		echo '<div class="blue_box">';
		echo '<h2>'.$questions['heading'].'</h2>';
		echo $questions['text'];
		echo '<br />'; //<br />
		//<img src="/images/prototype/directory/about/gmapwhereamI.png" width="400" height="296" alt="" />
		echo '</div>';
	}
?>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>
