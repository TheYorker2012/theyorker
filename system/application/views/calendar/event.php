<?php

/**
 * @file views/calendar/event.php
 * @brief View for event information.
 *
 * @param $Event CalendarEvent Event information.
 * @param $Occurrence CalendarEvent,NULL Occurrence information.
 * @param $ReadOnly bool Whether the event is read only.
 * @param $Attendees array[string] Attending users.
 */



$CI = & get_instance();
?>
<div class="BlueBox">
	<span><?=$Occurrence->Event->Name?></span>
	<div><p>
		<?php
		if ($Occurrence->TimeAssociated) {
			echo($Occurrence->StartTime->Format('g:ia'));
			echo('-');
			echo($Occurrence->EndTime->Format('g:ia'));
			echo('<br />');
		}
		if ('published' !== $Occurrence->State) {
			echo('<strong>'.$Occurrence->State.'</strong>');
			if (!$ReadOnly && 'owned' === $Occurrence->Event->UserStatus) {
				$links = array();
				if ('none' !== VipMode() &&
					'draft' === $Occurrence->State &&
					$Occurrence->Event->Source->GetSourceId() === 0)
				{
					$links[] = '<a href="'.vip_url('calendar/publish/'.$Occurrence->Event->SourceEventId.$CI->uri->uri_string()).'">publish</a>';
				}
				$links[] = '<a href="'.site_url('calendar/actions/delete/'.
					$Occurrence->Event->Source->GetSourceId().
					'/'.urlencode($Occurrence->Event->SourceEventId).
					$CI->uri->uri_string()).'">delete</a>';
				echo(' ('.implode(',', $links).')');
			}
			echo('<br />');
		}
		if (!empty($Occurrence->LocationDescription)) {
			echo($Occurrence->LocationDescription);
			echo('<br />');
		}
		echo('<i>');
		echo($Occurrence->Event->Description);
		echo('</i>');
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
		if (NULL !== $Occurrence->Event->Image) {
			echo('<br />');
			echo('<img src="'.$Occurrence->Event->Image.'" />');
		}
		echo('</p>');
		if (isset($Attendees)) {
		var_dump($Attendees);
			/*echo('<h2>Confirmed Attendees</h2>');
			echo('<ul>');
			foreach ($Attendees as $attendee) {
				echo('<li>'.$attendee.'</li>');
			}
			echo('</ul>');*/
		}
		?>
		
	</div>
</div>