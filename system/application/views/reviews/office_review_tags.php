<div class='RightToolbar'>
	<h4>Areas for Attention</h4>
	<div class="Entry">
		<div class="information_box">
			The following reviews are waiting to be published:
			<ul>
				<li><a href='#'>Dan Ashby 02/02/2007</a></li>
				<li><a href='#'>Charlotte Chung 02/02/2007</a></li>
			</ul>
		</div>
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			<a href='#'>Information</a> has been updated and is waiting to be published.
		</div>
		<div class="information_box">
			There are <a href='#'>Comments</a> that have been reported for abuse.
		</div>
	</div>
<h4>What's this?</h4>
	<p>
		<?php echo 'whats_this'; ?>
	</p>
<h4>Other tasks</h4>
<ul>
	<li><a href='#'>Maintain my account</a></li>
	<li><a href='#'>Remove this directory entry</a></li>
</ul>
</div>

<?php

	echo form_open('office/reviews/addtag');
	echo form_hidden('organisation_name',$organisation_name);
	echo form_hidden('context_type',$context_type);
	echo '<div class="blue_box">';
	echo '<h2>list boxes for each tag group</h2>';
	echo '<b>Possible tags</b><br />';
	echo '<select size=4 name=tag>';
	foreach ($new_tags['tag_group_names'] as $tag_group_name)
	{
		foreach ($new_tags[$tag_group_name] as $tag)
		{
			echo '<option value="'.$tag.'">'.$tag_group_name.' -> '.$tag.'</option>';
		}
	}
	echo '</select><br />';
 	echo '<input type="submit" value="Add new tag">';
	echo '</form><br /><br />';

	echo form_open('office/reviews/deltag');
	echo form_hidden('organisation_name',$organisation_name);
	echo form_hidden('context_type',$context_type);
	echo '<b>Current tags</b><br />';
	echo '<select size=4 name=tag>';
	foreach ($existing_tags['tag_group_names'] as $tag_group_name)
	{
		foreach ($existing_tags[$tag_group_name] as $tag)
		{
			echo '<option value="'.$tag.'">'.$tag_group_name.' -> '.$tag.'</option>';
		}
	}
	echo '</select><br />';
 	echo '<input type="submit" value="Delete tag">';
	echo '</div></form>';

?>

