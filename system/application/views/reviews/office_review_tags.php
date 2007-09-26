<div class='RightToolbar'>
	<h4>Page Information</h4>
	<p>
		<?php echo $page_information; ?>
	</p>
</div>
<div id="MainColumn">
	<div class="blue_box">
		<h2>tagged as...</h2>
		<p>
		<?php
		echo form_open('office/reviews/deltag');
		echo form_hidden('organisation_name',$organisation_name);
		echo form_hidden('context_type',$context_type);
		?>
		<select size="5" name="tag" style="float: none; width: 350px;"><?php
		foreach ($existing_tags['tag_group_names'] as $tag_group_name)
		{
			foreach ($existing_tags[$tag_group_name] as $tag)
			{
				echo '<option value="'.$tag.'">'.$tag_group_name.' -> '.$tag.'</option>';
			}
		}
		?></select><br />
	 	<input type="submit" value="Delete tag" style="float: none;">
		</form>
		</p>
	</div>
	<div class="blue_box">
		<h2>potential tags</h2>
		<p>
		<?php
		echo form_open('office/reviews/addtag');
		echo form_hidden('organisation_name',$organisation_name);
		echo form_hidden('context_type',$context_type);
		?>
		<select size="8" name="tag" style="float: none;  width: 350px;"><?php
		foreach ($new_tags['tag_group_names'] as $tag_group_name)
		{
			foreach ($new_tags[$tag_group_name] as $tag)
			{
				echo '<option value="'.$tag.'">'.$tag_group_name.' -> '.$tag.'</option>';
			}
		}
		?></select><br />
	 	<input type="submit" value="Add new tag" style="float: none;">
		</form>
		</p>
	</div>
	<a href="/office/reviewlist/<?php echo $context_type; ?>">Back to the attention list</a>
</div>

