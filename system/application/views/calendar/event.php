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
<div id='RightColumn'>
	<h2 class="first">Occurrences of this Event</h2>
	<?php
	$links = array();
	$class_names = array();
	$mini_legend = array();
	$legend_data = array(
		'dra' => array(0, '<a>&nbsp;Personal or Draft&nbsp;</a>'),
		'pub' => array(1, '<a>&nbsp;Published&nbsp;</a>'),
		'can' => array(2, '<a>&nbsp;Cancelled&nbsp;</a>'),
	);
	foreach ($Event->Occurrences as &$occurrence) {
		$date_id = $occurrence->StartTime->Format('Ymd');
		$links[$date_id] =
			site_url($Path->OccurrenceInfo($occurrence)).$FailRedirect;
		$classname = substr($occurrence->State, 0, 3);
		$class_names[$date_id][] = $classname;
		// Add the legend item for this class if it isn't there yet.
		if (isset($legend_data[$classname]) &&
			!isset($mini_legend[$legend_data[$classname][0]]))
		{
			$mini_legend[$legend_data[$classname][0]] = array(
				array($classname),
				$legend_data[$classname][1],
			);
		}
	}
	if (NULL !== $Occurrence) {
		$class_names[$Occurrence->StartTime->Format('Ymd')][] = 'cur';
	}
	// Sort the legend by the (numeric) key.
	ksort($mini_legend);
	get_instance()->load->view('calendar/minicalendar', array(
		'Links'			=> $links,
		'ClassNames'	=> $class_names,
		'WeekStart'		=> NULL,
		'Onclick'		=> NULL,
		'Legend'		=> $mini_legend
	));
	?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<?php if (NULL !== $Event) { ?>
			<h2><?php echo(htmlentities($Event->Name, ENT_QUOTES, 'utf-8')); ?></h2>
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
					$location = $Occurrence->GetLocationDescription();
					if (is_string($location) && !empty($location)) {
						echo('Location: '.htmlentities($location, ENT_QUOTES, 'utf-8'));
						echo('<br />');
					}
				} else {
					echo('<div>');
				}
				if (!empty($Event->Organisations)) {
					$organisers = array();
					foreach ($Event->Organisations as $organisation) {
						$org_text = '';
						if ($organisation->InDirectory) {
							$org_text .= '<a href="'.site_url('directory/'.$organisation->ShortName).'">';
						}
						$org_text .= htmlentities($organisation->Name, ENT_QUOTES, 'utf-8');
						if ($organisation->InDirectory) {
							$org_text .= '</a>';
						}
						$organisers[] = $org_text;
					}
					echo('Organiser'.(count($organisers)>1 ? 's' : '').': '.implode(', ', $organisers));
					echo('<br />');
				}
				echo('</div>');
					
				if (NULL !== $Occurrence) {
					echo('<p>');
					if ('published' === $Occurrence->State ||
						'cancelled' === $Occurrence->State ||
						'owner' === $Event->UserStatus)	
					{
						echo('<strong>'.$Occurrence->State.'</strong>');
					}
					/*
					if ('owner' === $Event->UserStatus) {
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
					echo('</p>');
				}
				echo('<p><i>');
				echo($Event->GetDescriptionHtml());
				echo('</i></p>');
				if (NULL !== $Occurrence &&
					$Occurrence->UserHasPermission('set_attend') &&
					$Occurrence->State == 'published' /*&&
					$Occurrence->EndTime->Timestamp() > time()*/)
				{
					$attendence_actions = array('yes' => 'attend', 'no' => 'don&apos;t attend', 'maybe' => 'maybe attend');
					echo('<p>');
					if ('no' === $Occurrence->UserAttending) {
						echo('not attending');
						if ($Occurrence->Event->Source->IsSupported('attend')) {
							echo(' (<a href="'.
								site_url($Path->OccurrenceAttend($Occurrence,'yes')).$CI->uri->uri_string().
								'">'.$attendence_actions['yes'].'</a>');
							echo(', <a href="'.
								site_url($Path->OccurrenceAttend($Occurrence,'maybe')).$CI->uri->uri_string().
								'">'.$attendence_actions['maybe'].'</a>)');
						}
					} elseif ('yes' === $Occurrence->UserAttending) {
						echo('attending');
						if ($Occurrence->Event->Source->IsSupported('attend')) {
							echo(' (<a href="'.
								site_url($Path->OccurrenceAttend($Occurrence,'maybe')).$CI->uri->uri_string().
								'">'.$attendence_actions['maybe'].'</a>');
							echo(', <a href="'.
								site_url($Path->OccurrenceAttend($Occurrence,'no')).$CI->uri->uri_string().
								'">'.$attendence_actions['no'].'</a>)');
						}
					} elseif ('maybe' === $Occurrence->UserAttending) {
						echo('maybe attending');
						if ($Occurrence->Event->Source->IsSupported('attend')) {
							echo(' (<a href="'.
								site_url($Path->OccurrenceAttend($Occurrence,'yes')).$CI->uri->uri_string().
								'">'.$attendence_actions['yes'].'</a>');
							echo(', <a href="'.
								site_url($Path->OccurrenceAttend($Occurrence,'no')).$CI->uri->uri_string().
								'">'.$attendence_actions['no'].'</a>)');
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
								echo(htmlentities($attendee['name'], ENT_QUOTES, 'utf-8'));
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
</div>