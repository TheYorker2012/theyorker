<?php

/**
 * @file views/calendar/my_calendar.php
 * @brief Peronsalised calendar main view frame.
 *
 * @see
 *	- http://theyorker.gmghosting.com/calendar My Calendar Main Page
 *	- http://real.theyorker.co.uk/wiki/Functional:My_Calendar Functional Specification
 *
 * @version 20/03/2007 James Hogan (jh559)
 *	- Created
 *
 * @param $Filters array Array of filters, indexed by an id:
 *	- 'name'        => string  Name of the filter.
 *	- 'field'       => string  Occurrence field to filter by.
 *	- 'value'       => string  Occurrence field value to filter by.
 *	- 'selected'    => bool    Whether the filter is selected by default.
 *	- 'description' => string  Description of filter.
 *	- 'display'     => string  Display mode {'block','image'}.
 *	- 'colour'      => string  Hexadecimal colour value (if display == 'block').
 *	- 'selected_image'   => string  Path of image for selected (if display == 'image').
 *	- 'unselected_image' => string  Path of image for unselected (if display == 'image').
 * @param $ViewMode FramesView Main subview to display events with.
 */

?>

<ul>
	<?php
	foreach ($Filters as $id => $filter) {
		switch ($filter['display']) {
			case 'block':
				echo '<li><a href="#">';
				echo $filter['name'];
				echo '</a></li>';
				break;
			case 'image':
				echo '<img src="'.$filter['selected_image'].'" alt="'.$filter['name'].'"/>';
				break;
		}
	}
	?>
</ul>

<div>
<?php
// Load the main view
$ViewMode->Load();
?>
</div>