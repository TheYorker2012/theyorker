<?php

/**
 * @file views/calendar/publish.php
 * @brief View for publishing events.
 *
 * - Warns the user that this is permanent change.
 * - Provides choices (publish all, individual, etc)
 *
 * @param $Event
 */

?>

<div id='RightColumn'>
	<h2>To-Do</h2>
	
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>Publish Event</h2>
		<p>
			Publishing the event cannot be undone.
			The event can be cancelled however it will still be displayed on people's calendars as a cancelled event.
			Moving the event will also leave a remnent at the old time stating that the event has been rescheduled.
		</p>
		<h3><?=$Event->Name?></h3>
		<p><?=$Event->Description?></p>
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
				<?=$occurrence->StartTime->Format(DATE_RFC822)?>
			</li>
			<?php
		}
		?>
		</ul>
		<p>
			Are you sure you wish to publish the event and all its occurrences?
		</p>
		<form method="post" action="<?=get_instance()->uri->uri_string()?>">
			<fieldset>
				<input type="submit" name="evpub_confirm" value="Publish" />
				<input type="submit" name="evpub_cancel" value="Cancel" />
			</fieldset>
		</form>
	</div>
</div>