<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>tagged as...</h2>
		<p>
		<?php
		echo(form_open('office/reviews/deltag'));
		echo(form_hidden('organisation_name',$organisation_name));
		echo(form_hidden('context_type',$context_type));
		?>
		<select size="5" name="tag" style="float: none; width: 350px;"><?php
		foreach ($existing_tags['tag_group_names'] as $tag_group_name)
		{
			foreach ($existing_tags[$tag_group_name] as $tag)
			{
				echo '<option value="'.xml_escape($tag).'">'.xml_escape($tag_group_name).' -> '.xml_escape($tag).'</option>';
			}
		}
		?></select><br />
	 	<input type="submit" value="Delete tag" style="float: none;">
		</form>
		</p>
	</div>
	<div class="BlueBox">
		<h2>potential tags</h2>
		<p>
		<?php
		echo(form_open('office/reviews/addtag'));
		echo(form_hidden('organisation_name',$organisation_name));
		echo(form_hidden('context_type',$context_type));
		?>
		<select size="8" name="tag" style="float: none;  width: 350px;"><?php
		foreach ($new_tags['tag_group_names'] as $tag_group_name)
		{
			foreach ($new_tags[$tag_group_name] as $tag)
			{
				echo '<option value="'.xml_escape($tag).'">'.xml_escape($tag_group_name).' -> '.xml_escape($tag).'</option>';
			}
		}
		?></select><br />
	 	<input type="submit" value="Add new tag" style="float: none;">
		</form>
		</p>
	</div>
	<a href="/office/reviewlist/<?php echo($context_type); ?>">Back to the attention list</a>
</div>

