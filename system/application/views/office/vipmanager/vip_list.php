<?php
/**
 * @file vip_list.php
 * @brief Show a list of all the vips in all organisations
 */
 
/// Draw a column heading sorting hyperlink.
function SortLink($filter, $sort_fields, $field, $title)
{
	echo('<a href="' . $filter['base'] . '/'.
		((isset($sort_fields[$field]) && $sort_fields[$field])
			? 'desc' : 'asc').'/'.$field . '">');
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

?>

<div id="RightColumn">
	<h2 class="first">Quick Links</h4>
	<div class="entry">
		<a href="/office/vipmanager">Back To VIP List</a>
	</div>
	<h2>What's this?</h2>
	<div class="Entry">
		<?php echo $main_text; ?>
	</div>
	<h2>Search</h2>
	<div class="Entry">
		<input type="text" name="search" id="search" onkeyup="searchVIPList();" />
	</div>
	<div class="Entry">
		<label for="filter_vip_status">VIP Status, Showing:</label>
		<select id="filter_vip_status" onchange="searchVIPList();">
			<option value="all" selected="selected">All</option>
			<option value="vip">VIP</option>
			<option value="requested">Requested</option>
			<option value="novip">None</option>
		</select>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>VIPs</h2>

		<p>This is a list of the VIPs in every organisation. VIP requests will appear at the top of the list.</p>

		<form class="form" method="post" action="<?php echo($target); ?>" id="member_select_form">

			<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2" width="100%">
				<thead>
					<tr style="background-color: #eee">
						<th align="center">
							<input type="checkbox" name="members_selected[]" value="userSelectAllNone" id="userSelectAllNone" />
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
						<th>
							<?php SortLink($filter, $sort_fields, 'organisation','Organisation'); ?>
						</th>
						<th align="center">
							VIP
						</th>
					</tr>
				</thead>
				<tbody id="MemberTable">
					<tr id="NotFound" style="display: none;">
						<td colspan="6" style="text-align: center;">
							No Matching Entries
						</td>
					</tr>
<?php
		foreach ($members as $membership) {
?>
					<tr id="userid<?php echo($membership['user_id']); ?>">
						<td align="center">
							<input type="checkbox" name="members_selected[]" value="user<?php echo($membership['user_id']); ?>" id="user<?php echo($membership['user_id']); ?>" />
						</td>
						<td>
							<a href="<?php echo('/office/vipmanager/info/'.$membership['organisation_entity_id'].'/'.$membership['user_id']); ?>"><?php echo($membership['firstname']); ?></a>
						</td>
						<td>
							<a href="<?php echo('/office/vipmanager/info/'.$membership['organisation_entity_id'].'/'.$membership['user_id']); ?>"><?php echo($membership['surname']); ?></a>
						</td>
						<td>
<?php if (NULL !== $membership['email']) { ?>
							<a href="mailto:<?php echo($membership['email']);?>@york.ac.uk"><?php echo($membership['username']); ?></a>
<?php } else {?>
							<?php echo($membership['username']); ?>
<?php } ?>
						</td>
						<td>
							<a href="/office/reviews/<?php echo($membership['organisation_codename']); ?>"><?php echo($membership['organisation_name']); ?></a>
						</td>
						<td align="center">
<?php if (isset($membership['vip']) && $membership['vip']) { ?>
							<div style="display: none;">vip</div>
							<img src="/images/prototype/members/vip.png" alt="VIP" title="VIP" />
<?php } elseif (isset($membership['vip_requested']) && $membership['vip_requested']) { ?>
							<div style="display: none;">requested</div>
							<img src="/images/prototype/members/vip_requested.png" alt="Requested VIP Access: Click to Promote" title="Requested VIP Access: Click to Promote" />
<?php } else { ?>
							<div style="display: none;">novip</div>
<?php } ?>
						</td>
					</tr>
<?php } ?>
				</tbody>
			</table>
		</form>
	</div>
</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>
