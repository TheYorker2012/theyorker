<?php
/**
 * @author James Hogan (jh559)
 * @param $Username string
 * @param $Fullname string
 */

?>
<html>
	<head>
		<script src="/javascript/irc.js" type="text/javascript"></script>
		<script src="/javascript/simple_ajax.js" type="text/javascript"></script>
		<link href="/stylesheets/new.css" rel="stylesheet" type="text/css" />
		<link href="/stylesheets/irc.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript">
			function onLoadHandler()
			{
				irc_ajax_url = "/office/irc/ajax/embeddedlive";
				defaultdata['username'] = "<?php echo(str_replace('"','\"',$Username)); ?>";
				defaultdata['fullname'] = "<?php echo(str_replace('"','\"',$Fullname)); ?>";
			}
		</script>
	</head>
	<body onload="onLoadHandler()">
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
	</body>
</html>
