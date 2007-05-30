<div id="RightColumn">
	<h2 class="first">Operations</h2>
	<div class="Entry">
		<p>
			<a href="<?php echo vip_url('directory/cards/filter/user/'.$membership['user_id'].'');?>">Member's Business Card</a>
			<br />
			<a href='/viparea/members/unsubscribe/<?php echo $membership['user_id']; ?>'>Unsubscribe Member</a>
			<br />
		</p>
	</div>

	<h2>What's this?</h2>
	<div class="Entry">
		<p>
			<?php echo $main_text; ?>
		</p>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>Member Details</h2>
		<form class='form'>
			<fieldset>
				<label for='member_name'>Name:</label>
				<input style="border: 0px;" type='text' readonly='readonly' name='member_name' id="member_name" value='<?php echo $membership['firstname'] ?>'/>
				<label for='member_surname'>Surname:</label>
				<input style="border: 0px;" type='text' readonly='readonly' name='member_surname' value='<?php echo $membership['surname'] ?>'/>
				<label for='member_nick'>Nickname:</label>
				<input style="border: 0px;" type='text' readonly='readonly' name='member_nick' value='<?php echo $membership['nickname'] ?>'/>
				<?php if (NULL !== $membership['email']) { ?>
					<label for='member_email'>Email:</label>
					<input style="border: 0px;" type='text' readonly='readonly' name='member_email' value='<?php echo $membership['email'] ?>'/>
					<br />
				<?php } ?>
				<label for='member_gender'>Gender:</label>
				<input style="border: 0px;" type='text' readonly='readonly' name='member_gender' value='<?php echo $membership['gender'] ?>'/>
				<br />
				<label for='member_enrol_year'>Enrolled Year:</label>
				<input style="border: 0px;" type='text' readonly='readonly' name='member_enrol_year' value='<?php echo $membership['enrol_year'] ?>'/>
				<br />	<br />
			</fieldset>
		</form>
	</div>
	<div class='BlueBox'>
		<h2>Membership Status</h2>
		<form action="<?php echo vip_url('members/info/'.$membership['user_id']);?>" class='form' method='POST'>
			<fieldset>
				<label for='member_paid'>Status:</label>
				<input style="border: 0px;" type="text" name="member_status" value="<?php echo $membership['status']; ?>" disabled />
				<label for='member_paid'>Paid:</label>
				<input style="border: 0px;" type='checkbox' name='member_paid' value='1' <?php if($membership['paid']){echo 'checked';} ?> />
				<label for='member_vip'>VIP Member:</label>
				<input style="border: 0px;" type='checkbox' name='member_vip' value='1' <?php if($membership['vip']){echo 'checked';} ?> />
			</fieldset>
			<fieldset>
				<input name='member_update' type='submit' value='Update' class='button' />
			</fieldset>
		</form>
	</div>
	<div class='BlueBox'>
		<h2>Membership Control</h2>
		<form action="<?php echo vip_url('members/info/'.$membership['user_id']);?>" class='form' method='POST'>
			<fieldset>
				<p><?php echo $membership['cmd_string']; ?></p>
				<input name='member_cmd' type='submit' value='<?php echo $membership['cmd_action']; ?>' class='button' onclick="<?php echo $membership['cmd_js']; ?>"/>
				<div style="clear: both;"></div>
	<?php if (isset($membership['byline_reset']) && $membership['byline_reset']) {	?>
				<p>This user does not have a byline. This means that the user will be <b>unable to write articles</b> in the office. To give this user the default byline, click below:</p>
			</fieldset>
			<fieldset>
				<input name='member_byline_reset' type='submit' value='Set Default Byline' class='button' onclick="return confirm('This will set the byline of the member to their full name. Are you sure?');" />
			</fieldset>
	<?php } ?>
		</form>
	</div>
	<script type="text/javascript">
	<!--
	function submit_checker()
	{
		var editor_access = document.getElementById('editor_level_access');
		var password = document.getElementById('password');
		var confirm_password = document.getElementById('confirm_password');

		if (editor_access.checked) {
			if (password.value!=confirm_password.value) {
				alert('Passwords do not match, please confirm your password.');
				return false;
			}
			if (password.value.length == 0) {
				alert('You must assign editors a password');
				return false;
			}
			if (password.value.length < 4) {
				alert('Office password must be more than 3 characters in length');
				return false;
			}
		}
		return true;
	}
	function show_password_form() {
		var editor_access = document.getElementById('editor_level_access');
		var password_form = document.getElementById('password_form');
		password_form.style.display = (editor_access.checked ? 'block' : 'none');
	}
	-->
	</script>
	<?php
	if ('manage' === VipMode()) {
		?>
		<div class='blue_box'>
			<h2>Office Access</h2>
			<form action="<?php echo vip_url('members/info/'.$membership['user_id']);?>" class="form" method='POST' onSubmit="return submit_checker();">
				<fieldset>
					<label for='office_access_level'>Access level:</label>
					<input style="float:none;" type="radio" onChange="show_password_form()" id="none_level_access" name="office_access_level" value="none" <?php if (!($membership['office_writer_access'] || $membership['office_editor_access'])) echo 'checked'; ?>> No Access
					<input style="float:none;" type="radio" onChange="show_password_form()" id="writer_level_access" name="office_access_level" value="writer" <?php if ($membership['office_writer_access']) echo 'checked'; ?>> Writer
					<input style="float:none;" type="radio" onChange="show_password_form()" id="editor_level_access" name="office_access_level" value="editor" <?php if ($membership['office_editor_access']) echo 'checked'; ?>> Editor
					<div id="password_form" style="display: <?php echo ($membership['office_editor_access'] ? 'block' : 'none'); ?>;">
						<br />
						<p>Editors require an additional password to access the office. This should be different to their university password. When this password is reset, it will be e-mailed to the user.</p>
						<label for='password'>New password:</label>
						<input type="password" name="password" id="password" value="">
						<label for='confirm_password'>Confirm password:</label>
						<input type="password" name="confirm_password" id="confirm_password" value="">
						<br />
					</div>
				</fieldset>
				<fieldset>
					<input name='access_update' type="submit" value="Set Access Level" class="button" />
				</fieldset>
			</form>
		</div>
		<?php
	}
	?>
</div>
