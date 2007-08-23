<?php
/**
* @file views/calendar/occurrence_selector.php
* @brief Javascript infested page to select multiple occurrences displayed on a calendar or list
* @author James Hogan (jh559@cs.york.ac.uk)
* @author Chris Travis (no doubt he'll be able to fix james' rubbish)
*
* @param $Event       CalendarEvent             Event information.
* @param $Occurrences array[CalendarOccurrence] Allowed occurrences with possible extra FormSelected field.
* @param $InputName   string                    Name of input fields.
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

<table border="1" cellspacing="1" cellpadding="1">
	<?php
	foreach ($Occurrences as $occurrence) {
		?>
		<tr>
			<td>
				<input type="checkbox" name="<?php echo($InputName.'['.$occurrence->OccurrenceId.']'); ?>" <?php
					if (array_key_exists('FormSelected',$occurrence) && $occurrence->FormSelected) {
						echo('checked="checked" ');
					}
				?>/>
			</td>
			<td>
				<div class="Date">
					<?php echo($occurrence->StartTime->Format('%D %T')); ?>
				</div>
			</td>
		</tr>
		<?
	}
	?>
</table>
