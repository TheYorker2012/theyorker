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
	<p>
		<strong>Account Maintainer:</strong> <?php echo $maintainer['name']; ?><br />
		<strong>Maintainer's Email:</strong> <?php echo $maintainer['email']; ?><br />
		<strong>Maintainer is Student:</strong> <?php echo $maintainer['student']; ?><br />
	</p>
</div>
<div class='grey_box'>
<h2>account details</h2>
	<form action='/viparea/account/update/<?php echo $organisation['shortname']; ?>/updatedetails' class='form' method='POST'>
	<fieldset>
		<label for='details_name'>Organistaion name :</label>
		<input type='text' name='details_name' style='width: 150px;'value='<?php echo $organisation['name']; ?>'/>
		<br />
		<label for='details_fileas'>Alphabetically List As:</label>
		<input type='text' name='orgdetails_alphlisting' style='width: 220px;' />
		<br />
		<label for='details_shortname'>Short name :</label>
		<input type='text' name='details_shortname' style='width: 150px;'value='<?php echo $organisation['shortname']; ?>'/>
		<br />
		<label for='details_yorkipedia'>Yorkipedia Entry:</label>
		<input type='text' name='orgdetails_yorkipedia' style='width: 220px;' value='<?php echo $organisation['yorkipedia']['title']; ?>'/>
		<br />
		<label for='details_org_type'>Category :</label>
		<SELECT name="details_org_type">
				<?php foreach ($categories as $category) { ?>
					<OPTION VALUE="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></OPTION>
				<?php } ?>
		</SELECT>
		<label for='details_button'></label>
		<input type='submit' name='details_button' value='Update' class='button' />
	</fieldset>
	</form>
</div>
<div class='grey_box'>
<h2>account username</h2>
	<p>
		<?php echo $account_username; ?>
	</p>
	<form action='/viparea/account/update/<?php echo $organisation['shortname']; ?>/username' class='form' method='POST'>
	<fieldset>
		<label for='account_username'>Username :</label>
		<input type='text' name='account_username' style='width: 150px;' value=''/>
		<br />
		<label for='account_button'></label>
		<input type='submit' name='account_button' value='Update' class='button' />
	</fieldset>
	</form>
</div>
<div class='grey_box'>
<h2>account password</h2>
	<p>
		<?php echo $account_password; ?>
	</p>
	<form action='/viparea/account/update/<?php echo $organisation['shortname']; ?>/password' class='form' method='POST'>
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
<a href='/viparea/'>Back to the vip area.</a>