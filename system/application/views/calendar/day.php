<?php

/**
 * @param $DateDescription	string     .
 * @param $DaysView			FramesDay  .
 * @param $TodoView			FramesView .
 * @param $CreateSources	array[CalendarSource]
 * @param $Paths			CalendarPaths
 */

/* This is a date description that just says "Today probably!"?
 * <h3><?php echo $DateDescription; ?></h3>
 */

?>

<div id='RightColumn'>
	<h2 style="margin-left: 0.4em;">To-Do List</h2>
	<?php $TodoView->Load(); ?>
	
	<?php if (!empty($CreateSources)) { ?>
		<h2 style="margin-left: 0.4em;">Actions</h2>
		<div class="TodoBox">
			<ul>
				<?php foreach ($CreateSources as $source) { ?>
					<li><a href="<?php echo(site_url($Path->EventCreate($source))); ?>">Add event to <?php echo($source->GetSourceName()); ?> Calendar</a></li>
				<?php } ?>
			</ul>
		</div>
	<?php } ?>
</div>

<div id="MainColumn">
	<?php $DaysView->Load(); ?>
</div>
