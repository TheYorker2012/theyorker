<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
</div>
<div class='blue_box'>
<h2>account maintenance</h2>
</div>
<div class='grey_box'>
<h2>account details</h2>
	<form action='/viparea/account/update/<?php echo $organisation['shortname']; ?>/updatedetails' class='form' method='POST'>
	<fieldset>
		<label for='details_name'>Organistaion name :</label>
		<input type='text' name='details_name' style='width: 150px;'value='<?php echo $organisation['name']; ?>'/>
		<br />
		<label for='details_shortname'>Short name :</label>
		<input type='text' name='details_shortname' style='width: 150px;'value='<?php echo $organisation['shortname']; ?>'/>
		<br />
		<label for='details_org_type'>Category :</label>
		<select name='details_org_type' size='1'>
			<option value='' selected='selected'></option>
			<option value='1'>Societies</option>
			<option value='2'>Organisations</option>
		</select>
		<label for='details_button'></label>
		<input type='submit' name='details_button' value='Update' class='button' />
	</fieldset>
	</form>
</div>
<div class='grey_box'>
<h2>account username</h2>
</div>
<div class='grey_box'>
<h2>account password</h2>
</div>