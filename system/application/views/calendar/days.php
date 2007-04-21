<?php

$squash = count($Days) > 3;

function DrawOccurrence(&$Occurrence, $Squash)
{
	?>
	<div id="ev_15" class="calviewIndEventBox2" style="width: 100%;">
		<div style="padding: 2px;font-size: small;">
			<strong><?php echo($Occurrence->Event->Name); ?></strong>
			<div class="calviewExpandedSmall" id="ev_es_%%refid%%" style="margin-top: 2px;">
				<div>
					<?php
					if ($Occurrence->TimeAssociated) {
						echo($Occurrence->StartTime->Format('g:ia'));
						echo('-');
						echo($Occurrence->EndTime->Format('g:ia'));
						echo('<br />');
					}
					if (!$Squash) {
						if (!empty($Occurrence->LocationDescription)) {
							echo($Occurrence->LocationDescription);
							echo('<br />');
						}
						echo('<i>');
						echo($Occurrence->Event->Description);
						echo('</i><br />');
						if (FALSE === $Occurrence->UserAttending) {
							echo('not attending');
						} elseif (TRUE === $Occurrence->UserAttending) {
							echo('attending');
						} else {
							echo('maybe attending');
						}
					}
					?>
				</div>
			</div>

		</div>
	</div>
	<?php
}

echo('<table id="calviewCalTable" border="0" cellpadding="0" cellspacing="0" width="100%">');
echo('<tr>');
foreach ($Days as $date => $times) {
	echo('<th class="calviewCalHeadingCell">');
	echo($times['date']->Format('l'));
	echo('<br />');
	echo($times['date']->Format('jS M'));
	echo('</th>');
}
echo('</tr><tr>');
foreach ($Days as $date => $day) {
	$times = $day['events'];
	echo('<td>');
	if (array_key_exists('000000',$times)) {
		foreach ($times['000000'] as $occurrence) {
			if (!$occurrence->TimeAssociated) {
				DrawOccurrence($occurrence, $squash);
			}
		}
	}
	echo('</td>');
}
echo('</tr><tr>');
foreach ($Days as $date => $day) {
	$times = $day['events'];
	echo('<td class="calviewCalEventsCell">');
	foreach ($times as $time => $ocs) {
		foreach ($ocs as $occurrence) {
			if ($occurrence->TimeAssociated) {
				DrawOccurrence($occurrence, $squash);
			}
		}
	}
	echo('</td>');
}
echo('</tr>');
echo('</table>');

?>