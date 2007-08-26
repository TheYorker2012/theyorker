<?php

/**
 * @file views/calendar/event_edit.php
 * @brief View for editing event information.
 *
 * @param $Event CalendarEvent Event information.
 * @param $Occurrence CalendarEvent,NULL Occurrence information.
 * @param $ReadOnly bool Whether the source is read only.
 * @param $Attendees array[string] Attending users.
 * @param $FailRedirect string URL fail redirect path.
 */



$CI = & get_instance();
?>
<div class="BlueBox">
	<h2><?php echo($Occurrence->Event->Name); ?></h2>
	<div><p>
		<?php
		// date + time
		echo('<div class="Date">');
		echo($Occurrence->StartTime->Format('%D'));
		if ($Occurrence->TimeAssociated) {
			echo('. '.$Occurrence->StartTime->Format('%T'));
			echo('-');
			echo($Occurrence->EndTime->Format('%T'));
		}
		echo('</div>');
		
		echo('<p>');
		if ('published' === $Occurrence->State || 'owned' === $Event->UserStatus) {
			echo('<strong>'.$Occurrence->State.'</strong>');
		}
		if ('owned' === $Event->UserStatus) {
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
			echo(' ('.implode(',', $links).')');
		}
		echo('<br />');
		if (!empty($Occurrence->LocationDescription)) {
			echo('at: '.$Occurrence->LocationDescription);
			echo('<br />');
		}
		echo('<i>');
		echo($Occurrence->Event->Description);
		echo('</i>');
		if (NULL !== $Occurrence->Event->Image) {
			echo('<br />');
			echo('<img src="'.$Occurrence->Event->Image.'" />');
		}
		echo('</p>');
		?>
		<form class="form" method="post" action="<?php echo(get_instance()->uri->uri_string()); ?>">
			<fieldset>
				<input class="button" type="submit" name="evview_return" value="Finished" />
			</fieldset>
		</form>
		<?php
		// Attendee list
		if (isset($Attendees) && !empty($Attendees)) {
			echo('<h2>Attendees</h2>');
			echo('<ul>');
			foreach (array(true,false) as $friend) {
				foreach ($Attendees as $attendee) {
					if ($attendee['friend'] === $friend) {
						echo('<li>');
						$linked = array_key_exists('link', $attendee);
						if ($attendee['friend']) {
							echo('<b>');
						}
						if ($linked) {
							echo('<a href="'.$attendee['link'].'" target="_blank">');
						}
						echo($attendee['name']);
						if ($linked) {
							echo('</a>');
						}
						if ($attendee['friend']) {
							echo('</b>');
						}
						echo(' '.$attendee['attend'].'</li>');
					}
				}
			}
			echo('</ul>');
		}
		?>
	</div>
</div>