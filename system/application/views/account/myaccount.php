<?php
function _render_bool_image($bool) {
	if ($bool) {
		return '<img src="/images/prototype/prefs/success.gif" alt="Yes" title="Yes" />';
	} else {
		return '<img src="/images/prototype/news/delete.gif" alt="No" title="No" />';
	}
}

function _render_vip_image($status, $org_id) {
	if ($status == 'approved') {
		return '<img src="/images/prototype/news/accepted.gif" alt="You are VIP" title="You are VIP" />';
	} elseif ($status == 'requested') {
		return '<img src="/images/prototype/news/requested.gif" alt="You have applied for VIP" title="You have applied for VIP" />';
	} else {
		return '<a href="/account/vip/' . $org_id . '"><img src="/images/prototype/prefs/apply.gif" alt="Apply to be VIP" title="Apply to be VIP" /></a>';
	}
}
?>

<div id="RightColumn">
	<h2 class="first">Edit Subscriptions</h2>
	<ul>
	<li><a href="/register/departments">Academic Subscriptions</a></li>
	<li><a href="/register/societies">Society Subscriptions</a></li>
	<li><a href="/register/athletic_union">AU Subscriptions</a></li>
	<li><a href="/register/venues">Venue Subscriptions</a></li>
	<li><a href="/register/college_campus">College &amp; Campus Subscriptions</a></li>
	<li><a href="/register/organisations">Organisation Subscriptions</a></li>
	</ul>

	<h2><?php echo ($vip_help_heading); ?></h2>
	<div class="Entry">
		<?php echo ($vip_help_text); ?>
	</div>
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
				        <th style="text-align:center; width:15%;">VIP</th>
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
						<td style="text-align:center;"><?php echo _render_vip_image($subscription['vip_status'], $subscription['org_id']); ?></td>
					</tr>
					<?php
					}
					?>
			    </tbody>
			</table>
		</div>
	</div>
</div>
