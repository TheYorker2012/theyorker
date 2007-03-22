<?php

/**
 * @file views/notices/settings.php
 * @brief Notices settings.
 *
 * Allows notices settings to be seen and altered.
 *
 * @see
 *	http://real.theyorker.co.uk/wiki/Functional:Notices
 *		Functional Specification section "VIP E-mail Settings"
 *
 * @version 21/03/2007 James Hogan (jh559)
 *	- Created.
 *
 * @param $MainText string Main help text.
 * @todo rest of this.
 */

?>

<div class='RightToolbar'>
info about the settings and link to wikitext help (From database)
</div>

<div>
<pre>
This page will allow the VIP to configure the header and footer used to e-mail from the VIP area (both for notices and for e-mailing from the members area). 
The page also allows the user to choose a default from name & address. 
The page allows the user to register the society's socs account (to be used for retrieving unread e-mail, to display on vips' "Vip Area" homepage and student homepage) via a wizard that an involves a confirmation e-mail. 
The page also gives a list of send-from e-mail addresses in three categories: 
 Current logged in user 
 Society e-mail addresses 
  This includes the socs account (if registered) 
  These are shared between all VIPs current and future 
  New names & addresses may be added provided that they are verified (if a user attempt to add a socs account, ask them if they would like an unread mail count on their homepage). 
 Personal e-mail addresses 
  These are only visible to the current user. 
  New names & addresses may be added provided that they are verified
</pre>
</div>
