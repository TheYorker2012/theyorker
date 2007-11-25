<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>edit tag</h2>
		<form method="post" action="/office/reviewtags/edit/<?php
				if(!empty($tag_form['tag_id'])){echo $tag_form['tag_id'];}
				?>">
			<fieldset>
				<label for="tag_name">Name:</label>
				<input type="text" name="tag_name" value="<?php
				if(!empty($tag_form['tag_name'])){echo $tag_form['tag_name'];}
				?>" />
				<label for="tag_group_id">Group:</label>
				<select name="tag_group_id"><?php
				foreach ($tag_groups as $tag_group) {
					echo('					<option value="'.$tag_group['group_id'].'"');
					if(!empty($tag_form['tag_group_id']))
							{
								if ($tag_group['group_id']==$tag_form['tag_group_id'])
								{echo 'selected="selected"';}
							}
					echo('>'."\n");
					echo('						'.$tag_group['content_type_name'].'->'.$tag_group['group_name']."\n");
					echo('					</option>'."\n");
				}?>
				</select>
			</fieldset>
			<fieldset>
				<input name="tag_edit" type="submit" value="Edit" class="button" />
			</fieldset>
		</form>
	</div>
	<a href='/office/reviewtags'>Go Back</a>
</div>
