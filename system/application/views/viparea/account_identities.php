<?php

/**
 * @file views/viparea/account_identities.php
 * @brief Identity settings for sending emails.
 *
 * Allows identity settings to be seen and altered.
 *
 * @see
 *	http://real.theyorker.co.uk/wiki/Functional:Notices
 *		Functional Specification section "VIP E-mail Settings"
 *
 * @version 27/03/2007 James Hogan (jh559)
 *	- Created.
 *
 * @param $MainText string Main help text.
 * @todo rest of this.
 */

?>

<div class='RightToolbar'>
<H4>something here</H4>
</div>

<div class="<?php echo alternator('blue','grey');?>_box">
	<H2>Email addresses</H2>
	<form>
		<table>
			<tr>
				<th>Name</th>
				<th>Email</th>
				<th>Type</th>
				<th>Verified</th>
				<th>Default</th>
				<th></th>
			</tr>
			<tr>
				<td>James Hogan</td>
				<td>jh559@york.ac.uk</td>
				<td>personal</td>
				<td>YES</td>
				<th><input type="radio" name="emailDefault" checked /></th>
				<td></td>
			</tr>
			<tr>
				<td>James Hogan</td>
				<td>jh559@cs.york.ac.uk</td>
				<td>personal</td>
				<td>YES</td>
				<th><input type="radio" name="emailDefault" /></th>
				<td>
					<a href="#">X</a>
				</td>
			</tr>
			<tr>
				<td>The Yorker</td>
				<td>updates@theyorker.co.uk</td>
				<td>org</td>
				<td colspan="2">NO <a href="#">retry (icon)</a></td>
				<td>
					<a href="#">X</a>
				</td>
			</tr>
		</table><br />
		<input type="submit" class="button" value="Save default" />
		
		<H3>Add email address</H3>
		Type: <select>
			<option selected>Personal</option>
			<option>Organisation</option>
		</select><br />
		Name: <input value="Jimmy" /><br />
		Email address: <input value="j@mes.hog.an" /><br />
		<P>The Name will appear to recipients in the From field</P>
		<P>The brief description is so that VIPs can see what addresses are meant for</P>
		<P>
			New email addresses will be verified before they can be used.
			An email will be sent to it with a link which must be followed to verify the address.
		</P>
		<input type="submit" class="button" value="Submit" />
	</form>
	
</div>

<div class="<?php echo alternator('blue','grey');?>_box">
	<H2>Society email address</H2>
	<P><strong>Society email is not set up</strong></P>
	<P>
		If you have a society account with the university you can register it with The Yorker.
		This makes it easy to send emails from the soc email address and optionally allows you to
			see an unread email count on the [vip] homepage.
	</P>
	<P>Click register to set up your soc account.</P>
	<input type="button" class="button" value="Register" />
</div>

<div class="<?php echo alternator('blue','grey');?>_box">
	<form>
		<H2>Society email address</H2>
		<P><strong>Society email set up as &quot;The Yorker &lt;soc25@york.ac.uk&gt;&quot;</strong></P>
		<input type="button" class="button" value="Change" />
		<input type="button" class="button" value="Remove" />
		
		<P>
			Unread mail counter:
			<input type="radio" name="socUnreadCounter" value="enabled" checked /> Enabled
			<input type="radio" name="socUnreadCounter" value="disabled" /> Disabled
		</P>
		<P>The unread mail counter allows you to see how many unread emails are in your soc email inbox from the [vip] homepage</P>
		<input type="button" class="button" value="Save" />
	</form>
</div>
