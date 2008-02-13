<?php

/**
 * @file views/calendar/subscriptions_index.php
 * @brief Calendar subscriptions index page
 * @author James Hogan (jh559)
 *
 * @param $Wikitexts array Texts from page properties:
 *	- introduction - Intro to go at top of page.
 *	- help_main - Main help text.
 * @param $Organisations array Top level organisations:
 *  - name
 *  - shortname
 *  - member
 *  - calendar
 *  - teams
 * @param $Path calendarPaths object
 */

/// Render rows in the subscriptions table for organisations.
function addSubscriptionOrganisationRows(& $orgs, & $Path, $depth = 0)
{
	static $depth_indicator = array(
		'&nbsp;+-',
		'&nbsp;&nbsp;&nbsp;+-',
		'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-',
		'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-',
	);
	foreach ($orgs as & $org) {
		// Add a row for this org
		echo('<tr id="calsub_org_'.xml_escape($org['shortname']).'" name="'.xml_escape($org['shortname']).'">'."\n");
		echo("\t".'<td>'.(isset($depth_indicator[$depth]) ? $depth_indicator[$depth] : '').'</td>'."\n");
		{
			echo("\t".'<td>');
			if ($org['show_in_directory']) {
				echo('<a href="'.site_url('directory/'.$org['shortname']).'">');
			}
			echo(htmlentities($org['name'], ENT_QUOTES, 'UTF-8'));
			if ($org['show_in_directory']) {
				echo('</a>');
			}
			echo('</td>'."\n");
		}
		echo("\t".'<td><img src="/images/'.($org['member'] ? 'icons/user.png' : 'icons/user_gray.png').'" title="'.($org['member'] ? 'You are a member of ' : 'You are not a member of ').xml_escape($org['name']).'" alt="'.($org['member'] ? 'Yes' : 'No').'" /></td>'."\n");
		{
			echo("\t".'<td><a href="');
			if ($org['calendar']) {
				echo(site_url($Path->OrganisationUnsubscribe($org['shortname'], 'calendar')));
			} else {
				echo(site_url($Path->OrganisationSubscribe($org['shortname'], 'calendar')));
			}
			echo(get_instance()->uri->uri_string().'">');
			echo('<img src="/images/'.($org['calendar'] ? 'icons/date.png' : 'prototype/news/declined.gif').'" title="'.($org['calendar'] ? 'Click to unsubscribe from ' : 'Click to subscribe to ').xml_escape($org['name']).'"  alt="('.($org['calendar'] ? 'yes' : 'no').')" />');
			echo('</a></td>'."\n");
			echo('</tr>'."\n");
		}
		// Add rows for any child orgs
		if (isset($org['teams'])) {
			addSubscriptionOrganisationRows($org['teams'], $Path, $depth + 1);
		}
	}
}

/// Render data in the subscriptions table for javascript.
function addSubscriptionOrganisationsJsData(& $orgs, $depth = 0, $parent = NULL, $main_comma = '')
{
	foreach ($orgs as & $org) {
		echo($main_comma);
		$main_comma = ",\n";
		// Add a hash element for this org
		echo("\t".js_literalise($org['shortname']).' : [ '.js_literalise($org['name']).', '.
				($parent !== NULL ? js_literalise($parent) : 'null').', '.
				'[ ');
		if (isset($org['teams'])) {
			$comma = '';
			foreach ($org['teams'] as & $team) {
				echo($comma.js_literalise($team['shortname']));
				$comma = ', ';
			}
		}
		echo(' ], '.js_literalise($org['member']).', '.js_literalise($org['calendar']).' ]');
		// Add rows for any child orgs
		if (isset($org['teams'])) {
			addSubscriptionOrganisationsJsData($org['teams'], $depth + 1, $org['shortname'], $main_comma);
		}
	}
}

?>

<?php	// Organisation data in javacscript
?><script type="text/javascript">
//<![CDATA[
calsub_orgs = {
<?php addSubscriptionOrganisationsJsData($Organisations); ?>
};
//]]>
</script>

<div id='RightColumn'>
	<h2 class="first">What&#039;s this?</h2>
	<?php if (isset($Wikitexts['help_main'])) { echo($Wikitexts['help_main']); } ?>
	
	<h2>Search</h2>
	<div>
		<form>
			<fieldset>
				<label for="org_filter">Name</label>
				<input type="text" id="org_filter" value="" onkeyup="calsub_filter_orgs();" />
				
				<label for="org_filter_member">Membership</label>
				<select id="org_filter_member" onchange="calsub_filter_orgs();">
					<option value="0"></option>
					<option value="yes">Member</option>
					<option value="no">Not member</option>
				</select>
				
				<label for="org_filter_calendar">Subscription</label>
				<select id="org_filter_calendar" onchange="calsub_filter_orgs();">
					<option value="0"></option>
					<option value="yes">Subscribed to calendar</option>
					<option value="no">Not subscribed to calendar</option>
				</select>
			</fieldset>
		</form>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<?php if (isset($Wikitexts['intro'])) { echo($Wikitexts['intro']); } ?>
		
		<div>
			<table class="calsub_orglist">
				<tr>
					<th colspan="2" width="80%">organisation</th>
					<th width="10%">member</th>
					<th width="10%">subscribed</th>
				</tr>
<?php	addSubscriptionOrganisationRows($Organisations, $Path);	?>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
calsub_filter_orgs();
</script>
