<div class='RightToolbar'>
	<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
</div>
<div style="width: 420px; margin: 0px; padding-right: 3px; ">
<?php echo $what_to_do; ?>
<P><A HREF="<?php echo vip_url('members/list/not/confirmed'); ?>">List invited users</A></P>
<form name='members_invite_form' action='<?php echo $target; ?>' method='POST' class='form'>
	<fieldset>
		<label for='invite_list'>Invite List:</label>
		<textarea name="invite_list" class="full" rows="10"><?php echo $default_list; ?></textarea>
		<input type='submit' class='button' name='members_invite_button' value='Invite Members'>
	</fieldset>
</form>
<a href='<?php echo vip_url('members/list'); ?>'>Back to Member Management.</a>
</div>