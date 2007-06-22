<?php

echo('<div align="center">');
if (isset($BackwardUrl)) {
	echo('<a href="'.$BackwardUrl.'"><img src="/images/prototype/calendar/backward.gif" alt="Backward" /></a> ');
}
if (isset($NowUrl)) {
	echo('<a href="'.$NowUrl.'">'.$NowUrlLabel.'</a> ');
}
if (isset($ForwardUrl)) {
	echo('<a href="'.$ForwardUrl.'"><img src="/images/prototype/calendar/forward.gif" alt="Forward" /></a> ');
}
echo('</div>');

$squash = count($Days) > 3;

function DrawOccurrence(&$Occurrence, &$Categories, $Squash, $Path)
{
	$CI = & get_instance();
	?>
	<div id="ev_15" class="calviewIndEventBox2" style="width: 100%;<?php
		$cat = $Occurrence->Event->Category;
		if (array_key_exists($cat, $Categories)) {
			if (array_key_exists('colour', $Categories[$cat])) {
				echo(' background-color:#'.$Categories[$cat]['colour'].';');
			}
		}
	?>">
		<div style="padding: 2px;font-size: small;">
			<?='<span><a href="' . $Path['edit'] . '/' . $Occurrence->Event->Source->GetSourceId(). '/' . urlencode($Occurrence->Event->SourceEventId) . '/' . urlencode($Occurrence->SourceOccurrenceId) . $CI->uri->uri_string().'">'.$Occurrence->Event->Name.'</a></span>'?>
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
$last_term = -1;
foreach ($Weeks as $key => $week) {
	if ($last_term !== $week['start']->AcademicTerm()) {
		echo('<tr><td colspan="'.(count($week['days'])+1).'"><h2>');
		echo($week['start']->AcademicTermName().' '.
			 $week['start']->AcademicTermTypeName().' '.
			 $week['start']->AcademicYearName());
		echo('</h2></td></tr>');
		$last_term = $week['start']->AcademicTerm();
	}
	echo('<tr>');
	echo('<th></th>');
	foreach ($week['days'] as $date => $day) {
		echo('<th class="calviewCalHeadingCell">');
		echo('<a href="'.$day['link'].'">');
		echo($day['date']->Format('l'));
		echo('<br />');
		echo($day['date']->Format('jS M'));
		echo('</a>');
		echo('</th>');
	}
	echo('</tr><tr>');
	echo('<th><a href="'.$week['link'].'">'.$week['start']->AcademicWeek().'</a></th>');
	foreach ($week['days'] as $date => $day) {
		$times = $day['events'];
		echo('<td><a href="'.$day['link'].'">');
		foreach ($times as $time => $ocs) {
			foreach ($ocs as $occurrence) {
				DrawOccurrence($occurrence, $Categories, $squash, $Path);
			}
		}
		echo('</a></td>');
	}
	echo('</tr>');
}
echo('</table>');

?>