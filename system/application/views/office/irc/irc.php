<?php
/**
 * @author James Hogan (jh559)
 * @param $Help
 * @param $IrcHelp
 * @param $Embed
 */

$username = $this->user_auth->username;
$fullname = $this->user_auth->firstname.' '.$this->user_auth->surname;
$nick = str_replace(' ', '', $fullname);
$url = 'http://trunk.dev.theyorker.co.uk/office/irc/free'.
			'?username='	. urlencode($username).
			'&fullname='	. urlencode($fullname).
// 			'&nick='		. urlencode($nick).
		'';
?>

<div class="BlueBox">
	<h2>interactive chat</h2>
	<?php echo($Help); ?>
	
	<?php if ($Embed) { ?>
	<iframe src="<?php echo($url); ?>"
			height="420" width="620"
			frameborder="0" scrolling="no">
		Your browser needs to support iFrames to use this webchat.
	</iframe>
	<?php } else { ?>
	<fieldset class="inline">
		<input class="button" type="button" onclick="irc_disconnect()" value="Disconnect" />
		<input class="button" type="button" onclick="irc_connect()" value="Connect" />
	</fieldset>
	<?php } ?>
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