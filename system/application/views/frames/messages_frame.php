<?php
// Display each message
// Each message will probably have more parameters (e.g. message type).
foreach ($messages as $message) {
	// Display the message
	// This is a bodge, it needs putting into a nice box with a coloured
	// background (depending on the type of message).
?>
	<div><?php echo $message['text']; ?></div>
<?php
}

// Load the rest of the page
$content[0]->Load();
?>