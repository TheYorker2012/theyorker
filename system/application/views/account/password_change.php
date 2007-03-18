<?php echo $main_text; ?>

<?php echo form_open($change_password_target, array('class' => 'form')); ?>
<fieldset>
	<label for="oldpassword">Enter your current password:</label>
	<?php echo form_password(array('name' => 'oldpassword', 'size' => 20, 'maxlength' => 32)); ?>
	<label for="newpassword">Enter your new password:</label>
	<?php echo form_password(array('name' => 'newpassword', 'size' => 20, 'maxlength' => 32)); ?>
	<label for="confirmpassword">Confirm your new password:</label>
	<?php echo form_password(array('name' => 'confirmpassword', 'size' => 20, 'maxlength' => 32)); ?>
	<?php echo form_submit(array(
		'class' => 'button',
		'name'  => 'change_password',
		'value' => 'Change Password')); ?>
</fieldset>
<?php echo form_close(); ?>