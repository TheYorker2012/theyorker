<?php
if ($article['header']['status'] == 'request')
{
	echo '<div class="blue_box">';
	echo '<h2>request info</h2>';
	if ($user['officetype'] == 'Low')
	{
		echo '<b>Title: </b>'.$article['header']['requesttitle'].'<br />
			<b>Description: </b>'.$article['header']['requestdescription'].'<br />
			</div>';
	}
	else
	{
		echo '<form class="form" action="/office/howdoi/questionmodify" method="post" >
				<fieldset>
					<input type="hidden" name="r_status" value="'.$article['header']['status'].'" >
					<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
					<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
					<label for"a_title">Title:</label>
					<input type="text" name="a_title" value="'.$article['header']['requesttitle'].'" />
					<label for="a_category">Category:</label>
					<select name="a_category">';
					foreach ($categories as $category_id => $category)
					{
						echo '<option value="'.$category_id.'"';
						if ($category_id == $article['header']['content_type'])
							echo ' selected';
						echo '>'.$category['name'].'</option>';
						//echo '<option selected>Opening Times</option>';
					}
					echo '</select><br />
					<label for"a_description">Description:</label>
					<textarea name="a_description" rows="5" cols="30" />'.$article['header']['requestdescription'].'</textarea>
					<input type="submit" value="Modify" class="button" name="r_submit_modify" />
				</fieldset>
			</form>
		</div>';
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