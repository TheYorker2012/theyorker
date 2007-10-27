<?php if (isset($login_error)) { ?>
		<div style="color:red">
			<?php echo($login_error); ?>
		</div>
<?php } ?>
		<div>
			To access this feature you must be a reporter for the Yorker.<br />
			If you are a reporter, please login with your Yorker account details below.
			<br />&nbsp;
		</div>
		<form action="http://apps.facebook.com/theyorker/myarticles/" method="post">
			<fieldset style="border:0">
				<label for="yorker_username" style="display:block;clear:both;float:left;width:30%;text-align:right;margin:0.4em;">Username:</label>
				<input type="text" name="yorker_username" id="yorker_username" value="" style="float:left;margin:0.2em;" />
				<br />
				<label for="yorker_password" style="display:block;clear:both;float:left;width:30%;text-align:right;margin:0.4em;">Password:</label>
				<input type="password" name="yorker_password" id="yorker_password" value="" style="float:left;margin:0.2em;" />
				<br />
				<input type="submit" name="yorker_login" id="yorker_login" value="Login" style="float:right;margin:0.5em;" />
			</fieldset>
		</form>