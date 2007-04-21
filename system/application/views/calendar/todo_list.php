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
 * @param $Items array[CalendarOccurrence] Array of to-do list items
 * @param $InlineAdder bool Whether to have an inline todo adder.
 * @param $InlineAdderTarget string
 *
 */

if ($InlineAdder) {
	?>
	<div class="BlueBox">
		<form method="post" action="<?php echo $InlineAdderTarget; ?>">
			<input name="todo_name" />
			<input name="todo_submit" type="submit" value="Add"/>
		</form>
	</div>
	<?php
}
?>
<div class="BlueBox">
	<ul>
		<?php
		foreach ($Items as $item) {
			echo '<li>'.$item->Event->Name.'</li>';
		}
		?>
	</ul>
</div>