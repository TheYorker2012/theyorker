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

/// Format a javascript string.
function jsString($string)
{
	return	'"'.
			str_replace(
				array('"',  '</'),
				array('\"', '<"+"/'),
				$string
			).
			'"';
}

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
		echo('<tr id="calsub_org_'.htmlentities($org['shortname'], ENT_QUOTES, 'UTF-8').'">'."\n");
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
		echo("\t".'<td><img src="/images/prototype/news/'.($org['member'] ? 'accepted.gif' : 'declined.gif').'" alt="'.($org['member'] ? 'Yes' : 'No').'" /></td>'."\n");
		{
			echo("\t".'<td><a href="');
			if ($org['calendar']) {
				echo(site_url($Path->OrganisationUnsubscribe($org['shortname'], 'calendar')));
			} else {
				echo(site_url($Path->OrganisationSubscribe($org['shortname'], 'calendar')));
			}
			echo(get_instance()->uri->uri_string().'">');
			echo('<img src="/images/prototype/news/'.($org['calendar'] ? 'accepted.gif' : 'declined.gif').'" alt="'.($org['calendar'] ? 'Yes' : 'No').'" />');
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
function addSubscriptionOrganisationsJsData(& $orgs, $depth = 0, $parent = NULL)
{
	foreach ($orgs as & $org) {
		// Add a hash element for this org
		echo("\t".jsString($org['shortname']).' : [ '.jsString($org['name']).', '.
				($parent !== NULL ? jsString($parent) : 'null').', '.
				'[ ');
		if (isset($org['teams'])) {
			$comma = '';
			foreach ($org['teams'] as & $team) {
				echo($comma.jsString($team['shortname']));
				$comma = ', ';
			}
		}
		echo(' ], '.($org['member']?'true':'false').', '.($org['calendar']?'true':'false').' ],'."\n");
		// Add rows for any child orgs
		if (isset($org['teams'])) {
			addSubscriptionOrganisationsJsData($org['teams'], $depth + 1, $org['shortname']);
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
	<h2 class="first">What&apos;s this?</h2>
	<?php if (isset($Wikitexts['help_main'])) { echo($Wikitexts['help_main']); } ?>
	
	<h2>Filter</h2>
	<div>
		<form>
			<fieldset>
				<label for="org_filter">Filter</label>
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
					<th colspan="2">Organisation</th>
					<th>Member</th>
					<th>Calendar</th>
				</tr>
<?php	addSubscriptionOrganisationRows($Organisations, $Path);	?>
			</table>
		</div>
	</div>
</div>
