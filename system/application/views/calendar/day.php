<?php

/**
 * @param $DateDescription string     .
 * @param $DaysView        FramesDay  .
 * @param $TodoView        FramesView .
 */

?>

<h3><?php echo $DateDescription; ?></h3>

<div id='RightColumn'>
	<h2>To-Do</h2>
	<?php $TodoView->Load(); ?>
</div>

<div id="MainColumn">
	<?php $DaysView->Load(); ?>
</div>