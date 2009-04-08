<?php
/**
 * @file views/crosswords/crossword.php
 * @param $Crossword array of crossword information:
 *	- 'id'
 * @param $Winners array
 * @param $Grid
 * @param $LoggedIn
 * @param $Paths with 'ajax'
 * @param $Tips
 * @param $Comments
 */

$autosave_interval = 30; // seconds
$winners_update_interval = 60; // seconds

$width = $Grid->crossword()->grid()->width();
$height = $Grid->crossword()->grid()->height();
?><script type="text/javascript"><?php
echo(xml_escape(
	'onLoadFunctions.push(function() {'.
		'var xw =new Crossword("xw", '.js_literalise($width).', '.js_literalise($height).');'.
		($LoggedIn ? 'xw.setAutosaveInterval('.js_literalise($Paths['ajax']).', '.js_literalise($autosave_interval).');' : '').
		($Crossword['expired'] ? '' : 'xw.setWinnersUpdateInterval('.js_literalise($Paths['ajax'].'/winners').', '.js_literalise($winners_update_interval).');').
		'xw.setSolutionsAction('.js_literalise($Paths['ajax'].'/solution').','.js_literalise($Crossword['expired']?null:false).');'.
	'})'
	,false));
?></script>

<div class="BlueBox">

<?php
	// Only show list if winners are enabled
	if ($Crossword['winners'] > 0) {
		?><div class="crosswordWinners"><?php
			?><h2>winners</h2><?php
			// If it's expired and there aren't any winners then nothing interesting
			if ($Crossword['expired'] && count($Winners) < 1) {
				?><em>No winners</em><?php
			}
			else {
				?><ol id="xw-winners"><?php
				// Display all positions if not expired yet
				$max = $Crossword['winners'];
				// Otherwise just the ones taken
				if ($Crossword['expired']) {
					$max = count($Winners);
				}
				// Show positions
				for ($id = 0; $id < $max; ++$id) {
					?><li class="winner<?php echo($id); ?>"><?php
					// with names in any taken positions
					if (isset($Winners[$id])) {
						$winner = $Winners[$id];
						echo(xml_escape($winner['firstname'].' '.$winner['surname']));
					}
					else {
						echo('&nbsp;');
					}
					?></li><?php
				}
				?></ol><?php
			}
		?></div><?php
	}
?>

	<h2>crossword</h2>

<?php
	if (!$LoggedIn) {
		$login_url = site_url('login/main'.$this->uri->uri_string());
		?>
		<div>
			<?php if (!$Crossword['expired']) { ?>
			<p>
				<b>You must be logged in to do this crossword online</b>,
				otherwise you will have to wait until all the winner positions have been filled.
			<p>
			<?php } ?>
			<p>
				The Yorker provides an intuitive and interactive online interface for doing crosswords.
				Please <a href="<?php echo(xml_escape($login_url)); ?>">log in</a> to take advantage of the following features:
			</p>
			<ul>
				<li>Do crosswords online before winner positions have been filled.</li>
				<li>List your name on the crossword's winner list if you are one of the first to complete it.</li>
				<li>Save unfinished crosswords for later completion.</li>
			</ul>
		</div>
		<?php
	}
?>

	<form class="form" action="#">
		<?php
		$Grid->Load();
		if ($LoggedIn) {
		?>
		<div style="clear:both" >
			<fieldset>
				<input	class="button" type="button" value="Save"
						onclick="<?php echo(xml_escape('crossword("xw").save("'.$Paths['ajax'].'");')); ?>" />
			</fieldset>
		</div>
		<?php } ?>
	</form>

</div>
<?php

// Tips
if (null !== $Tips) {
	if (!$Tips->IsEmpty()) {
		?><div class="BlueBox"><?php
		?><h2>tips</h2><?php
		$Tips->Load();
		?></div><?php
	}
}

if (null !== $Comments) {
	$Comments->Load();
}
?>
