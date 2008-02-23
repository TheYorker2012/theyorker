<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo($main_text); ?>
		<?php if ($new_mode) { ?>
		Technical Notes: The organisation short name is generated dynamically from the organisation name when the organisation is first created. A warning is presented if the name already exists (as the organisation may already exist).
		<?php } else { ?>
		<?php } ?>
	</p>
</div>

<div class='grey_box'>
		<?php if ($new_mode) { ?>
		<h2>create new organisation</h2>
		<?php } else { ?>
		<h2>organisation details</h2>
		<?php } ?>
		These details are not editable by the organisation themselves, or any associated VIP.
	<form action='' class='form' method='POST'>
	<fieldset>
		<label for='details_name'>Organistaion name :</label>
		<input type='text' name='details_name' style='width: 150px;' value=''/>
		<br />
		<label for='details_fileas'>Alphabetically List As:</label>
		<input type='text' name='details_fileas' style='width: 220px;' />
		<br />
		<label for='details_yorkipedia'>Yorkipedia Entry:</label>
		<input type='text' name='details_yorkipedia' style='width: 220px;' value=''/>
		<br />
		<label for='details_show_in_directory'>Show in Directory:</label>
		<input type='checkbox' name='details_show_in_directory' style='width: 220px;' value=''/>
		<br />
		<label for='details_reviewed'>Managed by Review Team:</label>
		<input type='checkbox' name='details_reviewed' style='width: 220px;' value=''/>
		<br />
		<label for='details_has_events'>Has Events:</label>
		<input type='checkbox' name='details_has_events' style='width: 220px;' value=''/>
		<br />
		<label for='details_org_type'>Organisation Category:</label>
		<select name="details_org_type">
				<?php foreach ($categories as $category) { ?>
					<option value="<?php echo($category['id']); ?>"><?php echo(xml_escape($category['name'])); ?></option>
				<?php } ?>
		</select>
		<label for='details_button'></label>
		<input type='submit' name='details_button' value='<?php echo ($new_mode ? 'Create' : 'Update') ?>' class='button' />
	</fieldset>
	</form>
</div>
<?php if (!$new_mode) { ?>
<div class='blue_box'>
<h2>account maintenance</h2>
	<p>
		<strong>Account Maintainer:</strong> John Smith<br />
		<strong>Maintainer's Email:</strong> john@smith.net<br />
		<strong>Maintainer is Student:</strong> Yes<br />
	</p>
	<input type='button' name='details_button' value='Reclaim Ownership' class='button' />
</div>
<div class='grey_box'>
<h2>account username</h2>

	<form action='' class='form' method='POST'>
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
	<form action='' class='form' method='POST'>
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
