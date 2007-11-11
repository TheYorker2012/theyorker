<?php
function _render_bool_image($bool) {
	if ($bool) {
		return '<img src="/images/prototype/prefs/success.gif" alt="Yes" title="Yes" />';
	} else {
		return '<img src="/images/prototype/news/delete.gif" alt="No" title="No" />';
	}
}
?>

<div id="RightColumn">
	<h2 class="first">Edit Subscriptions</h2>
	<ul>
	<li><a href="/register/academic">Academic Subscriptions</a></li>
	<li><a href="/register/societies">Society Subscriptions</a></li>
	<li><a href="/register/au">AU Subscriptions</a></li>
	<li><a href="/register/venue">Venue Subscriptions</a></li>
	</ul>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>My Subscriptions</h2>
		<div>
			<table style="width:100%;">
			    <thead>
			        <tr>
				        <th>Organisation</th>
				        <th>Type</th>
				        <th style="text-align:center; width:15%;">Paid</th>
				        <th style="text-align:center; width:15%;">Calendar / To Do</th>
				        <th style="text-align:center; width:15%;">E-Mail</th>
	    		    </tr>
			    </thead>
	            <tbody>
					<?php
					foreach ($Subscriptions as $subscription) {
					?>
					<tr>
						<td><?php echo($subscription['organisation_name']); ?></td>
						<td><?php echo($subscription['organisation_type_name']); ?></td>
						<td style="text-align:center;"><?php echo _render_bool_image($subscription['subscription_paid']); ?></td>
						<td style="text-align:center;"><?php echo _render_bool_image($subscription['subscription_calendar']); ?> <?php echo _render_bool_image($subscription['subscription_todo']); ?></td>
						<td style="text-align:center;"><?php echo _render_bool_image($subscription['subscription_email']); ?></td>
					</tr>					
					<?php
					}
					?>
			    </tbody>
			</table>
		</div>
	</div>
</div>
