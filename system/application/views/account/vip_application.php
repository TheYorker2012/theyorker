<div id="RightColumn">
	<h2><?php echo ($vip_help_heading); ?></h2>
	<div class="Entry">
		<?php echo ($vip_help_text); ?>
	</div>
</div>

<div id="MainColumn">
	<form action="<?php echo($org_id); ?>" method="post">
		<div class="BlueBox">
			<h2>Apply to be a VIP</h2>
			<p>Please complete the form below to apply to become a VIP for the requested organisation:</p>
			<fieldset>
				<label for="v_organisation">Organisation: </label>
				<div id="v_organisation"><?php echo($org_name['name']); ?></div>
				<br />
				<label for="v_position">Position in organisation: </label>
				<input type="text" id="v_position" name="v_position" value="<?php echo($this->input->post('v_position')); ?>" />
				<br />
				<label for="v_phone">Contact Phone Number (optional): </label>
				<input type="text" id="v_phone" name="v_phone" value="<?php echo($this->input->post('v_phone')); ?>" />
				<br />
			</fieldset>
		</div>
	 	<input type="submit" name="v_apply" id="v_apply" value="Apply" class="button" />
	</form>
</div>