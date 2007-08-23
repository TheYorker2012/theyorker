<?php

/**
 * @file views/calendar/event_action.php
 * @brief View for performing actions on events.
 *
 * @param $Event Event to alter.
 * @param $Occurrences Occurrences which can be altered.
 * @param $Properties Properties array.
 * @param $FormName string Form name.
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
		<h3><?php echo($Event->Name); ?></h3>
		<p><?php echo($Event->Description); ?></p>
		<form class="form" name="<?php echo($FormName); ?>" method="post" action="<?php echo(get_instance()->uri->uri_string()); ?>">
			<?php
			if (!empty($Occurrences)) {
				?>
				<P>Please confirm the occurrences of the event you want to <?php echo(strtolower($verb)); ?>:</P>
				<?php
				get_instance()->load->view('calendar/occurrence_selector', array(
					'Event' => $Event,
					'Occurrences' => $Occurrences,
					'InputName' => 'evpub_occurrences',
				));
				echo($Properties['confirm_message']['_wikitext']);
			} else {
				// No occurrences can be published
				echo($Properties['error']['no_occurrences']['_wikitext']);
			}
			?>
			<fieldset>
				<?php if (!empty($Occurrences)) { ?>
					<input class="button" type="submit" name="<?php echo($FormName); ?>_confirm" value="<?php echo($verb); ?>" />
				<?php } ?>
				<input class="button" type="submit" name="<?php echo($FormName); ?>_cancel" value="Cancel" />
			</fieldset>
		</form>
	</div>
</div>