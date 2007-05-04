<?php
/**
 * @file members_list.php
 * @brief Show a list of all the members and allow filtering.
 */

/// @todo all emails should be displayed

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
	<h2>Members of <?php echo $organisation['name']; ?></h2>


	<P>The following action can be performed on the selected members</P>
	<P>
		<select style="float: none;">
			<!--optgroup label="Actions:"-->
				<option selected>Send e-mail</option>
				<option>Remove membership</option>
				<option>Set as paid</option>
				<option>Set as not paid</option>
				<option>Request business cards</option>
				<option>Expire business cards</option>
			<!--/optgroup-->
			<?php
				if (!empty($organisation['subteams'])) {
					echo '<optgroup label="Invite:">';
					EchoTeamFilterOptions($organisation, 'Invite to ', FALSE);
					echo '</optgroup>';
				}
			?>
		</select>
		<input type="button" value="Go" style="float: none;"/>
	</P>
	<br />
	<form class="form" method="post" action="<?php echo $target; ?>" name="member_select_form" id="member_select_form">

		<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2">
		<tr style="background-color: #eee">
			<th align="center">
				<input type="checkbox" name="members_selected[]"
					value="userSelectAllNone"
					id="userSelectAllNone" /></th>
			<th><?php SortLink($filter, $sort_fields, 'firstname','Firstname'); ?></th>
			<th><?php SortLink($filter, $sort_fields, 'surname','Surname'); ?></th>
			<th>Email</th>
			<th align="center"><?php SortLink($filter, $sort_fields, 'confirmed','Conf'); ?><?php /*
			<th><?php SortLink($filter, $sort_fields, 'mailable','E?', TRUE); ?></th> */ ?>
			<th align="center"><?php SortLink($filter, $sort_fields, 'paid','Paid'); ?></th>
			<th align="center"><?php SortLink($filter, $sort_fields, 'vip','VIP'); ?></th>
			<th>Card</th>
		</tr>
		<?php
		$i = -1;
		foreach ($members as $membership) {
		$i = ($i+1)%4;
		?>
		<tr>
			<td align="center">
				<input type="checkbox" name="members_selected[]"
					value="user<?php echo $membership['user_id']; ?>"
					id="user<?php echo $membership['user_id']; ?>" /></td>
			<td><a href='<?php echo vip_url('members/info/'.$membership['user_id']); ?>'><?php echo $membership['firstname']; ?></a></td>
			<td><a href='<?php echo vip_url('members/info/'.$membership['user_id']); ?>'><?php echo $membership['surname']; ?></a></td>
			<?php /** @todo email should show username */ ?>

			<td><?php if (NULL !== $membership['email']) { ?>
				<a href='mailto:<?php echo $membership['email'];?>'><?php echo $membership['username']; ?></a>
			<?php } else {?>
				<?php echo $membership['username']; ?>
			<?php } ?></td>

			<td align="center"><?php if (isset($membership['confirmed']) && $membership['confirmed']) { ?>
				<IMG SRC="/images/prototype/members/confirmed.png" ALT="Yes" />
			<?php } ?></td><?php /*
			<td><?php if (isset($membership['on_mailing_list'])) FilterLinkBool($filter, 'mailable', $membership['on_mailing_list']); ?></td>*/ ?>
			<td align="center"><?php if (isset($membership['paid']) && $membership['paid']) { ?>
				<IMG SRC="/images/prototype/members/paid.png" ALT="Yes" />
			<?php } ?></td>
			<td align="center"><?php if (isset($membership['vip']) && $membership['vip']) { ?>
				<IMG SRC="/images/prototype/members/vip.png" ALT="Yes" />
			<?php } ?></td>
			<td align="center">
			<?php if ($i == 1) { ?>
				<IMG SRC="/images/prototype/members/card_active.png" ALT="Active" />
			<?php } elseif ($i == 2) { ?>
				<IMG SRC="/images/prototype/members/card_expired.png" ALT="Expired" />
			<?php } elseif ($i == 3) { ?>
				<IMG SRC="/images/prototype/members/card_inactive.png" ALT="Inactive" />
			<?php } ?>
			</td>
		</tr>
		<?php } ?>
		</table>
		<?php /*<a href="#" onclick="if (markAllRows('rowsDeleteForm')) return false;">check all</a> /
		<a href="#" onclick="if (unMarkAllRows('rowsDeleteForm')) return false;">uncheck all</a>*/ ?>
	</form>

</div>