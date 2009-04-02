<?php
/**
 * @author James Hogan (jh559)
 * @param $Help
 * @param $IrcHelp
 * @param $Embed
 * @param $Personal
 */

?>

<div class="BlueBox">
	<h2>interactive chat</h2>
	<?php echo($Help); ?>
	
	<?php if (null !== $Embed) {
		$url = $Embed.'/office/irc/free'.
					'?username='	. urlencode($EmbedData['Username']).
					'&fullname='	. urlencode($EmbedData['Fullname']).
		// 			'&nick='		. urlencode($EmbedData['Nick']).
				'';
	?>
	<iframe src="<?php echo(xml_escape($url)); ?>"
			height="420" width="620"
			frameborder="0" scrolling="no">
		Your browser needs to support iFrames to use this webchat.
	</iframe>
	<?php } else {
		$this->load->view('office/irc/embedded', $EmbedData);
	} ?>
</div>
<div class="BlueBox">
	<?php echo($IrcHelp); ?>
</div>
