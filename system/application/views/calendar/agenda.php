<?php

/**
 * @param $MiniMode bool
 * @param $Events
 * @param $Occurrences
 */

if (!empty($Occurrences)) {
	foreach ($Occurrences as $occurrence) {
		echo('<div class="BlueBox">');
		echo($occurrence->StartTime->Format('H:i').' - '.$occurrence->Event->Name);
		echo('</div>');
	}
} else {
	?>
	You have no events today.
	<?php
}

?>