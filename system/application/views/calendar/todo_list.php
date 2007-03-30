<?php

/**
 * @file views/calendar/todo_list.php
 * @brief To-do list subview.
 *
 * Two lists of to-do items.
 *
 * @see
 *	http://real.theyorker.co.uk/wiki/Functional:Calendar_view_todo Functional Specification
 *
 * @version 20/03/2007 James Hogan (jh559)
 *	- Created
 *
 * @param $Items array Array of to-do list items
 *
 */

?>

<ul>
<?php
foreach ($Events as $event) {
	echo '<li>'.$event->Name.'</li>';
}
?>
</ul>
