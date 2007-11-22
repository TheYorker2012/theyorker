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

	<applet code="EIRC" archive="/javascript/EIRC.jar,/javascript/EIRC-gfx.jar" width="620" height="400">
		<param name="server" value="<?php echo($Server); ?>" />
		<param name="port" value="6667" />
		<param name="mainbg" value="#C0C0C0" />
		<param name="mainfg" value="#000000" />
		<param name="textbg" value="#FFFFFF" />
		<param name="textfg" value="#000000" />
		<param name="selbg" value="#00007F" />
		<param name="selfg" value="#FFFFFF" />
		<param name="channel" value="<?php echo($Channel); ?>" />
		<param name="titleExtra" value=" - EIRC" />
		<param name="username" value="yorker_<?php echo($Username); ?>" />
		<param name="realname" value="<?php echo($Fullname); ?>" />
		<param name="nickname" value="<?php echo(str_replace(' ', '', $Fullname)); ?>" />
		<param name="login" value="1" />
		<param name="language" value="en" />
		<param name="country" value="UK" />
		
		<p>	Sorry, but you need a Java 1.1.x enabled browser to use this IRC client.</p>
	</applet>
	
	<?php echo($IrcHelp); ?>
</div>