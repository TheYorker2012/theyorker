<?php
/**
 * @param $Server
 * @param $Channel
 * @param $Username
 * @param $Fullname
 * @param $Help
 * @param $IrcHelp
 */
?>
<div id='RightColumn'>
	<h2 class="first">What&apos;s this?</h2>
	<?php echo($Help); ?>
	<?php echo($IrcHelp); ?>
</div>

<div id="MainColumn">
	<applet code="EIRC" archive="/javascript/EIRC.jar,/javascript/EIRC-gfx.jar" width="410" height="400">
		<param name="server" value="<?php echo($Server); ?>" />
		<param name="port" value="6667" /><?php /*
		<param name="mainbg" value="<?php echo($Background); ?>" /> */ ?>
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
		<param name="nickname" value="<?php echo(str_replace(' ', '', $Fullname)); ?>" /><?php /*
		<!--param name="password" value="" /-->
		<!--param name="servPassword" value="" /-->
		<!--param name="servEmail" value="" /--> */ ?>
		<param name="login" value="1" /><?php /*
		<!--param name="spawn_frame" value="1" /-->
		<!--param name="frame_width" value="620" /-->
		<!--param name="frame_height" value="400" /--> */ ?>
		<param name="language" value="en" />
		<param name="country" value="UK" />
		
		<h1>The Yorker IRC Client</h1>
		<p>	Sorry, but you need a Java 1.1.x enabled browser to use this IRC client.</p>
	</applet>
</div>