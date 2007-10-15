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
<div id="ev_15" class="calviewIndEventBox2" style="width: 100%;<?php
	$cat = $Occurrence->Event->Category;
	if (array_key_exists($cat, $Categories)) {
		if (array_key_exists('colour', $Categories[$cat])) {
			echo(' background-color:#'.$Categories[$cat]['colour'].';');
		}
	}
?>">
	<div style="padding: 2px;font-size: small;">
		<?php
		echo('<span><a href="'.
			site_url(
				$Path->OccurrenceInfo($Occurrence).
				$CI->uri->uri_string().'">'.
				htmlentities($Occurrence->Event->Name, ENT_QUOTES, 'utf-8')
			).
			'</a></span>'
		);
		?>
		<div class="calviewExpandedSmall" id="ev_es_%%refid%%" style="margin-top: 2px;">
			<div>
				<?php
				if ($Occurrence->TimeAssociated) {
					echo($Occurrence->StartTime->Format('%T'));
					echo('-');
					echo($Occurrence->EndTime->Format('%T'));
					echo('<br />');
				}
				if ('published' === $Occurrence->State ||
					'cancelled' === $Occurrence->State ||
					'owned' === $Occurrence->Event->UserStatus)	
				{
					echo('<strong>'.$Occurrence->State.'</strong>');
				}
				if (!$Squash) {
					if ('owned' === $Occurrence->Event->UserStatus) {
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
					echo('<br />');
					if (!empty($Occurrence->LocationDescription)) {
						echo(htmlentities($Occurrence->LocationDescription, ENT_QUOTES, 'utf-8'));
						echo('<br />');
					}
					echo('<i>');
					echo($Occurrence->Event->GetDescriptionHtml());
					echo('</i>');
				}
				$show_attendence = !$Squash;
				$attendence_actions = ($show_attendence
					? array('yes' => 'attend', 'no' => 'don&apos;t attend', 'maybe' => 'maybe attend')
					: array('yes' => 'Y', 'no' => 'N', 'maybe' => '?')
				);
				if ($Occurrence->UserHasPermission('set_attend') &&
					$Occurrence->State == 'published' /*&&
					$Occurrence->EndTime->Timestamp() > time()*/)
				{
					if ($show_attendence) {
						echo('<br />');
					} else {
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
	</div>
</div>