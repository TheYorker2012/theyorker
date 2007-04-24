<?php

echo('<div align="center">');
if (isset($BackwardUrl)) {
	echo('<a href="'.$BackwardUrl.'">Backward</a> ');
}
if (isset($ForwardUrl)) {
	echo('<a href="'.$ForwardUrl.'">Forward</a>');
}
echo('</div>');

$squash = (count($Days) > 3);

function DrawOccurrence(&$Occurrence, $Squash, $ReadOnly)
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
					if ('published' !== $Occurrence->State) {
						echo('<strong>'.$Occurrence->State.'</strong>');
					}
					if (!$Squash) {
						if (!empty($Occurrence->LocationDescription)) {
							echo($Occurrence->LocationDescription);
							echo('<br />');
						}
						echo('<i>');
						echo($Occurrence->Event->Description);
						echo('</i>');
						$CI = & get_instance();
						if ($Occurrence->EndTime->Timestamp() > time()) {
							echo('<br />');
							if (FALSE === $Occurrence->UserAttending) {
								echo('not attending');
								if ($Occurrence->Event->Source->IsSupported('attend')) {
									echo(' (<a href="'.site_url('calendar/actions/attend/'.
										$Occurrence->Event->Source->GetSourceId().
										'/'.urlencode($Occurrence->SourceOccurrenceId).
										'/accept'.$CI->uri->uri_string()).'">attend</a>');
									echo(', <a href="'.site_url('calendar/actions/attend/'.
										$Occurrence->Event->Source->GetSourceId().
										'/'.urlencode($Occurrence->SourceOccurrenceId).
										'/maybe'.$CI->uri->uri_string()).'">maybe attend</a>)');
								}
							} elseif (TRUE === $Occurrence->UserAttending) {
								echo('attending');
								if ($Occurrence->Event->Source->IsSupported('attend')) {
									echo(' (<a href="'.site_url('calendar/actions/attend/'.
										$Occurrence->Event->Source->GetSourceId().
										'/'.urlencode($Occurrence->SourceOccurrenceId).
										'/maybe'.$CI->uri->uri_string()).'">maybe attend</a>');
									echo(', <a href="'.site_url('calendar/actions/attend/'.
										$Occurrence->Event->Source->GetSourceId().
										'/'.urlencode($Occurrence->SourceOccurrenceId).
										'/decline'.$CI->uri->uri_string()).'">don\'t attend</a>)');
								}
							} else {
								echo('maybe attending');
								if ($Occurrence->Event->Source->IsSupported('attend')) {
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
						}
						if (!$ReadOnly && 'owned' === $Occurrence->Event->UserStatus) {
							echo('<br />');
							echo('<a href="'.site_url('calendar/actions/delete/'.
								$Occurrence->Event->Source->GetSourceId().
								'/'.urlencode($Occurrence->Event->SourceEventId).
								$CI->uri->uri_string()).'">delete</a>');
						}
						if (!$Squash && NULL !== $Occurrence->Event->Image) {
							echo('<br />');
							echo('<img src="'.$Occurrence->Event->Image.'" />');
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
	echo('<a href="'.$times['link'].'">');
	echo($times['date']->Format('l'));
	echo('<br />');
	echo($times['date']->Format('jS M'));
	echo('</a>');
	echo('</th>');
}
echo('</tr><tr>');
foreach ($Days as $date => $day) {
	$times = $day['events'];
	echo('<td>');
	if (array_key_exists('000000',$times)) {
		foreach ($times['000000'] as $occurrence) {
			if (!$occurrence->TimeAssociated) {
				DrawOccurrence($occurrence, $squash, $ReadOnly);
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
				DrawOccurrence($occurrence, $squash, $ReadOnly);
			}
		}
	}
	echo('</td>');
}
echo('</tr>');
echo('</table>');

?>