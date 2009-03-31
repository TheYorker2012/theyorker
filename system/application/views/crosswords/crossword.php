<?php
/**
 * @file views/crosswords/crossword.php
 * @param $Crossword array of crossword information:
 *	- 'id'
 * @param $Grid
 */

$width = $Grid->crossword()->grid()->width();
$height = $Grid->crossword()->grid()->height();
?><script type="text/javascript"><?php
echo(xml_escape(
	'onLoadFunctions.push(function() {'.
		'new Crossword("xw", '.js_literalise($width).', '.js_literalise($height).');'.
	'})'
	,false));
?></script>

<div class="BlueBox">

	<h2>crossword</h2>

	<form class="form">
		<?php
		$Grid->Load();
		?>
	</form>

</div>
