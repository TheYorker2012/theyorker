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
			echo '<img src="/images/prototype/members/sortasc.png" alt="sorted ascending" />';
		} else {
			echo '<Img src="/images/prototype/members/sortdesc.png" alt="sorted descending" />';
		}
	}
	echo '</a>';
}

/// Draw links to filter a boolean field (yes/no).
function BoolFilterLinks($filter, $field)
{
	?>
	<a href="<?php echo vip_url($filter['base'].'/'.$field); ?>"><img src="/images/prototype/members/yes9.png" alt="Filter yes's" /></a>
	<a href="<?php echo vip_url($filter['base'].'/not/'.$field); ?>"><img src="/images/prototype/members/no9.png" alt="Filter no's" /></a>
	<?php
}

/// Individual filter link for data in cells of table.
function FilterLinkBool($filter, $field, $value)
{
	echo '<a href="' . vip_url($filter['base'] .
		((!$value)?'/not/':'/') . $field) . '">';
	if ($value) {
		echo '<img src="/images/prototype/members/yes.png" alt="Yes" />';
	} else {
		echo '<img src="/images/prototype/members/no.png" alt="No" />';
	}
	echo '</a>';
}

?>

<div class="BlueBox">
	<h2>Members of <?php echo $organisation['name']; ?></h2>


	<p>The following action can be performed on the selected members</p>
	<select style="float: none;">
		<!--optgroup label="Actions:"-->
			<option selected="selected">Send e-mail</option>
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
	<br />
	<form class="form" method="post" action="<?php echo $target; ?>" id="member_select_form">

		<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2">
		<tr style="background-color: #eee">
			<th align="center">
				<input type="checkbox" name="members_selected[]"
					value="userSelectAllNone"
					id="userSelectAllNone" /></th>
			<th><?php SortLink($filter, $sort_fields, 'firstname','Firstname'); ?></th>
			<th><?php SortLink($filter, $sort_fields, 'surname','Surname'); ?></th>
			<th>Email</th>
			<th align="center"><?php SortLink($filter, $sort_fields, 'confirmed','Conf'); ?></th><?php /*
			<th><?php SortLink($filter, $sort_fields, 'mailable','E?', TRUE); ?></th> */ ?>
			<th align="center"><?php SortLink($filter, $sort_fields, 'paid','Paid'); ?></th>
			<?php if ('manage' !== VipMode()) { ?>
				<th align="center"><?php SortLink($filter, $sort_fields, 'vip','VIP'); ?></th>
			<?php } ?>
			<th>Card</th>
			<?php if ('manage' === VipMode()) { ?>
				<th>Byline</th>
				<th>Access</th>
			<?php } ?>
		</tr>
		<?php

		foreach ($members as $membership) {
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

			<td align="center">
			<?php if ($membership['user_confirmed'] && $membership['org_confirmed']) { ?>
				<img src="/images/prototype/members/confirmed.png" alt="Confirmed Member" title="Confirmed Member" />
			<?php } elseif ($membership['user_confirmed']) { ?>
				<img src="/images/prototype/members/user_confirmed.png" alt="Waiting for your approval" title="Waiting for your approval" />
			<?php } elseif ($membership['org_confirmed']) { ?>
				<img src="/images/prototype/members/org_confirmed.png" alt="Invitation sent, awaiting reply" title="Invitation sent, awaiting reply" />
			<?php } else { ?>
				&nbsp;
			<?php } ?>

			</td><?php /*
			<td><?php if (isset($membership['on_mailing_list'])) FilterLinkBool($filter, 'mailable', $membership['on_mailing_list']); ?></td>*/ ?>
			<td align="center"><?php if (isset($membership['paid']) && $membership['paid']) { ?>
				<img src="/images/prototype/members/paid.png" alt="Yes" />
			<?php } ?></td>
			<?php if ('manage' !== VipMode()) { ?>
				<td align="center"><?php if (isset($membership['vip']) && $membership['vip']) { ?>
					<img src="/images/prototype/members/vip.png" alt="Yes" />
				<?php } ?></td>
			<?php } ?>
			<td align="center">
			<?php if ($membership['has_business_card']) { ?>
				<?php if ($membership['business_card_needs_approval']) { ?>
					<img src="/images/prototype/members/card_awaiting_approval.png" alt="Awaiting Approval" title="Awaiting Approval" />
				<?php } elseif ($membership['business_card_expired']) { ?>
					<img src="/images/prototype/members/card_expired.png" alt="Expired" title="Expired" />
				<?php } else { ?>
					<img src="/images/prototype/members/card_active.png" alt="Has Business Card" title="Has Business Card" />
				<?php } ?>
			<?php } else { ?>
				&nbsp;
			<?php } ?>
			</td>
			<?php if ('manage' === VipMode()) { ?>
			<td align="center">
			<?php if ($membership['has_byline']) { ?>
				<?php if ($membership['byline_needs_approval']) { ?>
					<img src="/images/prototype/members/byline_awaiting_approval.png" alt="Awaiting Approval" title="Awaiting Approval" />
				<?php } elseif ($membership['byline_expired']) { ?>
					<img src="/images/prototype/members/byline_expired.png" alt="Byline Expired" title="Byline Expired" />
				<?php } else { ?>
					<img src="/images/prototype/members/byline_active.png" alt="Byline OK" title="Byline OK" />
				<?php } ?>
			<?php } else { ?>
				&nbsp;
			<?php } ?>
			</td>
			<td align="center">
				<?php if ($membership['office_editor_access']) { ?>
					<img src="/images/prototype/members/access_editor.gif" alt="Editor Access" title="Editor Access" />
				<?php } elseif ($membership['office_writer_access']) { ?>
					<img src="/images/prototype/members/access_writer.gif" alt="Writer Access" title="Writer Access" />
				<?php }
			} ?>
			</td>
		</tr>
		<?php } ?>
		</table>
		<?php /*<a href="#" onclick="if (markAllRows('rowsDeleteForm')) return false;">check all</a> /
		<a href="#" onclick="if (unMarkAllRows('rowsDeleteForm')) return false;">uncheck all</a>*/ ?>
	</form>

</div>
