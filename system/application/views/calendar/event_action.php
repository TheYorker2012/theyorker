<?php

/**
 * @file views/calendar/event_action.php
 * @brief View for performing actions on events.
 *
 * @param $Event Event to alter.
 * @param $Occurrences Occurrences which can be altered.
 * @param $Properties Properties array.
 * @param $FormName string Form name.
 * @param $OpStates     array[var => array('class' => string, 'label' => string)]
 *
 * @param $FormSelected array[int => bool],NULL   Whether certain occurrences are selected.
 * @param $OpStatuses   array[int => var],NULL   Which occurrences have been operated on.
 */

$verb = $Properties['verb']['_text'];
?>

<div id='RightColumn'>
	<h2>To-Do</h2>
	
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo($verb); ?> Events</h2>
		<?php echo($Properties['main']['_wikitext']); ?>
		<h3><?php echo(htmlentities($Event->Name, ENT_QUOTES, 'utf-8')); ?></h3>
		<p><?php echo(htmlentities($Event->Description, ENT_QUOTES, 'utf-8')); ?></p>
		<form class="form" name="<?php echo($FormName); ?>" method="post" action="<?php echo(get_instance()->uri->uri_string()); ?>">
			<?php
			if (!empty($Occurrences)) {
				?>
				<P>Please confirm the occurrences of the event you want to <?php echo(strtolower($verb)); ?>:</P>
				<?php
				get_instance()->load->view('calendar/occurrence_selector', array(
					'Event' => $Event,
					'Occurrences' => $Occurrences,
					'InputName' => $FormName.'_occurrences',
					'FormSelected' => $FormSelected,
					'OpStatuses' => $OpStatuses,
				));
			}
			if (!empty($FormSelected)) {
				echo($Properties['confirm_message']['_wikitext']);
			} else {
				echo($Properties['error']['no_occurrences']['_wikitext']);
			}
			?>
			<fieldset>
				<?php if (!empty($FormSelected)) { ?>
					<input class="button" type="submit" name="<?php echo($FormName); ?>_confirm" value="<?php echo($verb); ?>" />
				<?php } ?>
				<input class="button" type="submit" name="<?php echo($FormName); ?>_cancel" value="Return" />
			</fieldset>
		</form>
	</div>
</div>