<div class="RightToolbar">
	<h4>Areas for Attention</h4>
	<div class="Entry">
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b>?</b> <a href='#'>Questions</a> that are waiting to be published.
		</div>
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b>?</b> <a href='#'>Suggestions</a> that require attention.
		</div>
	</div>
</div>
<?php
echo('<div class="grey_box">
	<h2>edit category</h2>
	<form class="form" action="/office/howdoi/categorymodify" method="post" >
		<fieldset>
			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_POST['r_redirecturl'].'" />
			<input type="hidden" name="r_categoryid" id="r_categoryid" value="'.$category['id'].'" />
			<label for="title">Name: </label>
			<input type="text" name="a_name" value="'.xml_escape($category['name']).'" />
			<label for="codename">Codename: </label>
			<input type="text" name="a_codename" value="'.xml_escape($category['codename']).'" />
			<label for="blurb">Category Blurb: </label>
			<textarea name="a_blurb" cols="30" rows="5">'.$category['blurb'].'</textarea>
			<input type="submit" class="button" value="Save" name="r_submit_save" />
		</fieldset>
	</form>
</div>');
?>
