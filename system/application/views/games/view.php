 
<div class='BlueBox'>
	<h2><?php echo($game['title']); ?></h2>
	<!-- assumes flash content, is this always true?? -->
	<object
		width="<?php echo($game['width']); ?>"
		height="<?php echo($game['height']); ?>"
			
			src="<?php echo($game['filename']); ?>"
			width="<?php echo($game['width']); ?>"
			height="<?php echo($game['height']); ?>"
	  		pluginspage="http://www.macromedia.com/go/getflashplayer" />

</div>
