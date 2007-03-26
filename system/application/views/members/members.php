<?php


/// Draw a branch of the tree of teams
function EchoTeamFilterOptions($team, $prefix = '', $path = '', $indentation = 0)
{
	foreach ($team['subteams'] as $subteam) {
		echo '<option name="team_'.$subteam['id'].'">';
		//echo str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;',$indentation);
		echo $prefix.$path.$subteam['name'];
		echo '</option>';
		if (!empty($subteam['subteams'])) {
			EchoTeamFilterOptions($subteam, $prefix, $path.$subteam['name'].'/', $indentation+1);
		}
	}
}

?>

<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>

	<H4>Search</H4>
		<input type="checkbox" /> Within current filter<br />
		<input value="" />
		<input type="submit" value="Search" />
	<H4>Filters</H4>
		<select>
			<option>All members</option>
			<optgroup label="Member status:">
				<option>Confirmed</option>
				<option>Unconfirmed</option>
				<option>Paying</option>
				<option>Non-paying</option>
				<option>VIPs</option>
				<option>Non-VIPs</option>
			</optgroup>
			<optgroup label="Business card status:">
				<option>With active business card</option>
				<option>Still writing business card</option>
				<option>With expired business card</option>
				<option>Without business card</option>
			</optgroup>
			<?php
				if (!empty($organisation['subteams'])) {
					echo '<optgroup label="In team:">';
					EchoTeamFilterOptions($organisation, '', FALSE);
					echo '</optgroup>';
				}
			?>
		</select>
		<input type="submit" value="Show" />
		<a href="#">advanced filter options</a>
		<p>Showing confirmed members</p>
<?php
if (!empty($filter['descriptors'])) {
	?><P>
	<H4>Advanced Filters</H4>
	<SMALL><A HREF="<?php echo vip_url('members/list'); ?>">remove all</A></SMALL>
	<OL><?php
	foreach (array_reverse($filter['descriptors']) as $descriptor) {
		?><LI>
		<?php echo $descriptor['description']; ?><br />
		<SMALL>
		(<A HREF="<?php echo vip_url($filter['base'].'/'.$descriptor['link_invert']); ?>">invert filter</A> |
		<A HREF="<?php echo vip_url($filter['base'].'/'.$descriptor['link_remove']); ?>">remove filter</A>)
		</SMALL>
		</LI><?php
	}
	?></OL>
	</P><?php
}


?>
</div>
<?php $this->load->view('members/members_list');?>
<a href='<?php echo vip_url(); ?>'>Back to the vip area.</a>