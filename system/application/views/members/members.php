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

	<H4>Filter</H4>
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
		<a href="#">advanced filter options</a>
	
	<H4>Find in List</H4>
		<P>
			<input value="" />
		</P>
		
	<H4>Commands</H4>
		<P>The following action can be performed on the selected members</P>
		<P>
			<select>
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
			<input type="button" value="Go" />
		</P>
	
<?php
$filter['descriptors'] = array(
	array('description' => 'with VIP priv','link_remove' => '#'),
	array('description' => 'in Sections/HowDoI','link_remove' => '#'),
);
if (!empty($filter['descriptors'])) {
	?>
	<H4>Advanced Filters</H4>
	<P>Showing all:
	<UL><?php
	foreach ($filter['descriptors'] as $descriptor) {
		?><LI>
		<?php echo $descriptor['description']; ?>
		<SMALL>
		(<A HREF="<?php echo vip_url($filter['base'].'/'.$descriptor['link_remove']); ?>">remove</A>)
		</SMALL>
		</LI><?php
	}
	?></UL>
	<SMALL><A HREF="<?php echo vip_url('members/list'); ?>">remove all filters</A></SMALL>
	</P>
	<P>
		<select>
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
		<input type="button" value="Add Filter" />
	</P>
	<P><a href="#">basic filter</a></P>
	
	<?php
}


?>
</div>
<?php $this->load->view('members/members_list');?>
<a href='<?php echo vip_url(); ?>'>Back to the vip area.</a>