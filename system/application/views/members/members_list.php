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

/// Draw a column heading sorting hyperlink.
function SortLink($filter, $sort_fields, $field, $title)
{
	echo '<a href="' . vip_url($filter['base'] . '/sort/'.
		((isset($sort_fields[$field]) && $sort_fields[$field])
			? 'desc' : 'asc').'/'.$field) . '">';
	echo $title;
	if ($filter['last_sort'] === $field) {
		if (isset($sort_fields[$field]) && $sort_fields[$field]) {
			echo '<IMG SRC="/images/prototype/members/sortasc.png" ALT="sorted ascending" />';
		} else {
			echo '<IMG SRC="/images/prototype/members/sortdesc.png" ALT="sorted descending" />';
		}
	}
	echo '</a>';
}

/// Draw links to filter a boolean field (yes/no).
function BoolFilterLinks($filter, $field)
{
	?>
	<A HREF="<?php echo vip_url($filter['base'].'/'.$field); ?>"><IMG SRC="/images/prototype/members/yes9.png" ALT="Filter yes's" /></A>
	<A HREF="<?php echo vip_url($filter['base'].'/not/'.$field); ?>"><IMG SRC="/images/prototype/members/no9.png" ALT="Filter no's" /></A>
	<?php
}

/// Individual filter link for data in cells of table.
function FilterLinkBool($filter, $field, $value)
{
	echo '<a href="' . vip_url($filter['base'] .
		((!$value)?'/not/':'/') . $field) . '">';
	if ($value) {
		echo '<IMG SRC="/images/prototype/members/yes.png" ALT="Yes" />';
	} else {
		echo '<IMG SRC="/images/prototype/members/no.png" ALT="No" />';
	}
	echo '</a>';
}

?>

<div class='blue_box'>
	<h2>members</h2>
	<form class="form" method="post" action="<?php echo $target; ?>" name="member_select_form" id="member_select_form">
		
		<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2">
		<tr style="background-color: #eee">
			<th></th>
			<th><?php SortLink($filter, $sort_fields, 'firstname','Firstname'); ?></th>
			<th><?php SortLink($filter, $sort_fields, 'surname','Surname'); ?></th>
			<th>Email <?php BoolFilterLinks($filter, 'mailable'); ?></th>
			<th><?php SortLink($filter, $sort_fields, 'confirmed','Conf'); ?><br />
				<?php BoolFilterLinks($filter, 'confirmed'); ?></th><?php /*
			<th><?php SortLink($filter, $sort_fields, 'mailable','E?', TRUE); ?></th> */ ?>
			<th><?php SortLink($filter, $sort_fields, 'paid','Paid'); ?><br />
				<?php BoolFilterLinks($filter, 'paid'); ?></th>
			<th><?php SortLink($filter, $sort_fields, 'vip','VIP'); ?><br />
				<?php BoolFilterLinks($filter, 'vip'); ?></th>
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
				<td><IMG SRC="/images/prototype/members/nomail.png" ALT="not available" /></td>
			<?php } ?>
			<td><?php FilterLinkBool($filter, 'confirmed', $membership['confirmed']); ?></td><?php /*
			<td><?php FilterLinkBool($filter, 'mailable', $membership['on_mailing_list']); ?></td>*/ ?>
			<td><?php FilterLinkBool($filter, 'paid', $membership['paid']); ?></td>
			<td><?php FilterLinkBool($filter, 'vip', $membership['vip']); ?></td>
			<td><a href='<?php echo vip_url('members/info/'.$membership['user_id']); ?>'>Edit</a></td>
		</tr>
		<?php } ?>
		</table>
		<?php
			/// @TODO Check/uncheck all
		?>
		<a href="#" onclick="if (markAllRows('rowsDeleteForm')) return false;">check all</a> /
		<a href="#" onclick="if (unMarkAllRows('rowsDeleteForm')) return false;">uncheck all</a>
		
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