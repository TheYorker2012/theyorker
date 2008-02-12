<?php

/**
 * @file views/calendar/subscriptions_index.php
 * @brief Calendar subscriptions index page
 * @author James Hogan (jh559)
 *
 * @param $Wikitexts array Texts from page properties:
 *	- introduction - Intro to go at top of page.
 *	' help_main - Main help text.
 */

?>

<div id='RightColumn'>
	<h2 class="first">What&#039;s this?</h2>
	<?php if (isset($Wikitexts['help_main'])) { echo($Wikitexts['help_main']); } ?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<?php if (isset($Wikitexts['intro'])) { echo($Wikitexts['intro']); } ?>
	</div>
</div>
