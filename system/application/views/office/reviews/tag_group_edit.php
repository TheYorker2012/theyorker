<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>edit tag group</h2>
		<form method="post" action="/office/reviewtags/editgroup/<?php
				if(!empty($tag_group_form['tag_group_id'])) {
					echo($tag_group_form['tag_group_id']);
				}
				?>">
			<fieldset>
				<label for="tag_group_name">Name:</label>
				<input type="text" name="tag_group_name" value="<?php
				if(!empty($tag_group_form['tag_group_name'])) {
					echo(xml_escape($tag_group_form['tag_group_name']));
				}
				?>" />
				<label for="content_type_id">Section:</label>
				<select name="content_type_id"><?php
				foreach ($group_types as $group_type) {
					echo('					<option value="'.$group_type['type_id'].'"');
					if(!empty($tag_group_form['content_type_id'])) {
						if ($group_type['type_id'] == $tag_group_form['content_type_id']) {
							echo('selected="selected"');
						}
					}
					echo('>'."\n");
					echo('						'.xml_escape($group_type['type_name'])."\n");
					echo('					</option>'."\n");
				}?>
				</select>
				<label for="tag_group_ordered">Ordered:</label>
				<input type="checkbox" name="tag_group_ordered" value="1" <?php
				if(empty($tag_group_form) || !empty($tag_group_form['tag_group_ordered'])) {
					echo('checked="checked"');
				}
				?>/>
			</fieldset>
			<fieldset>
				<input name="tag_group_edit" type="submit" value="Edit" class="button" />
			</fieldset>
		</form>
	</div>
	<a href='/office/reviewtags'>Go Back</a>
</div>
