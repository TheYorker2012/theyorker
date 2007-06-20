<ul>
	<li><a href="/test/travis/calendar">Calendar</a></li>
	<li><a href="/test/travis/imap">IMAP - Check for new email count</a></li>
	<li><a href="/test/travis/campaign">Campaign Info Selecty Thing</a></li>
	<li><a href="/test/travis/contenttypes">ADMIN: Content Types Adder</a></li>
	<li><a href="/test/travis/select">ADMIN: Assignee Selector</a></li>
	<li><a href="/test/travis/feedback">ADMIN: Add Feedback to Trac</a></li>
</ul>

<?php
		$val = '';
		for ($i = 0; $i < 32; $i++) {
			$val .= chr(rand(65,90));
		}
		echo $val;
?>