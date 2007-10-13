<?php
/**
 * @file views/calendar/index.php
 * @brief Personal calendar index page.
 * @param $Notifications array
 * @param $Paths Paths object.
 */

?>
<div id="RightColumn">
	<h2 class="first">Notifications</h2>
	<div>
		<div id="calnot_none" style="display:<?php echo(
			(isset($Notifications) && !empty($NotNotifications))
				? 'block'
				: 'none');
			?>">
			<em>You have no outstanding calendar notifications.</em>
		</div>
		<?php
		if (isset($Notifications)) {
			// Display each notification
			foreach ($Notifications as $key => $notification) {
				if (NULL !== $notification) {
					// Display the notification
					$notification->Load();
				}
			}
		}
		?>
	</div>
	<?php if (1) { ?>
	<h2>Actions</h2>
	<div>
		<ul>
			<li><a href="<?php echo(site_url($Paths->EventCreateRaw(0))); ?>">Create a new event</a></li>
			<?php /*
			<li><a>Create a birthday/anniversary</a></li>
			*/ ?>
		</ul>
	</div>
	
	<h2>Advanced</h2>
	<div>
		<h3>Imported calendars</h3>
		<p>You can import external calendars into your Yorker personal calendar:</p>
		<dl><?php /*
			<dt><a>Web Accessible iCalendar</a></dt>
			<dd>iCalendar is a popular calendar standard. Use the following
				wizards for importing calendars from specific websites.
				<ul>
					<li><a>Facebook</a></li>
					<li><a>Google Calendar</a></li>
				</ul>
			</dd>*/ ?>
			<dt><a>Facebook</a></dt>
			<dd>With your permission The Yorker calendar can display your
				facebook events and friends birthdays while you are logged in to
				facebook.<?php /* Please also consider
				using Facebook's iCalendar export which is more convenient as
				you do not have to be logged in to facebook.*/ ?></dd>
		</dl><?php /*
		<h3>Export calendar</h3>
		<p>The Yorker aims to be interporable with other calendar tools and can
			export your calendar (including personal events and those from
			organisations you have subscribed to) in iCalendar format.</p>
		<ul><li><a>Set up iCalendar export.</a></li></ul>*/ ?>
	</div>
	
	<?php } ?>
</div>

<div id="MainColumn">
<h2>Welcome to your Personal Calendar</h2>
<p>stuff to go on this page</p>

<ul>
	<li>brief introduction</li>
	<li>calendar preview</li>
	<li>tools at side e.g. for setup</li>
</ul>
</div>