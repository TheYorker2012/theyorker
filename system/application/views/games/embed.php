	<?php 
		if(substr($filename,-3)=='swf'){
	?>
		<object
			width="<?php echo($width); ?>" 
			height="<?php echo($height); ?>"
			classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
			codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0"
		>
			<param name="movie" value="<?php echo(xml_escape($pathname)); ?>" />
			<embed
				src="<?php echo(xml_escape($pathname)); ?>"
				width="<?php echo($width); ?>"
				height="<?php echo($height); ?>"
				type="application/x-shockwave-flash"
				pluginspage="http://www.macromedia.com/go/getflashplayer"
			/>
		</object>
	<?php }else{ ?>
		<object
			classid="clsid:166B1BCA-3F9C-11CF-8075-444553540000" 
			codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/director/sw.cab" 
			width="<?php echo($width); ?>" 
			height="<?php echo($height); ?>"
		>	
			<param src="SRC" value="<?php echo(xml_escape($pathname)); ?>">
			<embed src="<?php echo(xml_escape($pathname)); ?>" 
				width="<?php echo($width); ?>"
				height="<?php echo($height); ?>"
				type="application/x-director"
				pluginspage="http://www.macromedia.com/shockwave/download/">
			</embed>
		</object>
	<?php } ?>
