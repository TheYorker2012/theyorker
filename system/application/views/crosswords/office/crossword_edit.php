<?php
/**
 * @file views/crosswords/office/crossword_edit.php
 * @param $Permissions array[string => bool] including:
 * @param $Crossword array of crossword information:
 *	- 'id'
 * @param $Grid
 * @param $Paths with view save
 */

$width = $Grid->crossword()->grid()->width();
$height = $Grid->crossword()->grid()->height();
?>
<script type="text/javascript">
onLoadFunctions.push(function() {
	CrosswordEdit("xw", <?php echo($width); ?>, <?php echo($height); ?>);
});
</script>
<div class="BlueBox">

	<h2>edit crossword</h2>

	<form class="form">
		<fieldset>
			<label for="xwed_width">Width</label>
			<input	type="text" id="xwed_width" name="xwed_width" value="<?php echo($width); ?>" maxlength="2" />
			<label for="xwed_height">Height</label>
			<input	type="text" id="xwed_height" name="xwed_height" value="<?php echo($height); ?>" maxlength="2" />
			<input	class="button" type="button" value="Update Size"
					onclick="<?php echo(xml_escape('crosswordResize("xw", '.
								'document.getElementById("xwed_width"), '.
								'document.getElementById("xwed_height"));')); ?>" />
		</fieldset>
	</form>

	<form class="form">
		<?php
		$Grid->Load();
		?>
		<div style="clear:both" >
			<fieldset>
				<input	class="button" type="button" value="Save"
						onclick="<?php echo(xml_escape('crossword("xw").post("'.$Paths['save'].'");')); ?>" />
				<input	class="button" type="button" value="Return"
						onclick="<?php echo(xml_escape('parent.location="'.$Paths['view'].'"')); ?>" />
			</fieldset>
		</div>
	</form>

</div>
