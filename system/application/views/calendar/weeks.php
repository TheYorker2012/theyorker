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

echo('<table id="calviewCalTable" border="0" cellpadding="0" cellspacing="0" width="100%">');
$last_term = $Weeks[count($Weeks)-1]['start']->AcademicTerm();
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
				$CI->load->view('calendar/occurrence_cell', array(
					'Occurrence' => & $occurrence,
					'Categories' => & $Categories,
					'Squash' => $squash,
					'Path' => $Path,
				));
			}
		}
		echo('</a></td>');
	}
	echo('</tr>');
}
echo('</table>');

?>