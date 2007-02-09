<div class="RightToolbar">
	<h4>Areas for Attention</h4>
	<div class="Entry">
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b>3</b> <a href='#'>Questions</a> that are waiting to be published.
		</div>
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b>3</b> <a href='#'>Suggestions</a> that require attention.
		</div>
	</div>
	<h4>Question Categories</h4>
	<div class="Entry">
	<a href="/office/howdoi/editquestion">Opening Times</a><br />
	<a href="/office/howdoi/editquestion">Numbers</a><br />
	<a href="/office/howdoi/editquestion">Essentials</a><br />
	<a href="/office/howdoi/editquestion">Other Info</a><br />
	<a href="/office/howdoi/editquestion">The Nearest ...</a><br />
	</div>
</div>
<div class="grey_box">
	<h2>add category</h2>
	<form class="form" action="/office/howdoi/categoryadd" method="post" >
		<fieldset>
			<label for="title">Name: </label>
			<input type="text" name="title" />
			<input type="submit" class="button" value="Create" />
		</fieldset>
	</form>
</div>
<div class="blue_box">
	<h2>edit categories</h2>
	Type in a name next to the category to rename it - otherwise leave it blank.
	<?php
	foreach ($categories as $category_id => $category)
	{
		/*
		echo '<form class="form" action="/office/howdoi/categoryadd" method="post" ><fieldset>';
		echo '<tr>';
//		echo '<input type="hidden" name="a_categoryid" id="a_categoryid" value="'.$category_id.'" />';
		echo '<td width="50%" align="right" style="padding: 0px 5px 0px 0px">';
		echo $category['name'];
		echo '</td>';
		echo '<td width="50%" align="left" style="padding: 0px 0px 0px 5px">';
		//echo '<a href="/office/howdoi/categoryedit/'.$category_id.'">[edit]</a>'.' ';
		echo '<a href="/office/howdoi/categorydelete/'.$category_id.'">[delete]</a>';
		//echo '<input type="submit" name="r_submit_edit" id="r_submit_edit" value="Edit" class="button" />';
		echo '</td>';
		echo '</tr>';
		echo '</fieldset></form>';
		//echo '<input type="text" name="name" value="'.$category['name'].'" /> <a href="'.$category['codename'].'">[delete]</a><br />';
		*/
		echo '<form class="form" action="/office/howdoi/categorymodify" method="post" >
			<fieldset>
				<label for="r_submit_edit">'.$category['name'].'</label>
				<input type="hidden" name="a_categoryid" id="a_categoryid" value="'.$category_id.'" />
				<input type="submit" name="r_submit_delete" id="r_submit_delete" value="Delete" class="button" />
				<input type="submit" name="r_submit_edit" id="r_submit_edit" value="Edit" class="button" />
			</fieldset>
			</form>
			<br />';
	}
	?>

</div>

<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>