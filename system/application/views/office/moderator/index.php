<?php

/**
 * @file views/office/moderator/index.php
 * @brief Main moderator control panel.
 */

?>

<div class="BlueBox">
	<h2>Welcome to the moderator control panel</h2>
	<UL>
		<li><a href="<?php echo site_url('office/moderator/comment/reported'); ?>">Reported comments</a></li>
		<li><a href="<?php echo site_url('office/moderator/comment/deleted'); ?>">Deleted comments</a></li>
		<li><a href="<?php echo site_url('office/moderator/comment/good'); ?>">Good comments</a></li>
	</UL>
</div>