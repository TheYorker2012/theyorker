<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<?php echo $main_text; ?>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>account administration</h2>
		<?php echo $account_maintenance_text; ?>
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
		<form action="<?php echo vip_url('account/maintainer/'); ?>" method="post" class="form">
			<fieldset>
				<input type="submit" class="button" value="Change Admin" />
			</fieldset>
		</form>
	</div>
</div>
