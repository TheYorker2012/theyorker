<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>create league</h2>
		<form method="post" action="/office/leagues/create">
			<fieldset>
				<label for="league_image_id">Image:</label>
				<input type="hidden" name="league_image_id" value="<?php if(!empty($league_form['league_image_id'])){ echo $league_form['league_image_id']; }?>"><?php
				if(!empty($image_preview)){echo $image_preview;} ?>
				<label for="league_name">Name:</label>
				<input type="text" name="league_name" value="<?php
				if(!empty($league_form['league_name'])){echo $league_form['league_name'];}
				?>" />
				<label for="league_type">League Type:</label>
				<select name="league_type"><?php
				foreach ($league_types as $league_type) {
					?>
					<option value="<?php echo $league_type['id'] ?>"
					<?php if(!empty($league_form['league_type']))
							{
								if ($league_type['id']==$league_form['league_type'])
								{echo 'selected="selected"';}
							}
						?>
						>
						<?php echo $league_type['name'] ?></option>
					<?php
				}
				?></select>
				<label for="league_size">League Size:</label>
				<input type="text" name="league_size" value="<?php
				if(!empty($league_form['league_size'])){echo $league_form['league_size'];}
				?>" />
			</fieldset>
			<fieldset>
				<input name="league_add" type="submit" value="Add" class="button" />
			</fieldset>
		</form>
	</div>
</div>