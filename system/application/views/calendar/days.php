<?php

function DrawOccurrence(&$Occurrence)
{
	echo('<div class="BlueBox">');
	echo($Occurrence->StartTime->Format('g:m a'));
	echo('<br />');
	echo($Occurrence->Event->Name);
	echo('<br />');
	if (FALSE === $Occurrence->UserAttending) {
		echo('not attending');
	} elseif (TRUE === $Occurrence->UserAttending) {
		echo('attending');
	} else {
		echo('maybe attending');
	}
	echo('</div>');
}

echo('<table width="100%">');
echo('<tr>');
foreach ($Days as $date => $times) {
	echo('<th align="center">');
	echo($times['date']->Format('l'));
	echo('<br />');
	echo($times['date']->Format('jS M'));
	echo('</th>');
}
echo('<tr>');
foreach ($Days as $date => $day) {
	$times = $day['events'];
	if (array_key_exists('000000',$times)) {
		echo('<td>');
		foreach ($times['000000'] as $occurrence) {
			if (!$occurrence->TimeAssociated) {
				DrawOccurrence($occurrence);
			}
		}
		echo('</td>');
	}
}
echo('</tr><tr>');
foreach ($Days as $date => $day) {
	$times = $day['events'];
	echo('<td>');
	foreach ($times as $time => $ocs) {
		foreach ($ocs as $occurrence) {
			if ($occurrence->TimeAssociated) {
				DrawOccurrence($occurrence);
			}
		}
	}
	echo('</td>');
}
echo('</tr>');
echo('</table>');

?>