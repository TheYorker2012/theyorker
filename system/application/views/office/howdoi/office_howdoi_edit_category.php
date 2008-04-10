<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>edit category</h2>
		<form class="form" action="/office/howdoi/categorymodify" method="post" >
			<fieldset>
				<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="<?php echo($_POST['r_redirecturl']); ?>" />
				<input type="hidden" name="r_categoryid" id="r_categoryid" value="<?php echo($category['id']); ?>" />
				<label for="title">Name: </label>
				<input type="text" name="a_name" value="<?php echo(xml_escape($category['name'])); ?>" />
				<label for="codename">Codename: </label>
				<input type="text" name="a_codename" value="<?php echo(xml_escape($category['codename'])); ?>" />
				<label for="blurb">Category Blurb: </label>
				<textarea name="a_blurb" cols="30" rows="5"><?php echo($category['blurb']); ?></textarea>
				<input type="submit" class="button" value="Save" name="r_submit_save" />
			</fieldset>
		</form>
	</div>
</div>