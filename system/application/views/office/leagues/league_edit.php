<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
	<h2>Current Image</h2>
	<div class="Entry" align='center'>
		<p>
			<?php echo($image); ?>
		</p>
	</div>
	<h2>Image Options</h2>
	<div class="Entry">
		<ul>
			<li><a href="/office/leagues/changeimage/<?php echo($league_form['league_id']); ?>">Change/Add Image</a></li>
			<?php if($has_image){?>
			<li><a href="/office/leagues/deleteimage/<?php echo($league_form['league_id']);
			?>" onclick="return(confirm ('Are you sure you want to remove this image?'));" >Delete Image</a></li>
			<?php } ?>
		</ul>
	</div>
	<h2>Related Actions</h2>
	<div class="Entry">
		<ul>
			<li><a href="/office/league/edit/<?php echo($league_id); ?>">Edit league's content.</a></li>
			<li><a href="/office/reviewtags">Create/Edit Tags.</a></li>
		</ul>
	</div>
</div>
<div id="MainColumn">
	<form method="post" action="/office/leagues/edit/<?php echo($league_form['league_id']); ?>">
		<div class="BlueBox">
			<h2>edit league</h2>
			<fieldset>
				<label for="league_name">Name:</label>
				<input type="text" name="league_name" value="<?php
				if(!empty($league_form['league_name'])) {
					echo(xml_escape($league_form['league_name']));
				}
				?>" />
				<input type="hidden" name="league_id" value="<?php echo $league_form['league_id']; ?>">
				<label for="league_type">League Type:</label>
				<select name="league_type"><?php
				foreach ($league_types as $league_type) {
					?>
					<option value="<?php echo $league_type['id'] ?>"
					<?php if(!empty($league_form['league_type']))
							{
								if ($league_type['id']==$league_form['league_type']) {
									echo 'selected="selected"';
								}
							}
						?>
						>
						<?php echo(xml_escape($league_type['name'])); ?></option>
					<?php
				}
				?></select>
				<label for="league_size">League Size:</label>
				<input type="text" name="league_size" value="<?php
				if(!empty($league_form['league_size'])) {
					echo $league_form['league_size'];
				}
				?>" />
			</fieldset>
		</div>
		<div class="BlueBox">
			<h2>current tags</h2>
			<?php echo $tags_current_text; ?>
			<fieldset>
				<select size="5" name="current_tags[]" style="float: none; width: 350px;" multiple>
				<?php
				foreach ($current_tags as $tag)
				{
					echo '				<option value="'.$tag['tag_id'].'">'.xml_escape($tag['tag_group_name']).' -> '.xml_escape($tag['tag_name']).'</option>'."\n";
				}
				?>
				</select>
			</fieldset>
		</div>
		<div class="BlueBox">
			<h2>new tags</h2>
			<?php echo($tags_new_text); ?>
			<fieldset>
				<select size="8" name="new_tags[]" style="float: none;  width: 350px;" multiple>
				<?php
				foreach ($new_tags as $tag)
				{
					echo '				<option value="'.$tag['tag_id'].'">'.xml_escape($tag['tag_group_name']).' -> '.xml_escape($tag['tag_name']).'</option>'."\n";
				}
				?>
				</select>
			</fieldset>
			
			<fieldset>
				<input name="league_edit" type="submit" value="Update League" class="button" />
			</fieldset>
		</div>
	</form>
	<a href='/office/leagues'>Go Back</a>
</div>