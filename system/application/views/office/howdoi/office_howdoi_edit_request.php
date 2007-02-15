<div class="RightToolbar">
	<h4>Quick Links</h4>
	<?php
		echo '<a href="/office/howdoi/editquestion/'.$parameters['article_id'].'">Return to content</a>';
	?>
</div>

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
		echo '<div class="grey_box">
			<h2>writers</h2>
				<table width="90%" cellpadding="3" align="center">
					<tr>
						<th width="45%">Name</td>
						<th width="30%">Status</td>
						<th width="25%">Options</td>
					</tr>';
		foreach ($writers['article'] as $writer)
		{
			echo '<tr>
					<td>'.$writer['name'].'</td>
					<td>'.$writer['status'].'</td>
					<td>
						<form class="form" action="/office/howdoi/writermodify" method="post" ><fieldset>
							<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
							<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
							<input type="hidden" value="'.$writer['id'].'" class="button" name="r_userid" />
							<input type="submit" value="Remove" class="button" name="r_submit_remove" />
						</fieldset></form>
					</td>
				</tr>';
		}
		echo '</table>';
		if ($writers['availcount'] > 0)
		{
			echo '<form class="form" action="/office/howdoi/writermodify" method="post" >
					<fieldset>
						<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
						<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
						<label for="a_addwriter">Add New Writer:</label>
						<select name="a_addwriter">';
			foreach ($writers['available'] as $writer)
			{
				echo '<option value="'.$writer['id'].'">'.$writer['name'].'</option>';
			}
			echo '</select>
						<input type="submit" value="Add" class="button" name="r_submit_add" />
					</fieldset>
				</form>';
		}
		echo '</div>';
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