<?php
/**
 * @file views/calendar/term_selector.php
 * @author James Hogan (jh559)
 *
 * @param $Start  Academic_time  First week displayed.
 * @param $End    Academic_time  Last week displayed.
 * @param $Path   CalendarPaths  Paths object.
 */

if (true) {
	$terms = array(
		'Term 1', 'Christmas',
		'Term 2', 'Easter',
		'Term 3', 'Summer',
	);
	$term_shortnames = array(
		'autumn', 'christmas',
		'spring', 'easter',
		'summer', 'holiday',
	);
	
	$start_year = $Start->AcademicYear();
	$start_term = $Start->AcademicTerm();
	$start_week = $Start->AcademicWeek();
	
	$end_year   = $End->AcademicYear();
	$end_term   = $End->AcademicTerm();
	$end_week   = $End->AcademicWeek();
	
	$now = new Academic_time(time());
	
	
	echo('<div>'."\n");
	echo('	<ul class="cal_term_select">'."\n");
	echo('		<li class="year"><a href="'.site_url($Path->Range(($start_year-1).'-summer')).'">'.($start_year-1).'/'.($start_year).'</a></li>'."\n");
	foreach ($terms as $term => $term_name) {
		echo('		<li class="'.($term % 2 == 0 ? 'term' : 'holiday').($term == $start_term ? ' selected':'').'">');
		echo('<a href="'.site_url($Path->Range($start_year.'-'.$term_shortnames[$term])).'">'.$term_name.'</a>');
		echo('</li>'."\n");
	}
	echo('		<li class="year"><a href="'.site_url($Path->Range(($start_year+1).'-autumn')).'">'.($start_year+1).'/'.($start_year+2).'</a></li>'."\n");
	echo('	</ul>'."\n");
	echo('	<ul class="cal_week_select '.($start_term % 2 == 0 ? 'term' : 'holiday').'">'."\n");
	$days_in_term = Academic_time::LengthOfAcademicTerm($start_year, $start_term);
	for ($i = 1; $i <= $days_in_term/7; ++$i) {
		$now_term = ($start_term == $now->AcademicTerm()) && ($start_year == $now->AcademicYear());
		$now_week = $now_term && ($i == $now->AcademicWeek());
		$classes = array();
		if (($i >= $start_week) && ( ($i <= $end_week) || ($end_term > $start_term) || ($end_year > $start_year) )) {
			$classes[] = 'selected';
		}
		if ($now_week) {
			$classes[] = 'thisweek';
		}
		$class = (empty($classes) ? '' : ' class="'.implode(' ',$classes).'"');
		echo('		<li'.$class.'>');
		echo('<a href="'.site_url($Path->Range($start_year.'-'.$term_shortnames[$start_term].'-'.$i)).'">'.$i.'</a>');
		echo('</li>'."\n");
	}
	echo('	</ul>'."\n");
	echo('	<div class="cal_term_week_clear"></div>'."\n");
	echo('</div>'."\n");
}

?>