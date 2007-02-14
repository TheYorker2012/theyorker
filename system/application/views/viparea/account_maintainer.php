<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
</div>
<div class='blue_box'>
<h2>account maintenance</h2>
	<p>
		<?php echo $account_maintenance; ?>
	</p>
	<form action='/viparea/account/maintainer/<?php echo $organisation['shortname']; ?>/updatedetails' class='form' method='POST'>
	<fieldset>
		<label for='maintainer_type'>Maintence by</label>
		<input type='radio' name='maintainer_type' value='yorker' <?php if($maintainer['type'] == "yorker"){ echo "checked";}?> /> The Yorker
		<input type='radio' name='maintainer_type' value='account' <?php if($maintainer['type'] == "account"){ echo "checked";}?> /> Organisation member.
		<label for='maintainer_name'>Account Maintainer:</label>
		<input type='text' name='maintainer_name' style='width: 150px;' value='<?php echo $maintainer['name']; ?>'/>
		<br />
		<label for='maintainer_email'>Maintainer's Email:</label>
		<input type='text' name='maintainer_email' style='width: 220px;' value='<?php echo $maintainer['email']; ?>'/>
		<br />
		<label for='maintainer_student'>Student?</label>
		<input type='checkbox' name='maintainer_student' value='yes'<?php if($maintainer['student'] == "yes"){ echo "checked";}?>/>
		<br />
		<label for='maintainer_button'></label>
		<input type='submit' name='maintainer_button' value='Update' class='button' />
	</fieldset>
	</form>
</div>
<a href='/viparea/account/update/<?php echo $organisation['shortname']; ?>'>Back to my account.</a>