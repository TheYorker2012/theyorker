<?php
/**
 * @param $Prefix string Prefix to calendar ids.
 */
?>
<style type="text/css">
	
	table.recur-cal {
		text-align: center;
		width: 100%;
		border: 1px solid #A0A0A0;
		border-collapse: collapse;
	}
	
	table.recur-cal th {
		background-color: #F4F4F4;
		border: 1px solid #A0A0A0;
		color: #606060;
	}
	
	table.recur-cal th.term {
		background-color: #EEEEEE;
		color: #404040;
	}
	
	table.recur-cal td.today {
		border: 2px solid #808080;
	}
	table.recur-cal td.exists {
		font-weight: bold;
	}
	
	table.recur-cal td {
		background-color: #FFFCBA;
	}
	table.recur-cal td.weekend {
		background-color: #EFECAA;
	}
	
	table.recur-cal td.even {
		background-color: #FFF980;
	}
	table.recur-cal td.even.weekend {
		background-color: #EFE970;
	}
	
	table.recur-cal td.past {
		background-color: #EEEEEE;
	}
	table.recur-cal td.past.weekend {
		background-color: #DEDEDE;
	}
	table.recur-cal td.past.even {
		background-color: #DADADA;
	}
	table.recur-cal td.past.even.weekend {
		background-color: #CACACA;
	}
	
	table.recur-cal td.selected {
		border: 1px solid #404040;
		background-color: #20C1F0;
	}
	table.recur-cal td.selected.weekend {
		background-color: #10B1E0;
	}
	table.recur-cal td.selected.even {
		background-color: #38A0F0;
	}
	table.recur-cal td.selected.even.weekend {
		background-color: #2890E0;
	}
	
	table.recur-cal td.selected.start {
		border: 2px solid #000000;
		background-color: #FF6A00;
	}
	table.recur-cal td.selected.start.even {
		background-color: #FF6A00;
	}
	
</style>
<script type="text/javascript">
	var <?php echo($Prefix); ?>_dates = new Array();
	
	function <?php echo($Prefix); ?>ResetMinicalDates()
	{
		for (var date in <?php echo($Prefix); ?>_dates) {
			date_cell = document.getElementById('<?php echo($Prefix); ?>['+date+']');
			if (date_cell != null) {
				date_cell.className = <?php echo($Prefix); ?>_dates[date];
				delete <?php echo($Prefix); ?>_dates[date];
			}
		}
	}
	
	function <?php echo($Prefix); ?>AdjustMinicalDate(date, class)
	{
		date_cell = document.getElementById('<?php echo($Prefix); ?>['+date+']');
		if (date_cell != null) {
			if (!(date in <?php echo($Prefix); ?>_dates)) {
				<?php echo($Prefix); ?>_dates[date] = date_cell.className;
			}
			date_cell.className += " "+class;
		}
	}
</script>
<table class="recur-cal">
<?php
	$today = Academic_time::NewToday();
// 	$today = $today->Adjust('1month');
// 	$day_start = $today->StartOfWeek();
	$day_start = $today->Adjust('-'.$today->AcademicDay().'days');
	$term_name = '';
	$today_id = $today->Format('Ymd');
	for ($week_counter = 0; $week_counter < 52; ++$week_counter) {
		$this_term_name = $day_start->AcademicTermName(). ' ' . $day_start->AcademicYearName(2);
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
			echo("<td$classes id=\"${Prefix}[$cell_id]\">$day_of_month</td>");
			$day_start = $day_start->Adjust('1day');
		}
		echo("</tr>\n");
	}
?>
</table>