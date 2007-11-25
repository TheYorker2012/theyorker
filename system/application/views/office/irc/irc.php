<?php
/**
 * @author James Hogan (jh559)
 * @param $Server
 * @param $Channel
 * @param $Username
 * @param $Fullname
 * @param $Help
 * @param $IrcHelp
 */
?>

<div class="BlueBox">
	<h2>interactive chat</h2>
	<?php echo($Help); ?>
	
	<fieldset class="inline">
		<input class="button" type="button" onclick="irc_disconnect()" value="Disconnect" />
		<input class="button" type="button" onclick="irc_connect()" value="Connect" />
	</fieldset>
	<div style="display:block;">
		<ul id="irc_channel_tabs" class="irc_channels">
		</ul>
	</div>
	<div id="irc_channels">
	</div>
</div>
<div class="BlueBox">
	<?php echo($IrcHelp); ?>
</div>