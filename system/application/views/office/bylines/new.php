<div id="RightColumn">
	<h2 class="first"><?php echo($whats_this_heading); ?></h2>
	<?php echo($whats_this_text); ?>
</div>

<div id="MainColumn">

	<div class="BlueBox">
		<h2>new byline</h2>

		<form action="/office/bylines/new_byline/" method="post">
			<fieldset>
				<label for="card_name">Name:</label>
				<input type="text" name="card_name" id="card_name" value="<?php echo(xml_escape($this->validation->card_name)); ?>" />
				<br />
				<label for="card_title">Title:</label>
				<input type="text" name="card_title" id="card_title" value="<?php echo(xml_escape($this->validation->card_title)); ?>" />
				<br />
				<label for="group_id">Byline Team:</label>
				<select name="group_id" id="group_id" size="1">
				<?php foreach ($groups as $group) { ?>
					<option value="<?php echo(xml_escape($group['business_card_group_id'])); ?>"
					<?php if ($this->validation->group_id == $group['business_card_group_id'])
						echo('selected="selected"'); ?>>
						<?php echo(xml_escape($group['business_card_group_name'])); ?>
					</option>
				<?php } ?>
				</select>
				<br />
				<label for="card_about">About:</label>
				<textarea name="card_about" id="card_about" cols="25" rows="5"><?php echo(xml_escape($this->validation->card_about)); ?></textarea>
				<br />
				<label for="card_course">Course:</label>
				<input type="text" name="card_course" id="card_course" value="<?php echo(xml_escape($this->validation->card_course)); ?>" />
				<br />
				<label for="card_email">Email:</label>
				<input type="text" name="card_email" id="card_email" value="<?php echo(xml_escape($this->validation->card_email)); ?>" />
				<br />
				<label for="postal_address">Postal Address:</label>
				<input type="text" name="postal_address" id="postal_address" value="<?php echo(xml_escape($this->validation->postal_address)); ?>" />
				<br />
				<label for="phone_mobile">Phone Mobile:</label>
				<input type="text" name="phone_mobile" id="phone_mobile" value="<?php echo(xml_escape($this->validation->phone_mobile)); ?>" />
				<br />
				<label for="phone_internal">Phone Internal:</label>
				<input type="text" name="phone_internal" value="<?php echo(xml_escape($this->validation->phone_internal)); ?>" />
				<br />
				<label for="phone_external">Phone External:</label>
				<input type="text" name="phone_external" value="<?php echo(xml_escape($this->validation->phone_external)); ?>" />
				<br />
				<label for="date_from_day">Display From:</label>
				<select name="date_from_day" id="date_from_day" size="1">
<?php for ($i = 1; $i <= 31; $i++) { ?>
					<option value="<?php echo($i); ?>"<?php if ($i == $this->validation->date_from_day) echo(' selected="selected"'); ?>><?php echo($i); ?></option>
<?php } ?>
				</select>
				<select name="date_from_month" id="date_from_month" size="1">
<?php for ($i = 1; $i <= 12; $i++) { ?>
					<option value="<?php echo($i); ?>"<?php if ($i == $this->validation->date_from_month) echo(' selected="selected"'); ?>><?php echo($i); ?></option>
<?php } ?>
				</select>
				<select name="date_from_year" id="date_from_year" size="1">
<?php for ($i = 2005; $i <= (date('Y')+15); $i++) { ?>
					<option value="<?php echo($i); ?>"<?php if ($i == $this->validation->date_from_year) echo(' selected="selected"'); ?>><?php echo($i); ?></option>
<?php } ?>
				</select>
				<br />
				<label for="date_to_day">Display To:</label>
				<select name="date_to_day" id="date_to_day" size="1">
<?php for ($i = 1; $i <= 31; $i++) { ?>
					<option value="<?php echo($i); ?>"<?php if ($i == $this->validation->date_to_day) echo(' selected="selected"'); ?>><?php echo($i); ?></option>
<?php } ?>
				</select>
				<select name="date_to_month" id="date_to_month" size="1">
<?php for ($i = 1; $i <= 12; $i++) { ?>
					<option value="<?php echo($i); ?>"<?php if ($i == $this->validation->date_to_month) echo(' selected="selected"'); ?>><?php echo($i); ?></option>
<?php } ?>
				</select>
				<select name="date_to_year" id="date_to_year" size="1">
<?php for ($i = 2005; $i <= (date('Y')+15); $i++) { ?>
					<option value="<?php echo($i); ?>"<?php if ($i == $this->validation->date_to_year) echo(' selected="selected"'); ?>><?php echo($i); ?></option>
<?php } ?>
				</select>
				<br />
				<label for="aboutus">Show in About Us?</label>
				<input type="checkbox" name="aboutus" id="aboutus" value="yes"<?php if ($this->validation->aboutus) { echo(' checked="checked"'); } ?> />
				<br />
<?php if ($this->permissions_model->hasUserPermission('BYLINES_GLOBAL')) { ?>
				<label for="global_setting">Global Byline?</label>
				<input type="checkbox" name="global_setting" id="global_setting" value="yes"<?php if ($this->validation->global_setting) { echo(' checked="checked"'); } ?> />
				<br />
<?php } ?>
				<input type="submit" name="add_byline" id="add_byline" value="Create Byline" class="button" />
			</fieldset>
		</form>
	</div>

</div>