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
				$Occurrence->Event->Name
			).
			'</a></span>'
		);
		?>
		<div class="calviewExpandedSmall" id="ev_es_%%refid%%" style="margin-top: 2px;">
			<div>
				<?php
				if ($Occurrence->TimeAssociated) {
					echo($Occurrence->StartTime->Format('g:ia'));
					echo('-');
					echo($Occurrence->EndTime->Format('g:ia'));
					echo('<br />');
				}
				if ('published' !== $Occurrence->State) {
					echo('<strong>'.$Occurrence->State.'</strong>');
					if (!$Squash && !$ReadOnly && 'owned' === $Occurrence->Event->UserStatus) {
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
				if (!$Squash) {
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
				}
				?>
			</div>
		</div>
	</div>
</div>