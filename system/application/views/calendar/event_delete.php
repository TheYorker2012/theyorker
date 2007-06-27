<?php

/**
 * @file views/calendar/event_delete.php
 * @brief View for deleting events.
 *
 * - Warns the user that this is permanent change.
 * - Provides choices (publish all, individual, etc)
 *
 * @param $Event Event to have parts deleted.
 * @param $Occurrences Occurrences to delete (possibly all).
 */

?>

<div id='RightColumn'>
	<h2>To-Do</h2>
	
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>Delete Events</h2>
		<p>
			Deleting the event cannot be undone.
		</p>
		<h3><?php echo($Event->Name); ?></h3>
		<p><?php echo($Event->Description); ?></p>
		<?php if (count($Event->Occurrences) > 1) { ?>
			<P>The event occurs at these times:</P>
		<? } else { ?>
			<P>The event occurs at:</P>
		<?php } ?>
		<ul>
		<?
		foreach ($Event->Occurrences as $occurrence) {
			?>
			<li>
				<?php echo($occurrence->StartTime->Format('%D %T')); ?>
			</li>
			<?php
		}
		?>
		</ul>
		<p>
			Are you sure you wish to delete the event and all its occurrences?
		</p>
		<form method="post" action="<?php echo(get_instance()->uri->uri_string()); ?>">
			<fieldset>
				<input type="submit" name="evpub_confirm" value="Publish" />
				<input type="submit" name="evpub_cancel" value="Cancel" />
			</fieldset>
		</form>
	</div>
</div>