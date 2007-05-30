<div id="RightColumn">
	<h2 class="first">Suggestions</h2>
	<div class="Entry">
		If you have any suggestions about features you would like to see here, please leave them as feedback at the bottom of the page.
	</div>
	<h2 class="first">Members Management</h2>
	<div class="Entry">
		<b>Members management is offline</b>, and we will be confirming new VIPs for your organisation ourselves. If you would like someone from your organisation to become a VIP also, then tell them to go to the account page (accessible from the link at the top of the screen) and request VIP status from there.
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>welcome</h2>
		<p>Welcome to the VIP area. For now you can only edit your directory entry, but more features will become available over time.</p>
	</div>
	<div class="BlueBox">
		<h2>My Tasks</h2>
		<ul>
			<li><a href='<?php echo vip_url('directory/information'); ?>'>Edit the directory entry</a></li>
			<!--<li><a href='<?php echo vip_url('calendar/'); ?>'>Manage events</a></li>-->
			<?php if($enable_members == TRUE){?>
			<!--<li><a href='<?php echo vip_url('members'); ?>'>Manage members</a></li>-->
			<?php }?>
		</ul>
	</div>
</div>
