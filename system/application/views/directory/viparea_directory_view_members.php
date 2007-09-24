<div id="RightColumn">
	<h2 class="first">Groups</h2>
	<div class="Entry">
<?php
	$no_groups = true;
	foreach ($organisation['groups'] as $group) {
?>
		<a href='<?php echo(htmlspecialchars($group['href'])); ?>'>
			<?php
			if($current_group['id']==$group['id']){
				echo("<b>".htmlspecialchars($group['name'])."</b>");
			}else{
				echo(htmlspecialchars($group['name']));
			}
			?>
		</a>
		<br />
<?php
	$no_groups = false;
	}
?>
	</div>

	<h2>Add A New Group</h2>
	<div class="Entry">
	<?php
	if($no_groups){
	echo "<p>You need to create at least one group before you can begin creating contact cards</p>";
	}
	?>
		<form method="post" action="<?php echo(vip_url('directory/contacts')); ?>">
			<fieldset>
				<label for="group_name">Name:</label>
				<input type="text" name="group_name" id="group_name" />
			</fieldset>
			<fieldset>
				<input name="add_group_button" type="submit" id="add_group_button" value="Add" class="button" />
			</fieldset>
		</form>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>edit group - <?php echo($current_group['name']); ?></h2>
		<p>
			rename this group
		</p>
		<form method="post" action="<?php echo(vip_url('directory/contacts')); ?>">
			<fieldset>
				<input name="group_id" type="hidden" value="<?php echo($current_group['id']); ?>" />
			</fieldset>
			<fieldset>
				<label for="group_name">Group Name:</label>
				<input type="text" name="group_name" id="group_name" value="<?php if(!empty($current_group['name'])){echo $current_group['name'];} ?>" />
			</fieldset>
			<fieldset>
				<input name="group_renamebutton" id="group_renamebutton" type="submit" value="Rename" class="button" />
			</fieldset>
		</form>
		<p>
			OR delete this group
		</p>
		<form method="post" action="<?php echo(vip_url('directory/contacts')); ?>">
			<fieldset>
				<input name="group_id" type="hidden" value="<?php echo($current_group['id']); ?>" />
			</fieldset>
			<fieldset>
				<input name="group_deletebutton" id="group_deletebutton" type="submit" value="Delete" class="button" />
			</fieldset>
		</form>
	</div>
<?php
if(empty($organisation['cards'])) {
?>
	<div class="BlueBox">
		<p>
			<b>This group contains no business cards.</b>
		</p>
	</div>
<?php
}
else {
	foreach ($organisation['cards'] as $business_card) {
		$this->load->view('directory/business_card',array(
			'business_card' => $business_card,
			'editmode' => isset($organisation['editmode']),
		));
	}
}
?>

<?php
	if($no_groups==false){
?>
	<div class="BlueBox">
		<h2>add a new business card</h2>
		<form method="post" action="<?php echo(vip_url('directory/contacts')); ?>">
		<fieldset>
			<label for="card_name">Name:</label>
			<input type="text" name="card_name" id="card_name" value="<?php if(!empty($card_form['card_name'])){echo $card_form['card_name'];} ?>" />
			<label for="card_title">Title:</label>
			<input type="text" name="card_title" id="card_title" value="<?php if(!empty($card_form['card_title'])){echo $card_form['card_title'];} ?>" />
			<label for="group_id">Group:</label>
			<select name="group_id" id="group_id">
			<?php
			foreach ($organisation['groups'] as $group) {
				?>
				<option value="<?php echo $group['id'] ?>"
				<?php if(!empty($card_form['group_id']))
						{
							if ($group['id']==$card_form['group_id'])
							{echo 'selected="selected"';}
						}
					?>
					>
					<?php echo $group['name'] ?></option>
				<?php
			}
			?>
			</select>
			<label for="card_username">Yorker Username:</label>
			<input type="text" name="card_username" id="card_username" value="<?php if(!empty($card_form['card_username'])){echo $card_form['card_username'];} ?>"/>
			<label for="card_course">Course:</label>
			<input type="text" name="card_course" id="card_course" value="<?php if(!empty($card_form['card_course'])){echo $card_form['card_course'];} ?>"/>
			<label for="email">Email:</label>
			<input type="text" name="email" id="email" value="<?php if(!empty($card_form['email'])){echo $card_form['email'];} ?>"/>
			<label for="card_about">About:</label>
			<textarea name="card_about" id="card_about" cols="26" rows="7"><?php if(!empty($card_form['card_about'])){echo $card_form['card_about'];} ?></textarea>
			<label for="postal_address">Postal Address:</label>
			<textarea name="postal_address" id="postal_address" cols="26" rows="4"><?php if(!empty($card_form['postal_address'])){echo $card_form['postal_address'];} ?></textarea>
			<label for="phone_mobile">Phone Mobile:</label>
			<input type="text" name="phone_mobile" id="phone_mobile" value="<?php if(!empty($card_form['phone_mobile'])){echo $card_form['phone_mobile'];} ?>"/>
			<label for="phone_internal">Phone Internal:</label>
			<input type="text" name="phone_internal" id="phone_internal" value="<?php if(!empty($card_form['phone_internal'])){echo $card_form['phone_internal'];} ?>"/>
			<label for="phone_external">Phone External:</label>
			<input type="text" name="phone_external" id="phone_external" value="<?php if(!empty($card_form['phone_external'])){echo $card_form['phone_external'];} ?>"/>
		</fieldset>
		<fieldset>
			<input name="card_addbutton" id="card_addbutton" type="submit" value="Add" class="button" />
		</fieldset>
	</form>
	</div>
	<?php
	}
	?>
</div>
