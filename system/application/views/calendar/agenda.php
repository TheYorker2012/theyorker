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
	$last_date = '';
	foreach ($Occurrences as $occurrence) {
		$new_date = $occurrence->StartTime->Format('%D');
		if ($new_date != $last_date) {
			echo('<tr><td colspan="3"><div class="Date">'.$new_date.'</div></td></tr>');
			$last_date = $new_date;
		}
		echo('<tr><td valign="top">');
		echo($occurrence->StartTime->Format('%T'));
		echo('</td><td valign="top"><img src="/images/prototype/homepage/arrow.png" /></td><td>');
		echo(htmlentities($occurrence->Event->Name, ENT_QUOTES, 'utf-8'));
		if (!empty($occurrence->LocationDescription)) {
			echo(' ('.htmlentities($occurrence->LocationDescription, ENT_QUOTES, 'utf-8').')');
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