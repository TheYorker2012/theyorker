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
	echo('<a href="' . vip_url($filter['base'] . '/'.
		((isset($sort_fields[$field]) && $sort_fields[$field])
			? 'desc' : 'asc').'/'.$field) . '">');
	echo($title);
	//if ($filter['last_sort'] === $field) {
		if (isset($sort_fields[$field]) && $sort_fields[$field]) {
			echo('<img src="/images/prototype/members/sortasc.png" alt="sorted ascending" />');
		}
		elseif (isset($sort_fields[$field]) && !$sort_fields[$field]) {
			echo('<Img src="/images/prototype/members/sortdesc.png" alt="sorted descending" />');
		}
	//}
	echo('</a>'."\n");
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

/// Draw a branch of the tree of teams
function EchoTeamFilterOptions($team, $prefix = '', $path = '', $indentation = 0)
{
	foreach ($team['subteams'] as $subteam) {
		echo('<option name="team_'.$subteam['id'].'">'."\n");
		//echo str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;',$indentation);
		echo($prefix.$path.$subteam['name']."\n");
		echo('</option>'."\n");
		if (!empty($subteam['subteams'])) {
			EchoTeamFilterOptions($subteam, $prefix, $path.$subteam['name'].'/', $indentation+1);
		}
	}
}

?>

<div class="BlueBox">
<?php
	echo('	<h2>Members of '.$organisation['name'].'</h2>'."\n");
	echo('	<form id="member_select_form" action="'.$target.'" method="post" class="form">'."\n");
?>
		<p>The following action can be performed on the selected members</p>
		<select style="float: none;" name="a_selected_member_action" id="a_select_member_action">
			<!--optgroup label="Actions:"-->
				<option value="send_email" selected="selected">Send e-mail</option>
				<option value="accept_join_request">Accept join request</option>
				<option value="withdraw_invitation">Withdraw invitation</option>
				<option value="remove_membership">Remove membership</option>
				<option value="set_as_paid">Set as paid</option>
				<option value="set_as_not_paid">Set as not paid</option>
<?php if ('manage' !== VipMode()) { ?>
				<option value="give_vip_access">Give VIP access</option>
				<option value="remove_vip_access">Remove VIP access</option>
<?php } else { ?>
				<option value="demote_to_writer">Demote to writer</option>
				<option value="remove_offive_access">Remove office access</option>
<?php } ?>
			<!--/optgroup-->
			<?php
				if (!empty($organisation['subteams'])) {
					echo '<optgroup label="Invite:">';
					EchoTeamFilterOptions($organisation, 'Invite to ', FALSE);
					echo '</optgroup>';
				}
			?>
		</select>
		<input type="submit" value="Go" style="float: none;" name="r_submit_member_action" id="r_submit_member_action" />
		<br />
		<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2">
			<thead>
				<tr style="background-color: #eee">
					<th align="center">
						<input type="checkbox" id="UserSelectAllNone" onclick="checkVisibleRows()" />
					</th>
					<th>
						<?php SortLink($filter, $sort_fields, 'firstname','Firstname'); ?>
					</th>
					<th>
						<?php SortLink($filter, $sort_fields, 'surname','Surname'); ?>
					</th>
					<th>
						<?php SortLink($filter, $sort_fields, 'email','Email'); ?>
					</th>
					<th align="center">
						Conf
					</th>
					<th align="center">
						Paid
					</th>
					<th align="center">
						Card
					</th>
<?php if ('manage' !== VipMode()) { ?>
					<th align="center">
						VIP
					</th>
<?php } ?>
<?php if ('manage' === VipMode()) { ?>
					<th>
						Byline
					</th>
					<th>
						Access
					</th>
<?php } ?>
				</tr>
			</thead>
			<tbody id="MemberTable">
				<tr id="NotFound" style="display: none;">
<?php
	if ('manage' !== VipMode()) {
		echo('					<td colspan="8" style="text-align: center;">'."\n");
	}
	else {
		echo('					<td colspan="10" style="text-align: center;">'."\n");
	}					
?>
						No Matching Entries
					</td>
				</tr>
<?php
	if ('manage' !== VipMode()) {
		echo('				<tr id="VIP" style="display: none;">'."\n");
		echo('				</tr>'."\n");
	}
	else {
		echo('				<tr id="Office" style="display: none;">'."\n");
		echo('				</tr>'."\n");
	}
?>
<?php
	foreach ($members as $membership) {
?>
<?php 
		echo('				<tr id="userid'.$membership['user_id'].'">'."\n");
?>
					<td align="center">
<?php
		echo('						<input type="checkbox" name="a_user_cb['.$membership['user_id'].']" id="a_user_cb['.$membership['user_id'].']" />'."\n");
?>
					</td>
					<td>
<?php 
		echo('						<a href="'.vip_url('members/info/'.$membership['user_id']).'">'.$membership['firstname'].'</a>'."\n");
?>
					</td>
					<td>
<?php 
		echo('						<a href="'.vip_url('members/info/'.$membership['user_id']).'">'.$membership['surname'].'</a>'."\n");
?>
					</td>
					<td>
<?php 
		if (NULL !== $membership['email']) { 
			echo('						<a href="mailto:'.$membership['email'].'@york.ac.uk">'.$membership['username'].'</a>'."\n");
		}
		else {
			echo('						'.$membership['username']."\n");
		}
?>
					</td>
					<td align="center">
<?php 
		if ($membership['user_confirmed'] && $membership['org_confirmed']) {
			echo('						<div style="display: none;">confirmed</div>'."\n");
			echo('						<img src="/images/prototype/members/confirmed.png" alt="Confirmed Member" title="Confirmed Member" />'."\n");
		}
		elseif ($membership['user_confirmed']) {
			echo('						<div style="display: none;">approval</div>'."\n");
			echo('						<img src="/images/prototype/members/user_confirmed.png" alt="Waiting for your approval" title="Waiting for your approval" />'."\n");
		}
		elseif ($membership['org_confirmed']) {
			echo('						<div style="display: none;">invitation</div>'."\n");
			echo('						<img src="/images/prototype/members/org_confirmed.png" alt="Invitation sent, awaiting reply" title="Invitation sent, awaiting reply" />'."\n");
		}
		else {
			echo('						<div style="display: none;">none</div>'."\n");
		}
?>
					</td>
					<td align="center">
<?php 
		if (isset($membership['paid']) && $membership['paid']) {
			echo('						<div style="display: none;">paid</div>'."\n");
			echo('						<img src="/images/prototype/members/paid.png" alt="Yes" />'."\n");
		}
		else {
			echo('						<div style="display: none;">notpaid</div>'."\n");
		}
?>
					</td>
					<td align="center">
<?php 
		if ($membership['has_business_card']) {
			if ($membership['business_card_needs_approval']) {
				echo('						<div style="display: none;">approval</div>'."\n");
				echo('						<img src="/images/prototype/members/card_awaiting_approval.png" alt="Awaiting Approval" title="Awaiting Approval" />'."\n");
			}
			elseif ($membership['business_card_expired']) {
				echo('						<div style="display: none;">expired</div>'."\n");
				echo('						<img src="/images/prototype/members/card_expired.png" alt="Expired" title="Expired" />'."\n");
			}
			else {
				echo('						<div style="display: none;">ok</div>'."\n");
				echo('						<img src="/images/prototype/members/card_active.png" alt="Has Business Card" title="Has Business Card" />'."\n");
			}
		}
		else {
			echo('						<div style="display: none;">none</div>'."\n");
		}
?>
					</td>
<?php 
		if ('manage' !== VipMode()) {
			echo('					<td align="center">'."\n");
			if (isset($membership['vip']) && $membership['vip']) {
				echo('						<div style="display: none;">vip</div>'."\n");
				echo('						<img src="/images/prototype/members/vip.png" alt="VIP" title="VIP" />'."\n");
			} 
			elseif (isset($membership['vip_requested']) && $membership['vip_requested']) {
				echo('						<div style="display: none;">requested</div>'."\n");
				echo('						<img src="/images/prototype/members/vip_requested.png" alt="Requested VIP Access" title="Requested VIP Access" />'."\n");
			}
			else {
				echo('						<div style="display: none;">none</div>'."\n");
			}
			echo('					</td>'."\n");
		}
?>
<?php 
	if ('manage' === VipMode()) {
		echo('					<td align="center">'."\n");
		if ($membership['has_byline']) {
			if ($membership['byline_needs_approval']) {
				echo('						<div style="display: none;">approval</div>'."\n");
				echo('						<img src="/images/prototype/members/byline_awaiting_approval.png" alt="Awaiting Approval" title="Awaiting Approval" />'."\n");
			}
			elseif ($membership['byline_expired']) {
				echo('						<div style="display: none;">expired</div>'."\n");
				echo('						<img src="/images/prototype/members/byline_expired.png" alt="Byline Expired" title="Byline Expired" />'."\n");
			}
			else {
				echo('						<div style="display: none;">ok</div>'."\n");
				echo('						<img src="/images/prototype/members/byline_active.png" alt="Byline OK" title="Byline OK" />'."\n");
			}
		}
		else {
			echo('						<div style="display: none;">none</div>'."\n");
		}
		echo('					</td>'."\n");
		echo('					<td align="center">'."\n");
		if ($membership['office_editor_access']) {
			echo('						<div style="display: none;">editor</div>'."\n");
			echo('						<img src="/images/prototype/members/access_editor.gif" alt="Editor Access" title="Editor Access" />'."\n");
		}
		elseif ($membership['office_writer_access']) {
			echo('						<div style="display: none;">writer</div>'."\n");
			echo('						<img src="/images/prototype/members/access_writer.gif" alt="Writer Access" title="Writer Access" />'."\n");
		}
		else {
			echo('						<div style="display: none;">none</div>'."\n");
		}
		echo('					</td>'."\n");
	}
?>	
				</tr>
<?php } ?>
			</tbody>
		</table>
	</form>

</div>
