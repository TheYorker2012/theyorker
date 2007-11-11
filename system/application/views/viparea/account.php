<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
</div>
<div class='blue_box'>
<h2>account administration</h2>
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
		<input type="submit" class="button" value="Change Admin" />
	</fieldset>
	</form>
</div>
<a href='/viparea/'>Back to the vip area.</a>