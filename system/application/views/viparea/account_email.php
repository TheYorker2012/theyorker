<?php

/**
 * @file views/viparea/account_email.php
 * @brief Email settings.
 *
 * @see
 *	http://real.theyorker.co.uk/wiki/Functional:Notices
 *		Functional Specification section "VIP E-mail Settings"
 *
 * @version 21/03/2007 James Hogan (jh559)
 *	- Created.
 * @version 27/03/2007 James Hogan (jh559)
 *	- Took out identities stuff into separate view.
 *
 * @param $MainText string Main help text.
 * @todo rest of this.
 */

?>

<div class='RightToolbar'>
info about the settings and link to wikitext help (From database)
</div>

<div class="<?php echo alternator('blue','grey');?>_box">
	<H2>Email signiture</H2>
	
	<em>Signiture (wikitext):</em>
	<textarea name="invite_list" class="full" rows="3"></textarea>
	<br />
	
	<input type="button" class="button" value="Save" />
	<input type="button" class="button" value="Preview" />
	<div id="headfootPreview" class="grey_box">
		<p>Generating preview...</p>
		<p>THIS IS A PREVIEW, AJAX GENERATED</p>
		<p>{Your email goes here}</p>
		<p>AND THE FOOTER</p>
	</div>
</div>
