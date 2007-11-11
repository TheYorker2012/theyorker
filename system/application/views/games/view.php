<div class='BlueBox'>
	<h2><?php echo($game['title']); ?></h2>
	<!-- assumes flash content, is this always true?? -->
	<object
		width="<?php echo($game['width']); ?>" 
		height="<?php echo($game['height']); ?>"
		classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
		codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0"
	>
		<param name="movie" value="<?php echo($game['filename']); ?>" />
		<embed
			src="<?php echo($game['filename']); ?>"
			width="<?php echo($game['width']); ?>"
			height="<?php echo($game['height']); ?>"
			type="application/x-shockwave-flash"
			pluginspage="http://www.macromedia.com/go/getflashplayer"
		/>
	</object>
</div>
