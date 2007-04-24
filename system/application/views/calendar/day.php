<?php

/**
 * @param $DateDescription string     .
 * @param $DaysView        FramesDay  .
 * @param $TodoView        FramesView .
 * @param $ReadOnly        bool
 * @param $Paths           array[string => string]
 *	- 'add' string Path to event adder
 */

/* This is a date description that just says "Today probably!"?
 * <h3><?php echo $DateDescription; ?></h3>
 */

?>

<div id='RightColumn'>
	<h2 style="margin-left: 0.4em;">To-Do</h2>
	<?php $TodoView->Load(); ?>
	
	<?php if (!$ReadOnly) { ?>
		<h2 style="margin-left: 0.4em;">Actions</h2>
		<div class="TodoBox">
			<ul>
				<li><a href="<?=$Path['add']?>">Add an event</a></li>
			</ul>
		</div>
	<?php } ?>
</div>

<div id="MainColumn">
	<?php $DaysView->Load(); ?>
</div>
