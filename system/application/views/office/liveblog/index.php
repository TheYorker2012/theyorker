<div class="BlueBox">
	<h2><?php echo($heading); ?></h2>
	<?php if (empty($twitter_id)) { ?>
		<?php echo($install_info); ?>
		<form action="/office/liveblog/install" method="post">
			<fieldset>
				<label for="twitter_user">Twitter Username:</label>
				<input type="text" name="twitter_user" id="twitter_user" value="" />
				<label for="twitter_pass">Twitter Password:</label>
				<input type="password" name="twitter_pass" id="twitter_pass" value="" />
			</fieldset>
			<fieldset>
				<input type="submit" name="twitter_login" id="twitter_login" value="Setup LiveBlogging" class="button" />
			</fieldset>
		</form>
	<?php } else { ?>
		<?php echo($uninstall_info); ?>
		<p>
			<b>Your Twitter Account ID:</b> <?php echo($twitter_id); ?><br />
			To unlink these Twitter details with your account please click <a href="/office/liveblog/uninstall">here</a>.
		</p>
	<?php } ?>
</div>