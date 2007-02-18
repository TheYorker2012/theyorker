<div class='RightToolbar'>
<h4>Suggestions</h4>
<h5>Advertising</h5>
<p>
information goes here.
</p>
<h4>Reminders</h4>
information goes here.
</div>
<div class='grey_box'>
	<h2>welcome</h2>
	<p>
		<?php echo $main_text; ?>
	</p>
</div>
<div class='blue_box'>
	<h2>My Tasks</h2>
	<ul>
		<li><a href='<?php echo vip_url('directory/information'); ?>'>Edit the directory entry</a></li>
		<li><a href='<?php echo vip_url('calendar/'); ?>'>Manage events</a></li>
		<?php if($enable_members == TRUE){?>
		<li><a href='<?php echo vip_url('members/view'); ?>'>Manage members</a></li>
		<?php }?>
		<li><a href='<?php echo vip_url('advertising'); ?>'>Advertising</a></li>
		<li><a href='<?php echo vip_url('account/update'); ?>'>My Viparea Account</a></li>
	</ul>
</div>