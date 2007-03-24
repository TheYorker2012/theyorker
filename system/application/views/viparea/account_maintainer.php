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
	<form action='<?php echo vip_url('account/maintainer'); ?>' class='form' method='POST'>
	<fieldset>
		<label for='maintainer_type'>Maintenance by :</label><br />
		<input type='radio' name='maintainer_type' value='yorker' <?php if($maintainer['maintained'] == false){ echo "checked";}?> 
		onclick="document.getElementById('nonstudent_details').style.display = 'none';" /> The Yorker<br />
		
		<?php if($is_student){ ?>
		<input type='radio' name='maintainer_type' value='student' <?php if($maintainer['is_user']){ echo "checked";}?>
		onclick="document.getElementById('nonstudent_details').style.display = 'none';" /> Me (<?php echo $user_fullname; ?>)<br />
		<?php }else{ ?>
		<input type='radio' name='maintainer_type' value='nonstudent' <?php if($maintainer['maintained'] and $maintainer['student'] == false){ echo "checked";}?>
		onclick="document.getElementById('nonstudent_details').style.display = 'block';" /> Non student member<br />
		<?php } ?>
		
		<div id='nonstudent_details' <?php if($maintainer['maintained'] and $maintainer['student'] == false){}else{ echo 'style="display: none;"';}?>>
			<label for='maintainer_name'>Maintainer's Name:</label>
			<input type='text' name='maintainer_name' style='width: 150px;' value='<?php echo $maintainer['maintainer_name']; ?>'/>
			<br />
			<label for='maintainer_email'>Maintainer's Email:</label>
			<input type='text' name='maintainer_email' style='width: 220px;' value='<?php echo $maintainer['maintainer_email']; ?>'/>
			<br />
		</div>
		
		<label for='maintainer_button'></label>
		<input type='submit' name='maintainer_button' value='Update' class='button' />
	</fieldset>
	</form>
</div>
<a href='<?php echo vip_url('account/update'); ?>'>Back to my account settings.</a>