<?php

/**
 * @file views/viparea/account_email.php
 * @brief Email settings.
 *
 * @see
 *	http://real.theyorker.co.uk/wiki/Functional:VIP_Account
*/
?>

<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<?php echo($main_text); ?>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>email signature</h2>
		<form class="form" action="" method="post">
			<fieldset>
				<textarea name="email_signature" rows="10" cols="42"><?php echo($signature); ?></textarea>
			</fieldset>
			<fieldset>
				<input type="submit" name="save_email_sig" class="button" value="Save" />
<?php				//<input type="button" id="preview_email_sig" class="button" value="Preview" /> ?>
			</fieldset>
		</form>
	</div>
<?php
 /*
	<div class="BlueBox">
		<h2>preview</h2>
		<div id="GeneratingPreview">
			<p>Generating preview...</p>
		</div>
		<div id="EmailPreview">
			<p>preview</p>
		</div>
	</div>
	*/
?>
</div>
