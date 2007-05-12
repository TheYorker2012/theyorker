<?php
	/// Draw a branch of the tree of teams
	function DoTeam($team, $in_list = TRUE)
	{
		if ($in_list) {
			echo '<LI>';
		}
		if (isset($team['notices'])) {
			echo '<a href="">';
		}
		echo $team['name'];
		if (isset($team['notices'])) {
			echo ' ('.count($team['notices']).' notices)';
			echo '</a>';
		}
		if (!empty($team['subteams'])) {
			echo '<UL>';
			foreach ($team['subteams'] as $subteam) {
				DoTeam($subteam);
			}
			echo '</UL>';
		}
		if ($in_list) {
			echo '</LI>';
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
				echo '<br />';
			}
		}
	}
?>

<PRE>$organisation=<?php var_dump($organisation); ?></PRE>
<PRE>$teams=<?php var_dump($teams); ?></PRE>
<PRE>$notices=<?php var_dump($notices); ?></PRE>
*/
?>

<?php
foreach($notices as $notice) {
?>
	<div class="BlueBox">
		<h2><?php echo(htmlspecialchars($notice['notice_subject'])); ?></h2>
		<div class="Date"><?php echo(htmlspecialchars($notice['notice_updated'])); ?></div>
		<div class="Author">
<?php
	$recpts = array();
	foreach($notice['recipients'] as $recpt) 
		$recpts[] = htmlspecialchars($teams_all[$recpt]['name']);
	echo(implode(', ', $recpts));
?>
		</div>
		<div>
			<?php echo('<p>'.$notice['notice_content_cache'].'</p>'); ?>
		</div>
	</div>
<?php
}
?>
