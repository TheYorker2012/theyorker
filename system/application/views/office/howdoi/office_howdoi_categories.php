<div class="RightToolbar">
</div>
<div class="blue_box">
	<h2>edit categories</h2>
<?php
	foreach ($categories as $category_id => $category)
	{
		echo '<form class="form" action="/office/howdoi/categorymodify" method="post" >
			<fieldset>
				<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
				<label for="r_submit_edit">'.$category['name'].'</label>
				<input type="hidden" name="r_categoryid" id="r_categoryid" value="'.$category_id.'" />
				<input type="submit" name="r_submit_delete" id="r_submit_delete" value="Delete" class="button" />
				<input type="submit" name="r_submit_edit" id="r_submit_edit" value="Edit" class="button" />
			</fieldset>
			</form>
			<br />';
	}
?>
	<div class="ArticleBox">
		<table>
			<thead>
				<tr>
					<th style="width:40%;">Name</th>
					<th style="width:60%;text-align:right;">Sort</th>
				</tr>
			</thead>
			<tbody>
<?php
foreach ($categories as $category_id => $category)
	{
	echo('				<tr>'."\n");
	echo('					<td>'."\n");
	echo('						<a href="">'.$category['name'].'</a>'."\n");
	echo('					</td>'."\n");
	echo('					<td style="text-align:right;">'."\n");
	echo('						<a href="">[Move Up]</a> <a href="">[Move Down]</a>'."\n");
	echo('					</td>'."\n");
	echo('				</tr>'."\n");
	}
?>
			</tbody>
		</table>
	</div>
</div>
<?php
echo '<div class="blue_box">
	<h2>add category</h2>
	<form class="form" action="/office/howdoi/categorymodify" method="post" >
		<fieldset>
			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
			<label for="title">Name: </label>
			<input type="text" name="a_categoryname" />
			<input type="submit" name="r_submit_add" id="r_submit_add" class="button" value="Create" />
		</fieldset>
	</form>
</div>';
?>

<div class="grey_box">
	<h2>sort categories</h2>
	Move the categories up and down into the order you want them in.
	<?php
	$category_count = count($categories);
	foreach ($categories as $category_id => $category)
	{
		echo '<form class="form" action="/office/howdoi/categorymodify" method="post" >
			<fieldset>
				<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
				<label for="r_submit_edit">'.$category['name'].'</label>
				<input type="hidden" name="r_sectionorder" id="r_sectionorder" value="'.$category['section_order'].'" />';
				if ($category['section_order'] != 1)
					echo '<input type="submit" name="r_submit_up" id="r_submit_up" value="Move Up" class="button" />';
				else
					echo '<input type="submit" name="r_submit_up" id="r_submit_up" value="Move Up" class="disabled_button" disabled />';
				if ($category['section_order'] != $category_count)
					echo '<input type="submit" name="r_submit_down" id="r_submit_down" value="Move Down" class="button" />';
				else
					echo '<input type="submit" name="r_submit_down" id="r_submit_down" value="Move Down" class="disabled_button" disabled />';
			echo '</fieldset>
			</form>
			<br />';
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
