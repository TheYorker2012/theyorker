<?php
/**
 * @file views/calendar/occurrence_cell.php
 * @brief View for a single occurrence for drawing within a calendar.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @param $Occurrence The occurrence to draw.
 * @param $Categories The categories.
 * @param $Squash Whether to squash it up.
 * @param $ReadOnly Whether the occurrence can be edited by the user.
 * @param $Path Set of useful paths.
 */

$CI = & get_instance();

?>
<div id="ev_15" class="cal_event cal_event_nojs<?php
	$cat = $Occurrence->Event->Category;
	if (array_key_exists($cat, $Categories)) {
		echo(" cal_category_$cat");
	}
	echo(' '.implode(' ', CalCssGetEventClasses($Occurrence)));
?>">
	<?php
	echo('<div class="cal_event_heading"><a href="'.
		site_url(
			$Path->OccurrenceInfo($Occurrence).
			$CI->uri->uri_string().'">'.
			xml_escape($Occurrence->Event->Name)
		).
		'</a></div>'
	);
	?>
	<div class="cal_event_info">
		<?php
		if ($Occurrence->TimeAssociated) {
			echo($Occurrence->StartTime->Format('%T'));
			echo(' - ');
			echo($Occurrence->EndTime->Format('%T'));
			echo('<br />');
		}
		?>
	</div>
	<div class="cal_event_info">
		<i><?php
	if ($Occurrence->State == 'cancelled') {
		echo('Cancelled');
	} else {
		echo(js_nl2br(xml_escape($Occurrence->GetLocationDescription())));
	}
?></i><?php
		if (!$Squash) {
			/*
			if ('owner' === $Occurrence->Event->UserStatus) {
				$links = array();
				if ($Occurrence->UserHasPermission('publish')) {
					$links[] = '<a href="'.
						site_url($Path->OccurrencePublish($Occurrence).$CI->uri->uri_string()).
						'">publish</a>';
				}
				if ($Occurrence->UserHasPermission('delete')) {
					$links[] = '<a href="'.
						site_url($Path->OccurrenceDelete($Occurrence).$FailRedirect).
						'">delete</a>';
				}
				if ($Occurrence->UserHasPermission('cancel')) {
					$links[] = '<a href="'.
						site_url($Path->OccurrenceCancel($Occurrence).$CI->uri->uri_string()).
						'">cancel</a>';
				}
				if ($Occurrence->UserHasPermission('postpone')) {
					$links[] = '<a href="'.
						site_url($Path->OccurrencePostpone($Occurrence).$CI->uri->uri_string()).
						'">postpone</a>';
				}
				echo(' ('.implode(', ', $links).')');
			}
			*/
			echo('<i>');
			echo($Occurrence->Event->GetDescriptionHtml());
			echo('</i>');
		}
		$show_attendence = !$Squash;
		$attendence_actions = ($show_attendence
			? array('yes' => 'attend', 'no' => 'don&#039;t attend', 'maybe' => 'maybe attend')
			: array('yes' => 'Y', 'no' => 'N', 'maybe' => '?')
		);
		if ($Occurrence->UserHasPermission('set_attend') &&
			$Occurrence->State == 'published' /*&&
			$Occurrence->EndTime->Timestamp() > time()*/)
		{
			echo('<br />');
			if (!$show_attendence) {
				echo('attend:');
			}
			if ('no' === $Occurrence->UserAttending) {
				if ($show_attendence) {
					echo('not attending');
				}
				if ($Occurrence->Event->Source->IsSupported('attend')) {
					echo(' (<a href="'.
						site_url($Path->OccurrenceAttend($Occurrence,'yes')).$CI->uri->uri_string().
						'">'.$attendence_actions['yes'].'</a>');
					echo(', <a href="'.
						site_url($Path->OccurrenceAttend($Occurrence,'maybe')).$CI->uri->uri_string().
						'">'.$attendence_actions['maybe'].'</a>)');
				}
			} elseif ('yes' === $Occurrence->UserAttending) {
				if ($show_attendence) {
					echo('attending');
				}
				if ($Occurrence->Event->Source->IsSupported('attend')) {
					echo(' (<a href="'.
						site_url($Path->OccurrenceAttend($Occurrence,'maybe')).$CI->uri->uri_string().
						'">'.$attendence_actions['maybe'].'</a>');
					echo(', <a href="'.
						site_url($Path->OccurrenceAttend($Occurrence,'no')).$CI->uri->uri_string().
						'">'.$attendence_actions['no'].'</a>)');
				}
			} elseif ('maybe' === $Occurrence->UserAttending) {
				if ($show_attendence) {
					echo('maybe attending');
				}
				if ($Occurrence->Event->Source->IsSupported('attend')) {
					echo(' (<a href="'.
						site_url($Path->OccurrenceAttend($Occurrence,'yes')).$CI->uri->uri_string().
						'">'.$attendence_actions['yes'].'</a>');
					echo(', <a href="'.
						site_url($Path->OccurrenceAttend($Occurrence,'no')).$CI->uri->uri_string().
						'">'.$attendence_actions['no'].'</a>)');
				}
			}
		}
		
		if (!$Squash) {
			if (NULL !== $Occurrence->Event->Image) {
				echo('<br />');
				echo('<img src="'.$Occurrence->Event->Image.'" />');
			}
		}
		?>
	</div>
</div>