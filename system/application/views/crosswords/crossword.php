<?php
/**
 * @file views/crosswords/crossword.php
 * @param $Crossword array of crossword information:
 *	- 'id'
 * @param $Grid
 * @param $LoggedIn
 * @param $Paths with 'save'
 */

$width = $Grid->crossword()->grid()->width();
$height = $Grid->crossword()->grid()->height();
?><script type="text/javascript"><?php
echo(xml_escape(
	'onLoadFunctions.push(function() {'.
		'var xw =new Crossword("xw", '.js_literalise($width).', '.js_literalise($height).');'.
		($LoggedIn ? 'xw.setAutosaveInterval('.js_literalise($Paths['save']).', 30);' : '').
	'})'
	,false));
?></script>

<div class="BlueBox">

	<h2>crossword</h2>
<?php
	if (!$LoggedIn) {
		$login_url = site_url('login/main'.$this->uri->uri_string());
		?>
		<div>
			<p><b>You must be logged in to do crosswords online.</b>
			Please <a href="<?php echo(xml_escape($login_url)); ?>">log in now</a> to take advantage of the following features:</p>
			<ul>
				<li>Intuitive and interactive online interface.</li>
				<li>List your name on the crossword's winners list if you are one of the first to complete it.</li>
				<li>Save unfinished crosswords for later completion.</li>
			</ul>
		</div>
		<?php
	}
?>

	<form class="form">
		<?php
		$Grid->Load();
		?>
		<div style="clear:both" >
			<fieldset>
				<input	class="button" type="button" value="Save"
						onclick="<?php echo(xml_escape('crossword("xw").save("'.$Paths['save'].'");')); ?>" />
			</fieldset>
		</div>
	</form>

</div>
