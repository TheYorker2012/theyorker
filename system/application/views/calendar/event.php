<?php

/**
 * @file views/calendar/event.php
 * @brief View for event information.
 *
 * @param $Event CalendarEvent Event information.
 * @param $Occurrence CalendarEvent,NULL Occurrence information.
 * @param $ReadOnly bool Whether the source is read only.
 * @param $Attendees array[string] Attending users.
 * @param $FailRedirect string URL fail redirect path.
 * @param $Path array[string => url] Paths to common event links. used:
 *	- 'delete'
 */



$CI = & get_instance();
?>
<div class="BlueBox">
	<?php if (NULL !== $Event) { ?>
		<h2><?php echo($Event->Name); ?></h2>
		<div><p>
			<?php
			if (NULL !== $Occurrence) {
				// date + time
				echo('<div class="Date">');
				echo($Occurrence->StartTime->Format('%D'));
				if ($Occurrence->TimeAssociated) {
					echo('. '.$Occurrence->StartTime->Format('%T'));
					echo('-');
					echo($Occurrence->EndTime->Format('%T'));
				}
				echo('</div>');
				
				echo('<div>');
				if (!empty($Occurrence->LocationDescription)) {
					echo('Location: '.$Occurrence->LocationDescription);
					echo('<br />');
				}
				if (!empty($Event->Organisations)) {
					$organisers = array();
					foreach ($Event->Organisations as $organisation) {
						$org_text = '';
						if ($organisation->InDirectory) {
							$org_text .= '<a href="'.site_url('directory/'.$organisation->ShortName).'">';
						}
						$org_text .= $organisation->Name;
						if ($organisation->InDirectory) {
							$org_text .= '</a>';
						}
						$organisers[] = $org_text;
					}
					echo('Organiser'.(count($organisers)>1 ? 's' : '').': '.implode(', ', $organisers));
					echo('<br />');
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
				
				echo('<i>');
				echo($Event->Description);
				echo('</i>');
				if ($Event->Source->IsSupported('attend') &&
					$Occurrence->UserHasPermission('attend') &&
					$Occurrence->EndTime->Timestamp() > time())
				{
					echo('<br />');
					if ('no' === $Occurrence->UserAttending) {
						echo('not attending');
						if ($Occurrence->UserHasPermission('set_attend')) {
							echo(' (<a href="'.site_url('calendar/actions/attend/'.
								$Event->Source->GetSourceId().
								'/'.urlencode($Occurrence->SourceOccurrenceId).
								'/accept'.$CI->uri->uri_string()).'">attend</a>');
							echo(', <a href="'.site_url('calendar/actions/attend/'.
								$Event->Source->GetSourceId().
								'/'.urlencode($Occurrence->SourceOccurrenceId).
								'/maybe'.$CI->uri->uri_string()).'">maybe attend</a>)');
						}
					} elseif ('yes' === $Occurrence->UserAttending) {
						echo('attending');
						if ($Occurrence->UserHasPermission('set_attend')) {
							echo(' (<a href="'.site_url('calendar/actions/attend/'.
								$Event->Source->GetSourceId().
								'/'.urlencode($Occurrence->SourceOccurrenceId).
								'/maybe'.$CI->uri->uri_string()).'">maybe attend</a>');
							echo(', <a href="'.site_url('calendar/actions/attend/'.
								$Event->Source->GetSourceId().
								'/'.urlencode($Occurrence->SourceOccurrenceId).
								'/decline'.$CI->uri->uri_string()).'">don\'t attend</a>)');
						}
					} elseif ('maybe' == $Occurrence->UserAttending) {
						echo('maybe attending');
						if ($Occurrence->UserHasPermission('set_attend')) {
							echo(' (<a href="'.site_url('calendar/actions/attend/'.
								$Event->Source->GetSourceId().
								'/'.urlencode($Occurrence->SourceOccurrenceId).
								'/accept'.$CI->uri->uri_string()).'">attend</a>');
							echo(', <a href="'.site_url('calendar/actions/attend/'.
								$Event->Source->GetSourceId().
								'/'.urlencode($Occurrence->SourceOccurrenceId).
								'/decline'.$CI->uri->uri_string()).'">don\'t attend</a>)');
						}
					}
				}
				echo('</p>');
			}
			echo('<p>');
			if (NULL !== $Event->Image) {
				echo('<br />');
				echo('<img src="'.$Event->Image.'" />');
			}
			echo('</p>');
			?>
			<form class="form" method="post" action="<?php echo(get_instance()->uri->uri_string()); ?>">
				<fieldset>
					<input class="button" type="submit" name="evview_return" value="Return" />
					<?php if (!$Event->ReadOnly) { ?>
						<input class="button" type="submit" name="evview_edit" value="Edit" />
					<?php } ?>
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
	<?php } else { ?>
		<form method="post" action="<?php echo(get_instance()->uri->uri_string()); ?>">
			<fieldset>
				<input type="submit" name="evview_return" value="Return" />
			</fieldset>
		</form>
	<?php } ?>
</div>