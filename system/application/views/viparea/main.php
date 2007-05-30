<div id="RightColumn">
	<h2 class="first">Suggestions</h2>
	<div class="Entry">
		<h3>Advertising</h3>
		<p>information goes here.</p>
	</div>

	<h2>Reminders</h2>
	<div class="Entry">
		<p>information goes here.</p>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>welcome</h2>
		<?php echo($main_text); ?>
	</div>
	<div class="BlueBox">
		<h2>My Tasks</h2>
		<ul>
			<li><a href='<?php echo vip_url('directory/information'); ?>'>Edit the directory entry</a></li>
			<li><a href='<?php echo vip_url('calendar/'); ?>'>Manage events</a></li>
			<?php if($enable_members == TRUE){?>
			<li><a href='<?php echo vip_url('members'); ?>'>Manage members</a></li>
			<?php }?>
		</ul>
	</div>
</div>
