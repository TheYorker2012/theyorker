<div id="RightColumn">
	<h2 class="first">Quick Links</h2>
	<div class="Entry">
		<ul>
			<li><a href="/office/howdoi/editquestion/<?php echo($parameters['article_id']); ?>">Return to content</a></li>
		</ul>
	</div>
</div>
<div id="MainColumn">
	<?php
	if (($article['header']['status'] == 'request') or ($article['header']['status'] == 'published'))
	{
		echo('<div class="BlueBox">');
		echo('<h2>request info</h2>');
		if ($user['officetype'] == 'Low')
		{
			echo('<b>Title: </b>'.xml_escape($article['header']['requesttitle']).'<br />
				<b>Description: </b>'.xml_escape($article['header']['requestdescription']).'<br />
				</div>');
		}
		else
		{
			echo('<form class="form" action="/office/howdoi/questionmodify" method="post" >
					<fieldset>
						<input type="hidden" name="r_status" value="'.xml_escape($article['header']['status']).'" >
						<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
						<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
						<label for"a_title">Title:</label>
						<input type="text" name="a_title" value="'.xml_escape($article['header']['requesttitle']).'" />
						<label for="a_category">Category:</label>
						<select name="a_category">');
			foreach ($categories as $category_id => $category)
			{
				echo('<option value="'.xml_escape($category['codename']).'"');
				if ($category_id == $article['header']['content_type'])
					echo(' selected="selected"');
				echo('>'.xml_escape($category['name']).'</option>');
				//echo '<option selected="selected">Opening Times</option>';
			}
			echo('</select><br />
						<label for"a_description">Description:</label>
						<textarea name="a_description" rows="5" cols="30" />'.xml_escape($article['header']['requestdescription']).'</textarea>
						<input type="submit" value="Modify" class="button" name="r_submit_modify" />
					</fieldset>
				</form>
			</div>');
			echo('<div class="BlueBox">
				<h2>writers</h2>
					<table width="90%" cellpadding="3" align="center">
						<tr>
							<th width="45%">Name</td>
							<th width="30%">Status</td>
							<th width="25%">Options</td>
						</tr>');
			foreach ($writers['article'] as $writer)
			{
				echo('<tr>
						<td>'.xml_escape($writer['name']).'</td>
						<td>'.xml_escape($writer['status']).'</td>
						<td>
							<form class="form" action="/office/howdoi/writermodify" method="post" ><fieldset>
								<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
								<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
								<input type="hidden" value="'.$writer['id'].'" class="button" name="r_userid" />
								<input type="submit" value="Remove" class="button" name="r_submit_remove" />
							</fieldset></form>
						</td>
					</tr>');
			}
			echo('</table>');
			if ($writers['availcount'] > 0)
			{
				echo('<form class="form" action="/office/howdoi/writermodify" method="post" >
						<fieldset>
							<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
							<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
							<label for="a_addwriter">Add New Writer:</label>
							<select name="a_addwriter">');
				foreach ($writers['available'] as $writer)
				{
					echo('<option value="'.$writer['id'].'">'.xml_escape($writer['name']).'</option>');
				}
				echo('</select>
							<input type="submit" value="Add" class="button" name="r_submit_add" />
						</fieldset>
					</form>');
			}
			echo('</div>');
		}
	}
	?>
</div>
