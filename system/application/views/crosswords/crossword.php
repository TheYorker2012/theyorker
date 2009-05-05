<?php
/**
 * @file views/crosswords/crossword.php
 * @param $Crossword array of crossword information:
 *	- 'id'
 * @param $Winners array
 * @param $Grid
 * @param $LoggedIn null,true,false
 * @param $Paths with 'ajax'
 * @param $Tips
 * @param $Comments
 * @param $Links array
 */

$autosave_interval = 30; // seconds
$winners_update_interval = 60; // seconds

$width = $Grid->crossword()->grid()->width();
$height = $Grid->crossword()->grid()->height();
?><script type="text/javascript"><?php
echo(xml_escape(
	'onLoadFunctions.push(function() {'.
		'var xw =new Crossword("xw", '.js_literalise($width).', '.js_literalise($height).');'.
		((true===$LoggedIn && isset($Paths['ajax'])) ? 'xw.setAutosaveInterval('.js_literalise($Paths['ajax']).', '.js_literalise($autosave_interval).');' : '').
		(($Crossword['expired'] || !isset($Paths['ajax'])) ? '' : 'xw.setWinnersUpdateInterval('.js_literalise($Paths['ajax'].'/winners').', '.js_literalise($winners_update_interval).');').
		(isset($Paths['ajax']) ? 'xw.setSolutionsAction('.js_literalise($Paths['ajax'].'/solution').','.js_literalise($Crossword['expired']?null:false).');' : '').
	'})'
	,false));
?></script><?php

?><div class="BlueBox"><?php
	?><h2><?php
		if ($Crossword['publication'] !== null) {
			$pub = new Academic_time($Crossword['publication']);
			echo($pub->Format('D ').$pub->AcademicTermNameUnique().' week '.$pub->AcademicWeek());
		}
		else {
			?>unscheduled<?php
		}
	?></h2><?php

	if (!empty($Links)) {
		?><ul><?php
			// Main links
			foreach ($Links as $label => $url) {
				?><li><a href="<?php echo(xml_escape($url)); ?>"><?php
					echo(xml_escape($label));
				?></a></li><?php
			}
		?></ul><?php
	}
	?><h3>contents</h3><?php
	?><ul><?php
		// Contents
		?><li><a href="#crossword">crossword</a></li><?php
		if (null !== $Tips && !$Tips->IsEmpty()) {
			?><li><a href="#tips">tips</a></li><?php
		}
		if (null !== $Comments) {
			?><li><a href="#comments">comments</a></li><?php
		}
	?></ul><?php
?></div>

<div id="crossword" class="BlueBox">

<?php
	// Only show list if winners are enabled
	if ($Crossword['winners'] > 0 && null !== $Winners) {
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

	?><h2><?php
		?>Crossword<?php
		if (count($Crossword['author_fullnames']) > 0) {
			?> by <?php
			echo(xml_escape(join(', ', $Crossword['author_fullnames'])));
		}
	?></h2><?php

	if (false===$LoggedIn) {
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
		if (true===$LoggedIn && isset($Paths['ajax'])) {
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
if (null !== $Tips && !$Tips->IsEmpty()) {
	?><div id="tips" class="BlueBox"><?php
	?><h2>tips</h2><?php
	$Tips->Load();
	?></div><?php
}

if (null !== $Comments) {
	$Comments->Load();
}
?>
