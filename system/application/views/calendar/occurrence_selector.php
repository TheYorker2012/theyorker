<?php
/**
 * @file views/calendar/occurrence_selector.php
 * @brief Javascript infested page to select multiple occurrences displayed on a calendar or list
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @author Chris Travis (no doubt he'll be able to fix james' rubbish)
 *
 * @param $Event        CalendarEvent             Event information.
 * @param $Occurrences  array[CalendarOccurrence] Allowed occurrences.
 * @param $InputName    string                    Name of input fields.
 * @param $OpStates     array[var => array('class' => string, 'label' => string)]
 *
 * @param $FormSelected array[int => bool],NULL   Whether certain occurrences are selected.
 * @param $OpStatuses   array[int => var],NULL   Which occurrences have been operated on.
 *
 *
 *
 * @todo make this nice instead of horrible :P
 */
?>

<?php
/**
* For now i'm just gonna make a list of checkboxes, presumably the javascript
* can be made to adjust these before posting.
*/
?>

<table border="0" cellspacing="2" cellpadding="0">
	<tr>
		<th />
		<th>When</th>
		<th>State</th>
		<th />
	</tr>
	<?php
	foreach ($Occurrences as $id => $occurrence) {
		?>
			<td>
				<?php if (array_key_exists($id,$FormSelected)) { ?>
					<input type="checkbox" name="<?php echo($InputName.'['.$occurrence->SourceOccurrenceId.']'); ?>" <?php
						if ($FormSelected[$id]) {
							echo('checked="checked" ');
						}
					?>/>
				<?php } ?>
			</td>
			<td>
				<div class="Date">
					<?php echo($occurrence->StartTime->Format('%D %T')); ?>
				</div>
			</td>
			<td>
				<?php echo($occurrence->State); ?>
			</td>
			<td>
				<?php if (array_key_exists($id, $OpStatuses)) { ?>
					<?php
						echo $OpStates[$OpStatuses[$id]]['label'];
					?>
				<?php } ?>
			</td>
		</tr>
		<?
	}
	?>
</table>
