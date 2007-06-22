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
	$sorted_occurrences = array();
	foreach ($Occurrences as $key => $occurrence) {
		$sorted_occurrences[$occurrence->StartTime->Timestamp()][] = & $Occurrences[$key];
	}
	foreach ($Occurrences as $occurrence) {
		echo('<tr><td valign="top">');
		echo($occurrence->StartTime->Format('H:i'));
		echo('</td><td valign="top"><img src="/images/prototype/homepage/arrow.png" /></td><td>');
		echo($occurrence->Event->Name);
		if (!empty($occurrence->LocationDescription)) {
			echo(' ('.$occurrence->LocationDescription.')');
		}
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