<fb:fbml version="1.0">
	<div id="TopBar" align="center">
		<a href="http://www.theyorker.co.uk">
			<img align="left" src="<?php echo(site_url('images/prototype/homepage/information.png')); ?>" />
		</a>
		<b><a href="http://www.theyorker.co.uk"><font color="#FF6900">www.theyorker.co.uk - make it your home</font></a></b> - [
		<a href="http://www.theyorker.co.uk/news">daily news</a> |
		<a href="http://www.theyorker.co.uk/news/features">features</a> |
		<a href="http://www.theyorker.co.uk/calendar">personal calendar</a> |
		<a href="http://www.theyorker.co.uk/directory">directory</a> ]
		<a href="http://www.theyorker.co.uk">
			<img align="right" src="<?php echo(site_url('images/prototype/homepage/information.png')); ?>" />
		</a>
	</div>
	<div id="PairMessage" align="center">
		You need to <a href="<?php echo site_url('pair'); ?>">Pair Accounts</a> in order to see personalised content.
	</div>
	<div id="Title" align="center">
		<?php
			if(isset($body_title)) {
				echo('<h1 id="PageTitle">'.$body_title.'</h1>');
			}
		?>
	</div>
	<div id="Messages">
		<?php
			// Display each message
			foreach ($messages as $message) {
				// Display the message
				$message->Load();
			}
		?>
	</div>
	<div id="MainContent">
		<?php
			$content[0]->Load();
		?>
	</div>
	<div id="Copyright" align="center">
		<small>
			Copyright 2007 The Yorker. Weather data provided by Yahoo. <a href="<?php echo site_url('policy'); ?>">Privacy Policy</a>
		</small>
	</div>
</fb:fbml>
