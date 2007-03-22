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

<div class='RightToolbar'>
info about writing notices from db including link to wikitext help page
</div>

<div>
<pre>
from email
to {teams, [remove]}, add [dropdown teams]
subject: %%organisation%%: [_________]
(header)
[wikitext input]
(footer)
</pre>
</div>
