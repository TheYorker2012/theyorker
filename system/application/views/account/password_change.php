<?php echo $main_text; ?>

<div class="blue_box">
	<H2>Change Password</H2>
	<?php echo form_open($change_password_target, array('class' => 'form')); ?>
	<fieldset>
		<label for="oldpassword">Current password:</label>
		<?php echo form_password(array('name' => 'oldpassword', 'size' => 20, 'maxlength' => 32)); ?><br />
		<label for="newpassword">New password:</label>
		<?php echo form_password(array('name' => 'newpassword', 'size' => 20, 'maxlength' => 32)); ?><br />
		<label for="confirmpassword">Confirm new password:</label>
		<?php echo form_password(array('name' => 'confirmpassword', 'size' => 20, 'maxlength' => 32)); ?><br />
		<?php echo form_submit(array(
			'class' => 'button',
			'name'  => 'change_password',
			'value' => 'Change Password')); ?>
	</fieldset>
	<?php echo form_close(); ?>
</div>