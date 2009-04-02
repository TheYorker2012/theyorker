<?php
/**
 * @author James Hogan (jh559)
 * @param $Framed bool
 * @param $Username string
 * @param $Fullname string
 */

if (!$Framed) {
// Must echo through PHP in case short tags is turned on
echo('<?xml version="1.0" encoding="UTF-8"?'.'>');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<script src="/javascript/irc.js" type="text/javascript"></script>
		<script src="/javascript/simple_ajax.js" type="text/javascript"></script>
		<link href="/stylesheets/new.css" rel="stylesheet" type="text/css" />
		<link href="/stylesheets/irc.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript">
		// <![CDATA[
			function onLoadHandler()
			{
				irc_ajax_url = "/office/irc/ajax/embeddedlive";
				defaultdata['username'] = <?php echo(js_literalise($Username)); ?>;
				defaultdata['fullname'] = <?php echo(js_literalise($Fullname)); ?>;
			}
		// ]]>
		</script>
	</head>
	<body onload="onLoadHandler()">
<?php } else {?>
		<script type="text/javascript">
		// <![CDATA[
			onLoadFunctions.push(function() { irc_ajax_url = "/office/irc/ajax"; });
		// ]]>
		</script>
<?php } ?>
		<fieldset class="inline">
			<input class="button" type="button" onclick="irc_disconnect()" value="Disconnect" />
			<input class="button" type="button" onclick="irc_connect()" value="Connect" />
		</fieldset>
		<div id="irc_error_msg" style="display:none;" class="irc_error"></div>
		<div style="display:block;">
			<ul id="irc_channel_tabs" class="irc_channels">
			</ul>
		</div>
		<div id="irc_channels">
		</div>
<?php if (!$Framed) { ?>
	</body>
</html>
<?php } ?>
