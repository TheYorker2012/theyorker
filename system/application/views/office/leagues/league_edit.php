<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
	<h2>Current Image</h2>
	<div class="Entry" align='center'>
		<p>
			<?php echo $image; ?>
		</p>
	</div>
	<h2>Image Options</h2>
	<div class="Entry">
		<ul>
			<li><a href="/office/leagues/changeimage/<?php echo $league_form['league_id']; ?>">Change/Add Image</a></li>
			<?php if($has_image){?>
			<li><a href="/office/leagues/deleteimage/<?php echo $league_form['league_id'];
			?>" onclick="return(confirm ('Are you sure you want to remove this image?'));" >Delete Image</a></li>
			<?php } ?>
		</ul>
	</div>
</div>
<div id="MainColumn">

	<div class="BlueBox">
		<h2>edit league</h2>
		<form method="post" action="/office/leagues/edit/<?php echo $league_form['league_id']; ?>">
			<fieldset>
				<label for="league_name">Name:</label>
				<input type="text" name="league_name" value="<?php
				if(!empty($league_form['league_name'])){echo $league_form['league_name'];}
				?>" />
				<input type="hidden" name="league_id" value="<?php echo $league_form['league_id']; ?>">
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
				<input name="league_edit" type="submit" value="Edit" class="button" />
			</fieldset>
		</form>
	</div>
	<a href='/office/leagues'>Go Back</a>
</div>