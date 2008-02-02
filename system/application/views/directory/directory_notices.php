<?php
	/// Draw a branch of the tree of teams
	function DoTeam($team, $in_list = TRUE)
	{
		if ($in_list) {
			echo('<li>');
		}
		if (isset($team['notices'])) {
			echo('<a href="">');
		}
		echo(xml_escape($team['name']));
		if (isset($team['notices'])) {
			echo(' ('.count($team['notices']).' notices)');
			echo('</a>');
		}
		if (!empty($team['subteams'])) {
			echo('<ul>');
			foreach ($team['subteams'] as $subteam) {
				DoTeam($subteam);
			}
			echo('</ul>');
		}
		if ($in_list) {
			echo('</li>');
		}
		return count($team['subteams']);
	}
?>
<?php
/*
<a href="">Only relevent notices</a><br />
<a href="">All notices</a><br />
<?php
	if (!empty($teams['subteams'])) {
		// Draw the tree of teams
		foreach ($teams['subteams'] as $team) {
			if (!DoTeam($team, FALSE)) {
				echo('<br />');
			}
		}
	}
?>

<pre>$organisation=<?php var_dump($organisation); ?></pre>
<pre>$teams=<?php var_dump($teams); ?></pre>
<pre>$notices=<?php var_dump($notices); ?></pre>
*/
?>

<div id="RightColumn">
	<h2 class="first">Notice Boards</h2>
	<div class="Entry">
		Some filters like on the directory index will go here, assuming the organisation has teams
	</div>
</div>
<div id="MainColumn">
<?php
foreach($notices as $notice) {
?>
	<div class="BlueBox">
		<h2><?php echo(xml_escape($notice['notice_subject'])); ?></h2>
		<div class="Date"><?php echo(xml_escape($notice['notice_updated'])); ?></div>
		<div class="Author">
<?php
	$recpts = array();
	foreach($notice['recipients'] as $recpt) {
		$recpts[] = xml_escape($teams_all[$recpt]['name']);
	}
	echo(implode(', ', $recpts));
?>
		</div>
		<div><?php /* notice_content_cache is xhtml */ ?>
			<?php echo('<p>'.$notice['notice_content_cache'].'</p>'); ?>
		</div>
	</div>
<?php
}
?>
</div>
