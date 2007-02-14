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
		<li><a href='/viparea/directory/<?php echo $organisation; ?>/information'>Edit the directory entry</a></li>
		<li><a href='/viparea/calendar/'>Manage events</a></li>
		<?php if($enable_members == TRUE){?>
		<li><a href='/viparea/members/view/<?php echo $organisation; ?>'>Manage members</a></li>
		<?php }?>
		<li><a href='/viparea/advertising/<?php echo $organisation; ?>/'>Advertising</a></li>
		<li><a href='/viparea/account/update/<?php echo $organisation; ?>/'>My Account</a></li>
	</ul>
</div>