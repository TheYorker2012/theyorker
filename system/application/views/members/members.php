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

<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<?php echo $main_text; ?>
	</div>

	<h2>Filter</h2>
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
	
		<h2>Find in List</h2>
		<p>
			<input value="" />
		</p>
	
<?php
$filter['descriptors'] = array(
	array('description' => 'with VIP priv','link_remove' => '#'),
	array('description' => 'in Sections/HowDoI','link_remove' => '#'),
);
if (!empty($filter['descriptors'])) {
	?>
	<h2>Advanced Filters</h2>
	<p>Showing all:</p>
	<ul><?php
	foreach ($filter['descriptors'] as $descriptor) {
		?><li>
		<?php echo $descriptor['description']; ?>
		<small>
		(<a href="<?php echo vip_url($filter['base'].'/'.$descriptor['link_remove']); ?>">remove</a>)
		</small>
		</li><?php
	}
	?></ul>
	<small><a href="<?php echo vip_url('members/list'); ?>">remove all filters</a></small>
	<p>
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
		<input type="button" value="Add Filter" />
	</p>
	<p><a href="#">basic filter</a></p>
	
	<?php
}


?>
</div>
<div id="MainColumn">
<?php $this->load->view('members/members_list');?>
</div>

<?php

echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');

?>
