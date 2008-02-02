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
		<form method="post" action="<?php echo $InlineAdderTarget; ?>">
				<table border="0">
				<tr>
				<td>
				<input name="todo_name" />
				</td><td>
				<input name="todo_submit" type="submit" value="Add"/>
				</td>
				</tr>
				</table>
		</form>
	<?php
}
?>
<div class="TodoBox">
	<ul id="todolist">
		<?php
		foreach ($Items as $item) {
			echo '<li style="cursor: move;">'.xml_escape($item->Event->Name).'</li>';
		}
		?>
	</ul>
</div>

<script type="text/javascript">
// <![CDATA[
	Sortable.create("todolist", {dropOnEmpty:true,containment:["todolist"],constraint:false});
// ]]>
</script>
