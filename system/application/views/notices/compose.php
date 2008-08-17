<?php

/**
 * @file views/notices/compose.php
 * @brief Compose notice view.
 *
 * Allows notices to be written and sent.
 *
 * @see
 *	http://real.theyorker.co.uk/wiki/Functional:Notices
 *		Functional Specification section "VIP Send Notice"
 *
 * @version 21/03/2007 James Hogan (jh559)
 *	- Created.
 *
 * @param $MainText string Main help text.
 * @todo Default values.
 */

?>

<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<p>Info about writing notices from db including link to wikitext help page.</p>
		<p><a href="<?php echo vip_url('account/email'); ?>">Email settings</a></p>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		from email
		to {teams, [remove]}, add [dropdown teams]
		subject: %%organisation%%: [_________]
		(header)
		[wikitext input]
		(footer)
	</div>
</div>
