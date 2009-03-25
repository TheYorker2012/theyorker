<?php
/**
 * @file views/crosswords/office/crossword_edit.php
 * @param $Permissions array[string => bool] including:
 * @param $Crossword array of crossword information:
 *	- 'id'
 * @param $Grid
 * @param $Paths with save
 */
?>

<div class="BlueBox">

	<h2>Edit Crossword</h2>

	<pre><?php //echo(xml_escape(print_r($_GET))); ?></pre>

	<form class="form">
		<?php
		$Grid->Load();
		?>
		<div style="clear:left" >
			<fieldset>
				<input	class="button" type="button" name="xwed_save" value="Save"
						onclick="<?php echo(xml_escape('crossword("xw").post("'.$Paths['save'].'");')); ?>" />
			</fieldset>
		</div>
	</form>

</div>
