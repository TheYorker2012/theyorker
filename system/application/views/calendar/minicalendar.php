<?php
/**
 * @param $Onclick string JS function to call.
 * @param $Links array[Ymd => url] Date organised structure of links.
 * @param $ClassNames array[Ymd => array[classname]] Class names per day.
 * @param $WeekStart int First day of week, 0:sunday, 1:monday...
 * @param $Legend array[description => styles] Legend information.
 */

// Load the calendar css helper
get_instance()->load->helper('calendar_css_classes');

if (!isset($WeekStart) || NULL === $WeekStart) {
	$WeekStart = 1;
}
?>
<table class="recur-cal cal-text<?php if (isset($Onclick) && NULL !== $Onclick) echo(' allclick'); ?>">
<?php
	// Legend
	if (isset($Legend) && is_array($Legend) && !empty($Legend)) {
		echo('<tr><th colspan="8" class="term">Legend</th></tr>');
		foreach ($Legend as $legend_info) {
			list($classes_array, $description) = $legend_info;
			$classnames = join(' ',$classes_array);
			echo("<tr><th /><td colspan=\"7\" class=\"$classnames\">$description</td></tr>");
		}
	}

	$day_of_week_headings = array('S','M','T','W','T','F','S');
	$term_date_alteration = '4days';
	$today = Academic_time::NewToday();
// 	$today = $today->Adjust('1month');
// 	$day_start = $today->StartOfWeek();
	$day_start = $today->Adjust('-'.$today->AcademicDay().'days')->StartOfWeek($WeekStart);
	$term_name = '';
	$today_id = $today->Format('Ymd');
	for ($week_counter = 0; $week_counter < 52; ++$week_counter) {
		$term_altered_date = $day_start->Adjust($term_date_alteration);
		$this_term_name = $term_altered_date->AcademicTermNameUnique(). ' ' . $term_altered_date->AcademicYearName(2);
		$week_number = $term_altered_date->AcademicWeek();
		if ($this_term_name != $term_name) {
			$term_name = $this_term_name;
			$this_term_name .= ' ('.$day_start->Format('M').')';
			?>	<tr><th colspan="8" class="term"><?php echo($this_term_name); ?></th></tr>
	<tr><th></th><?php
			for ($day = 0; $day < 7; ++$day) {
				$dayofweek = ($WeekStart + $day) % 7;
				echo('<th>'.$day_of_week_headings[$dayofweek].'</th>');
			}
	?></tr>
<?php	}
		echo("\t<tr><th>$week_number</th>");
		for ($day_counter = 0; $day_counter < 7; ++$day_counter) {
			$day_of_month = $day_start->DayOfMonth();
			$month = $day_start->Month();
			$cell_id = $day_start->Format('Ymd');
			$classes_list = CalCssGetDateClasses($day_start, $WeekStart+$day_counter);
			if (isset($ClassNames[$cell_id])) {
				$classes_list = array_merge($classes_list, $ClassNames[$cell_id]);
			}
			if (!empty($classes_list)) {
				$classes = ' class="'.implode(' ', $classes_list).'"';
			} else {
				$classes = '';
			}
			if (isset($Onclick) && $Onclick !== NULL) {
				$classes .= " onclick=\"$Onclick('$cell_id');\"";
			}
			$wrapper = array('','');
			if (isset($Links[$cell_id])) {
				$wrapper[0] = '<a href="'.$Links[$cell_id].'">';
				$wrapper[1] = '</a>';
			}
			echo("<td$classes id=\"mc$cell_id\">$wrapper[0]&nbsp;$day_of_month&nbsp;$wrapper[1]</td>");
			$day_start = $day_start->Adjust('1day');
		}
		echo("</tr>\n");
	}
?>
</table>
