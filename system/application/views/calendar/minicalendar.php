<?php
/**
 * @param $Onclick string JS function to call.
 */
?>
<table class="recur-cal">
<?php
	$today = Academic_time::NewToday();
// 	$today = $today->Adjust('1month');
// 	$day_start = $today->StartOfWeek();
	$day_start = $today->Adjust('-'.$today->AcademicDay().'days');
	$term_name = '';
	$today_id = $today->Format('Ymd');
	for ($week_counter = 0; $week_counter < 52; ++$week_counter) {
		$this_term_name = $day_start->AcademicTermNameUnique(). ' ' . $day_start->AcademicYearName(2);
		if ($this_term_name != $term_name) {
			$term_name = $this_term_name;
			$this_term_name .= ' ('.$day_start->Format('M').')';
			?>	<tr><th colspan="8" class="term"><?php echo($this_term_name); ?></th></tr>
	<tr><th></th><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th><th>S</th></tr>
<?php	}
		$week_number = $day_start->AcademicWeek();
		echo("\t<tr><th>$week_number</th>");
		for ($day_counter = 0; $day_counter < 7; ++$day_counter) {
			$day_of_month = $day_start->DayOfMonth();
			$month = $day_start->Month();
			$cell_id = $day_start->Format('Ymd');
			$classes_list = array();
			if ($cell_id == $today_id) {
				$classes_list[] = 'today';
			}
			if ($month % 2 == 0) {
				$classes_list[] = 'even';
			}
			if ($day_counter>4) {
				$classes_list[] = 'weekend';
			}
			if ($day_start->Timestamp() < $today->Timestamp()) {
				$classes_list[] = 'past';
			}
			if (!empty($classes_list)) {
				$classes = ' class="'.implode(' ', $classes_list).'"';
			} else {
				$classes = '';
			}
			if (isset($Onclick)) {
				$classes .= " onclick=\"javascript:$Onclick('$cell_id');\"";
			}
			echo("<td$classes id=\"minical_$cell_id\">&nbsp;$day_of_month&nbsp;</td>");
			$day_start = $day_start->Adjust('1day');
		}
		echo("</tr>\n");
	}
?>
</table>
