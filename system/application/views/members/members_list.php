<?php
/**
 * @file members_list.php
 * @brief Show a list of all the members and allow filtering.
 */

/// Show select options for a team.
function EchoOptionTeams($team, $selected, $head, $depth = 0)
{
	if ($head) {
		echo '<option value="'.$team['id'].'"'.(($team['id'] == $selected)?' SELECTED':'').'>'.
			str_repeat('+ ',$depth).$team['name'].'</option>';
	}
	if (!empty($team['subteams'])) {
		foreach ($team['subteams'] as $subteam) {
			EchoOptionTeams($subteam, $selected, TRUE, $depth + 1);
		}
	}
}
?>

<div class='blue_box'>
	<h2>members</h2>
	<form class="form" method="post" action="<?php echo $target; ?>" name="member_select_form" id="member_select_form">
		<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2">
		<tr style="background-color: #eee">
			<th></th>
			<?php
				function SortHeader($filter_base, $sort_fields, $field, $title)
				{
					echo '<a href="' . vip_url($filter_base . '/sort/'.
						((isset($sort_fields[$field]) && $sort_fields[$field])
							? 'desc' : 'asc').'/'.$field) . '">'.
						$title.
						'</a>';
				}
			?>
			<th><?php SortHeader($filter_base, $sort_fields, 'firstname','Firstname'); ?></th>
			<th><?php SortHeader($filter_base, $sort_fields, 'surname','Surname'); ?></th>
			<th>Email</th>
			<th><?php SortHeader($filter_base, $sort_fields, 'paid','Paid'); ?></th>
			<th>E?</th>
			<th>Conf</th>
			<th><?php SortHeader($filter_base, $sort_fields, 'vip','VIP'); ?></th>
			<th>Edit</th>
		</tr>
		<?php foreach ($members as $membership) {?>
		<tr>
			<td align="center">
				<input type="checkbox" name="members_selected[]"
					value="user<?php echo $membership['user_id']; ?>"
					id="user<?php echo $membership['user_id']; ?>" /></td>
			<td><label for="user<?php echo $membership['user_id']; ?>"><?php echo $membership['firstname']; ?></label></td>
			<td><label for="user<?php echo $membership['user_id']; ?>"><?php echo $membership['surname']; ?></label></td>
			<?php if (NULL !== $membership['email']) { ?>
				<td><a href='mailto:<?php echo $membership['email'];?>'><?php echo $membership['email']; ?></a></td>
			<?php } else {?>
				<td>unavailable</td>
			<?php } ?>
			<td><?php echo $membership['paid']; ?></td>
			<td><?php echo $membership['on_mailing_list']; ?></td>
			<td>0</td>
			<td><?php echo $membership['vip']; ?></td>
			<td><a href='<?php echo vip_url('members/info/'.$membership['user_id']); ?>'>Edit</a></td>
		</tr>
		<?php } ?>
		</table>
		<?php
			/// @TODO Check/uncheck all
		?>
		<a href="#" onclick="if (markAllRows('rowsDeleteForm')) return false;">Check All</a> /
		<a href="#" onclick="if (unMarkAllRows('rowsDeleteForm')) return false;">Uncheck All</a>
		
		<fieldset>
			<input type='submit' class='button' name='members_select_unsubscribe_button' value='Unsubscribe'>
			<input type='submit' class='button' name='members_select_request_cards_button' value='Request business cards'>
		</fieldset>
		
		<fieldset>
			<label for="invite_team">Invite to team:</label>
			<select name="invite_team"><?php EchoOptionTeams($organisation, 0, FALSE, -1); ?></select>
			<input type='submit' class='button' name='members_select_invite_button' value='Invite'>
		</fieldset>
	</form>

</div>