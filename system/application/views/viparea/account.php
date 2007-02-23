<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
</div>
<div class='blue_box'>
<h2>account maintenance</h2>
	<p>
		<?php echo $account_maintenance_text; ?>
	</p>
	<?php
	if($maintainer['maintained']){
		if($maintainer['student']){ ?>
		<p>
			The following student is responisble for this account.
		</p>
		<p>
			<strong>Account Maintainer:</strong> <?php echo $maintainer['maintainer_firstname']." ".$maintainer['maintainer_surname']; ?><br />
			<strong>Maintainer's Email:</strong> <?php echo $maintainer['maintainer_student_email']; ?><br />
		</p>
		<?php }else{ ?>
		<p>
			The following non-student is responisble for this account.
		</p>
		<p>
			<strong>Account Maintainer:</strong> <?php echo $maintainer['maintainer_name']; ?><br />
			<strong>Maintainer's Email:</strong> <?php echo $maintainer['maintainer_email']; ?><br />
		</p>
		<?php
		}
	}else{ ?>
	<p>
		This account is being maintained by the yorker staff.
	</p>
	<?php } ?>
	<form action="<?php echo vip_url('account/maintainer/'); ?>" method="link" class="form">
	<fieldset>
		<input type="submit" class="button" value="Change Maintainer" />
	</fieldset>
	</form>
</div>
<?php if(!$is_student){ ?>
<div class='grey_box'>
<h2>account password</h2>
	<p>
		<?php echo $account_password_text; ?>
	</p>
	<form action='<?php echo vip_url('account/update'); ?>' class='form' method='POST'>
	<fieldset>
		<label for='password_old'>Old Password :</label>
		<input type='password' name='password_old' style='width: 150px;' />
		<br />
		<label for='password_new1'>New Password :</label>
		<input type='password' name='password_new1' style='width: 150px;' />
		<br />
		<label for='password_new2'>Repeat New Password :</label>
		<input type='password' name='password_new2' style='width: 150px;' />
		<br />
		<label for='password_button'></label>
		<input type='submit' name='password_button' value='Change' class='button' />
	</fieldset>
	</form>
</div>
<?php } ?>
<a href='/viparea/'>Back to the vip area.</a>