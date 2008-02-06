<div id="RightColumn">
	<h2 class="first"><?php echo($whats_this_heading); ?></h2>
	<?php echo($whats_this_text); ?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>byline teams</h2>

		<table style="width:80%">
			<tr>
				<th>Team Name</th>
				<th>Byline Count</th>
				<th>Order</th>
				<th>Ops</th>
			</tr>
<?php foreach ($teams as $team) { ?>
			<tr>
				<td>
					<a href="/office/bylines/view_team/<?php echo($team['business_card_group_id']); ?>/">
						<?php echo(xml_escape($team['business_card_group_name'])); ?>
					</a>
				</td>
				<td><?php echo($team['business_card_group_count']); ?></td>
				<td style="text-align:center">
					<a href="/office/bylines/order_team/<?php echo($team['business_card_group_id']); ?>/up/">
						<img src="/images/prototype/members/sortdesc.png" alt="Move Up" title="Move Up" />
					</a>
					<a href="/office/bylines/order_team/<?php echo($team['business_card_group_id']); ?>/down/">
						<img src="/images/prototype/members/sortasc.png" alt="Move Down" title="Move Down" />
					</a>
				</td>
				<td style="text-align:center">
					<a href="/office/bylines/view_team/<?php echo($team['business_card_group_id']); ?>/">
						<img src="/images/prototype/news/edit.png" alt="Edit" title="Edit" />
					</a>
					<a href="/office/bylines/delete_team/<?php echo($team['business_card_group_id']); ?>/">
						<img src="/images/prototype/news/delete.png" alt="Delete" title="Delete" />
					</a>
				</td>
			</tr>
<?php } ?>
		</table>
	</div>

	<div class="BlueBox">
		<h2>new byline team</h2>
		
		<form action="/office/bylines/teams/" method="post">
			<fieldset>
				<label for="team_name">Team Name:</label>
				<input type="text" name="team_name" id="team_name" value="" />
				<input type="submit" name="add_team" id="add_team" value="Create" class="button" />
			</fieldset>
		</form>
	</div>
</div>