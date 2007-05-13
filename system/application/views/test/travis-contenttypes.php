	<form name="add_type" id="add_type" action="/test/travis/addtype/" method="post">
		<div class="blue_box">
			<fieldset>
				<label for="c_name">Content type name:</label>
				<input type="text" name="c_name" value="Blog Surf" size="35" />
				<br />
				<label for="c_dname">Directory name:</label>
				<input type="text" name="c_dname" value="theyorker-sections-blogsurf" size="35" />
				<br />
				<label for="c_codename">Code Name:</label>
				<input type="text" name="c_codename" value="blogsurf" size="35" />
				<br />
				<label for="c_blurb">Blurb:</label>
				<input type="text" name="c_blurb" value="on transport..." size="35" />
				<br />
				<label for="c_parent">Parent Category:</label>
				<select name="c_parent" size="1">
					<option value="NULL" selected="selected">--NONE--</option>
<?php foreach ($parents as $p) { ?>
					<option value="<?php echo($p->id); ?>"><?php echo($p->name); ?></option>
<?php } ?>
				</select>
				<br />
				<label for="c_children">Has Children:</label>
				<select name="c_children" size="1">
					<option value="0" selected="selected">No</option>
					<option value="1">Yes</option>
				</select>
				<br />
				<label for="c_section">Section:</label>
				<select name="c_section" size="1">
					<option value="news" selected="selected">News</option>
					<option value="reviews">reviews</option>
					<option value="blogs">blogs</option>
				</select>
				<br />

				<input type="submit" class="button" name="c_add" value="Add Content Type" />
				<br />
			</fieldset>
		</div>
	</form>