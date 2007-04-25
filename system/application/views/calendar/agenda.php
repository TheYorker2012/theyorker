<?php

/**
 * @param $MiniMode bool
 * @param $Events
 * @param $Occurrences
 */

if (!empty($Occurrences)) {
	?>
	<table border="0" cellpadding="1" cellspacing="0">
	<?
	foreach ($Occurrences as $occurrence) {
		echo('<tr><td valign="top">');
		echo($occurrence->StartTime->Format('H:i'));
		echo('</td><td><img src="/images/prototype/homepage/arrow.png" /></td><td>');
		echo($occurrence->Event->Name);
		echo('</td></tr><tr><td colspan="2" /><td>');
		echo($occurrence->LocationDescription);
		echo('</td></tr>');
	}
	?>
	</table>
	<?php
} else {
	?>
	You have no events today.
	<?php
}

?>