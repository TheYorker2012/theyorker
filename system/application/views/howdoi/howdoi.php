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
		echo '<a href="view">'.$category['name'].'</a><br />';
	}
?>
</div>

<div class="grey_box">
  <h2><?php echo $section_howdoi['title']; ?></h2>
  <?php echo $section_howdoi['text']; ?>
</div>

<div class="blue_box">
<?php
	echo '<h2>Question Categories</h2>';
	foreach ($categories as $key => $category)
	{
		//echo '<h5><a href="'.$category['codename'].'/">'.$category['name'].'</a><br /></h5>';
		echo '<h5>'.$category['name'].'</h5>';
		foreach ($categories[$key]['articles'] as $articles)
		{
			echo '<a href="'.$category['codename'].'/'.$articles['id'].'">'.$articles['heading'].'</a><br />';
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