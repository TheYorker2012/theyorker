<?php
/**
 * @file views/calendar/index.php
 * @brief Personal calendar index page.
 *
 * @param $IntroHtml Introductory html.
 * @param $RighbarHtml Html for the righthand bar.
 * @param $Paths Paths object.
 * @param $Notifications array
 */

$first_h2 = ' class="first"';
?>
<div id="RightColumn">
	<?php if (isset($Notifications) && NULL !== $Notifications) { ?>
	<h2<?php echo($first_h2); $first_h2=''; ?>>Notifications</h2>
		<div>
			<div id="calnot_none" style="display:<?php echo(
				(!isset($Notifications) || empty($Notifications))
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
	<?php } ?>
	<?php if (1) { ?>
	<h2<?php echo($first_h2); $first_h2=''; ?>>Actions</h2>
	<div>
		<ul>
			<li><a href="<?php
				echo(site_url($Paths->EventCreateRaw(0)).
					get_instance()->uri->uri_string());
				?>">Create a new event</a></li>
			<li><a href="<?php
				echo(site_url($Paths->Range('today:3days')));
				?>">Display the next few days</a></li>
			<li><a href="<?php
				echo(site_url($Paths->Range('thisterm-thisweek')));
				?>">Display this week</a></li>
			<li><a href="<?php
				echo(site_url($Paths->Range('thisterm')));
				?>">Display this term</a></li>
			<?php /*
			<li><a>Create a birthday/anniversary</a></li>
			*/ ?>
		</ul>
	</div>
	<?php if (NULL !== $RightbarHtml) {
		echo($RightbarHtml);
	} ?>
	<?php } ?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<?php if (NULL !== $IntroHtml) {
		echo($IntroHtml);
		} ?>
	</div>
</div>