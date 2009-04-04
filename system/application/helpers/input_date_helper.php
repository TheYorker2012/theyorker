<?php

get_instance()->load->helper('input');

/// Input date interface which takes timestamp or Academic_time.
class InputDateInterface extends InputInterface
{
	private $date = true;
	private $time = true;

	public function __construct($name, $default = null, $enabled = null, $auto = true)
	{
		if (null !== $default) {
			if (is_numeric($default)) {
				$default = (int)$default;
			}
			else {
				$default = $default->Timestamp();
			}
		}
		parent::__construct($name, $default, $enabled, $auto);
		get_instance()->main_frame->includeJs('javascript/simple_ajax.js');
		get_instance()->main_frame->includeJs('javascript/calendar_termdates.js');
		get_instance()->main_frame->includeJs('javascript/input_selector.js');
		get_instance()->main_frame->includeJs('javascript/input_date.js');
		get_instance()->main_frame->includeCss('stylesheets/input_selector.css');
		get_instance()->main_frame->includeCss('stylesheets/input_date.css');
		get_instance()->main_frame->includeCss('stylesheets/input_date-iefix.css',null,null,'IE');
		get_instance()->main_frame->includeCss('stylesheets/calendar.css');

		$this->div_classes = array('input_date');
	}

	public function setTimeEnabled($time)
	{
		$this->time = $time;
	}

	protected function _Load()
	{
		$value = $this->value;
		if (null === $value) {
			$value = Academic_time::NewToday();
		}
		else {
			$value = new Academic_time($value);
		}

		?><div	class="input_date_display"<?php
			?>	onclick="<?php echo(xml_escape('return input_date_click("'.$this->name.'");')); ?>"<?php
			?>	><?php

		if ($this->date) {
			?><span class="day" id="<?php echo($this->name.'__day'); ?>"><?php
				echo($value->Format('l'));
			?></span> <?php
			?>week <span class="week" id="<?php echo($this->name.'__wk'); ?>"><?php
				echo($value->AcademicWeek());
			?></span> <?php
			?>of <span class="term" id="<?php echo($this->name.'__term'); ?>"><?php
				echo(ucfirst($value->AcademicTermNameUnique()));
				echo(' '.$value->StartOfTerm()->Year());
			?></span> <?php
		}

		if ($this->time) {
			?>at <span class="hour" id="<?php echo($this->name.'__hr'); ?>"><?php echo($value->Hour()); ?></span><?php
			?>:<span class="minute" id="<?php echo($this->name.'__min'); ?>"><?php echo($value->Minute()); ?></span> <?php
		}

		?></div><?php

		$days = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
		?><div	class="input_date_selector"<?php
			?>	id="<?php echo($this->name.'__selector'); ?>"<?php
			?>	><?php
			// Init script
			?><script type="text/javascript"><?php
			echo(xml_escape('onLoadFunctions.push(function() {'.
								'input_date_init('.js_literalise($this->name).');'.
							'});', false));
			?></script><?php
			?><div><?php
				// Day of the week
				?><select	id="<?php echo($this->name.'__day_select'); ?>"<?php
						?>	name="<?php echo($this->name.'[day]'); ?>"<?php
						?>	onchange="<?php echo(xml_escape('return input_date_day_changed("'.$this->name.'");')); ?>"<?php
						?>><?php
					foreach ($days as $val => $day) {
						?><option value="<?php echo($val); ?>"<?php
							if ($val == $value->DayOfWeek(1)) {
								?> selected="selected"<?php
							}
							?>><?php
						echo($day);
						?></option><?php
					}
				?></select><?php
				// Week of the term
				?><span>week</span><?php
				?><select	id="<?php echo($this->name.'__wk_select'); ?>"<?php
						?>	name="<?php echo($this->name.'[wk]'); ?>"<?php
						?>	onchange="<?php echo(xml_escape('return input_date_day_changed("'.$this->name.'");'));?>"<?php
						?>><?php
					$weeks = $value->AcademicTermWeeks();
					for ($wk = 1; $wk <= $weeks; ++$wk) {
						?><option value="<?php echo($wk); ?>"<?php
							if ($wk == $value->AcademicWeek()) {
								?> selected="selected"<?php
							}
							?>><?php
						echo($wk);
						?></option><?php
					}
				?></select><?php
				// Term
				?><span>of</span><?php
				?><select	id="<?php echo($this->name.'__term_select'); ?>"<?php
						?>	name="<?php echo($this->name.'[term]'); ?>"<?php
						?>	onchange="<?php echo(xml_escape('return input_date_term_changed("'.$this->name.'");'));?>"<?php
						?>><?php
					$cur = Academic_time::NewToday();
					$sel_year = $value->AcademicYear();
					$sel_term = $value->AcademicTerm();
					$year = $cur->AcademicYear();
					$term = $cur->AcademicTerm();
					for ($i = 0; $i < 6; ++$i) {
						$cur = new Academic_time(Academic_time::StartOfAcademicTerm($year, $term));

						?><option value="<?php echo("$year-$term"); ?>"<?php
							if ($term == $sel_term && $year == $sel_year) {
								?> selected="selected"<?php
							}
							?>><?php
						echo(xml_escape(ucfirst($cur->AcademicTermNameUnique()).' '.$cur->Year()));
						?></option><?php

						++$term;
						if ($term == 6) {
							$term = 0;
							++$year;
						}
					}
				?></select><?php
				// Time of day
				if ($this->time) {
					?><span>at</span><?php
					?><select	id="<?php echo($this->name.'__hr_select'); ?>"<?php
							?>	name="<?php echo($this->name.'[hr]'); ?>"<?php
							?>	onchange="<?php echo(xml_escape('return input_date_time_changed("'.$this->name.'");'));?>"<?php
							?>><?php
						for ($hr = 0; $hr < 24; ++$hr) {
							?><option value="<?php echo($hr); ?>"<?php
								if ($hr == $value->Hour()) {
									?> selected="selected"<?php
								}
								?>><?php
							echo(sprintf('%02d', $hr));
							?></option><?php
						}
					?></select><?php
					?><span>:</span><?php
					?><select	id="<?php echo($this->name.'__min_select'); ?>"<?php
							?>	name="<?php echo($this->name.'[min]'); ?>"<?php
							?>	onchange="<?php echo(xml_escape('return input_date_time_changed("'.$this->name.'");'));?>"<?php
							?>><?php
						$minute = $value->Minute();
						$minute_interval = 5;
						for ($min = 0; $min < 60; $min += $minute_interval) {
							?><option value="<?php echo($min); ?>"<?php
								if ($min <= $minute &&
									$min+$minute_interval > $minute) {
									?> selected="selected"<?php
								}
								?>><?php
							echo(sprintf('%02d', $min));
							?></option><?php
						}
					?></select><?php
				}
				// Close button
				?><input	type="button" value="x"<?php
						?>	onclick="<?php echo(xml_escape('return input_selector_click("'.$this->name.'__selector");')); ?>"<?php
						?>	><?php
			?></div><?php
			?><div><?php
				?><table class="recur-cal cal-text"><?php
					// Days along the top
					?><tr><?php
						?><th /><?php
						foreach ($days as $day) {
							?><th><?php
							echo(xml_escape($day));
							?></th><?php
						}
					?></tr><?php
					$cur = $value->MondayWeek1OfTerm();
					$sel = $value->Midnight()->Timestamp();
					$today = Academic_time::NewToday()->Timestamp();
					$last_month = 0;
					$term = $cur->AcademicTerm();
					for ($wk = 1; $cur->AcademicTerm() == $term; ++$wk) {
						?><tr id="<?php echo($this->name.'__wk_'.$wk); ?>"><?php
							?><th><?php
								echo($wk);
							?></th><?php
							for ($dy = 0; $dy < 7; ++$dy) {
								$month = $cur->Month();
								$ts = $cur->Timestamp();
								$classes = array();
								if ($ts < $today) {
									$classes[] = "pa";
								}
								if ($month % 2 == 0) {
									$classes[] = "ev";
								}
								if ($ts == $today) {
									$classes[] = "tod";
								}
								if ($ts == $sel) {
									$classes[] = "sel";
								}
								if ($dy >= 5) {
									$classes[] = "we";
								}
								?><td	class="<?php echo(join(' ',$classes)); ?>"<?php
									?>	id="<?php echo($this->name.'__'.$cur->AcademicWeek().'_'.$cur->Format('D')); ?>"<?php
									?>	onclick="<?php echo(xml_escape(
											'return input_date_change('.js_literalise($this->name).','.
																		js_literalise($wk).','.
																		js_literalise($dy).');'
											)); ?>"<?php
									?>	><?php
									if ($month != $last_month) {
										echo(xml_escape($cur->Format('M')).'&nbsp;');
										$last_month = $month;
									}
									echo(xml_escape($cur->Format('j')));
								?></td><?php
								$cur = $cur->Adjust('+1day');
							}
						?></tr><?php
					}
				?></table><?php
			?></div><?php
		?></div><?php
	}

	protected function _Import(&$arr)
	{
		static $required = array('day','wk','term','hr','min');
		foreach ($required as $require) {
			if (!isset($arr[$require])) {
				return;
			}
		}
		$data = array();
		$data['day'] = $arr['day'];
		$data['wk'] = $arr['wk'];
		$data['term'] = $arr['term'];
		$data['hr'] = $arr['hr'];
		$data['min'] = $arr['min'];

		// validate
		$term_year = split('-', $data['term']);
		if (count($term_year) != 2 || !is_numeric($term_year[0]) || !is_numeric($term_year[1])) {
			return array("term is not valid, count=".count($term_year));
		}
		list($data['yr'],$data['term']) = $term_year;
		$now_year = (int)date('Y');
		$ranges = array(
			'day' => array(0, 6, 1),
			'wk' => array(0, 19, 0),
			'term' => array(0, 5, 0),
			'yr' => array($now_year-1, $now_year+10, 0),
			'hr' => array(0, 23, 0),
			'min' => array(0, 59, 0),
		);
		foreach ($data as $id => &$datum) {
			if (!is_numeric($datum)) {
				return array("$id is not numeric");
			}
			$datum = (int)$datum;
			if ($datum < $ranges[$id][0] ||
				$datum > $ranges[$id][1])
			{
				return array("$id is out of range");
			}
			$datum += $ranges[$id][2];
		}

		$this->value = get_instance()->academic_calendar->Academic(
			$data['yr'], $data['term'], $data['wk'], $data['day'],
			$data['hr'], $data['min'], 0
		)->Timestamp();
		return array();
	}
}

?>
