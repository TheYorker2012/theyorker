<div id="RightColumn">
	<h2 class="first">Other tasks</h2>
	<ul>
		<li>
			<a href="/office/bylines/add_photo/<?php echo($byline_info['business_card_id']); ?>/">
				Upload a new photo
			</a>
		</li>
		<li>
			<a href="/office/bylines/delete_byline/<?php echo($byline_info['business_card_id']); ?>/">
				Delete this byline
			</a>
		</li>
	</ul>
	<h2><?php echo($whats_this_heading); ?></h2>
	<?php echo($whats_this_text); ?>
</div>

<div id="MainColumn">

<?php $this->load->view('/office/bylines/byline', $byline_info); ?>
	<div style="float:left;">
		<b>Owner:</b>
		<a href="/office/bylines/user/<?php echo(($byline_info['business_card_user_entity_id'] == NULL) ? '-1' : $byline_info['business_card_user_entity_id']); ?>">
			<?php if (($byline_info['user_firstname'] == NULL) && ($byline_info['user_surname'] == NULL)) {
				echo('GLOBAL');
			} else {
				echo(xml_escape($byline_info['user_firstname'] . ' ' . $byline_info['user_surname']));
			} ?>
		</a>
		<b>Team:</b>
		<a href="/office/bylines/view_team/<?php echo($byline_info['business_card_business_card_group_id']); ?>">
			<?php echo(xml_escape($byline_info['business_card_group_name'])); ?>
		</a>
		<b>Status:</b> <?php echo(($byline_info['business_card_approved']) ? '<span style="color:darkgreen">Approved</span>' : '<span style="color:red">Pending</span>'); ?>
		<br />
		<b>Display:</b> <?php echo(date('d/m/y', $byline_info['business_card_start_date']) . ' - ' . date('d/m/y', $byline_info['business_card_end_date'])); ?>
		<br />
		<?php if ($byline_info['business_card_about_us']) echo('<span style="color:red"><b>ABOUT US PAGE</b></span>'); ?>
		<div class="clear"></div>
	</div>

	<div class="BlueBox">
		<h2>edit byline</h2>

		<form action="/office/bylines/view_byline/<?php echo($byline_info['business_card_id']); ?>/" method="post">
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
					<option value="<?php echo $group['business_card_group_id'] ?>"
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
				<input type="submit" name="edit_byline" id="edit_byline" value="Update Byline" class="button" />
			</fieldset>
		</form>
	</div>

	<div class="BlueBox">
		<h2>byline status</h2>

		<div>
			<?php if ($byline_info['business_card_approved']) { ?>
				This byline has been approved by an editor and is now in use.
			<?php } else { ?>
				Changes have been made to this byline which need to be approved by an editor before it can be used.
				<?php /// @todo FIXME this logic should be in the controller with a view parameter e.g. @a $allow_approve
					if ($this->access == 'editor') { ?>
					<form action="/office/bylines/approve/<?php echo($byline_info['business_card_id']); ?>/" method="post">
						<input type="submit" name="approve" id="approve" value="Approve Byline" class="button" />
					</form>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>