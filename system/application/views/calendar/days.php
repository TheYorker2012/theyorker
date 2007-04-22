<?php

$squash = count($Days) > 3;

function DrawOccurrence(&$Occurrence, $Squash)
{
	?>
	<div id="ev_15" class="calviewIndEventBox2" style="width: 100%;">
		<div style="padding: 2px;font-size: small;">
			<span><?php echo($Occurrence->Event->Name); ?></span>
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
						$CI = & get_instance();
						if (FALSE === $Occurrence->UserAttending) {
							echo('not attending');
							echo(' (<a href="'.site_url('calendar/actions/attend/'.
								$Occurrence->Event->Source->GetSourceId().
								'/'.urlencode($Occurrence->SourceOccurrenceId).
								'/accept'.$CI->uri->uri_string()).'">attend</a>');
							echo(', <a href="'.site_url('calendar/actions/attend/'.
								$Occurrence->Event->Source->GetSourceId().
								'/'.urlencode($Occurrence->SourceOccurrenceId).
								'/maybe'.$CI->uri->uri_string()).'">maybe attend</a>)');
						} elseif (TRUE === $Occurrence->UserAttending) {
							echo('attending');
							echo(' (<a href="'.site_url('calendar/actions/attend/'.
								$Occurrence->Event->Source->GetSourceId().
								'/'.urlencode($Occurrence->SourceOccurrenceId).
								'/maybe'.$CI->uri->uri_string()).'">maybe attend</a>');
							echo(', <a href="'.site_url('calendar/actions/attend/'.
								$Occurrence->Event->Source->GetSourceId().
								'/'.urlencode($Occurrence->SourceOccurrenceId).
								'/decline'.$CI->uri->uri_string()).'">don\'t attend</a>)');
						} else {
							echo('maybe attending');
							echo(' (<a href="'.site_url('calendar/actions/attend/'.
								$Occurrence->Event->Source->GetSourceId().
								'/'.urlencode($Occurrence->SourceOccurrenceId).
								'/accept'.$CI->uri->uri_string()).'">attend</a>');
							echo(', <a href="'.site_url('calendar/actions/attend/'.
								$Occurrence->Event->Source->GetSourceId().
								'/'.urlencode($Occurrence->SourceOccurrenceId).
								'/decline'.$CI->uri->uri_string()).'">don\'t attend</a>)');
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