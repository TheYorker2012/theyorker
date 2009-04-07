<?php
/**
 * @file views/crosswords/office/crossword_edit.php
 * @param $Permissions array[string => bool] including:
 * @param $Configuration InputInterfaces config interface.
 * @param $Tips CrosswordTipsView tip list view.
 * @param $Crossword array of crossword information:
 *	- 'id'
 * @param $Grid
 * @param $Paths with view save
 */

$width = $Grid->crossword()->grid()->width();
$height = $Grid->crossword()->grid()->height();
?><script type="text/javascript"><?php
echo(xml_escape(
	'onLoadFunctions.push(function() {'.
		'CrosswordEdit("xw", '.js_literalise($width).', '.js_literalise($height).');'.
	'})'
	,false));
?></script>
<div class="BlueBox">

	<h2>crossword configuration</h2>

	<form class="form" method="post" action="<?php echo(site_url($this->uri->uri_string())); ?>">
		<fieldset>
			<?php $Configuration->Load(); ?>
		</fieldset>

		<fieldset>
			<input	class="button" type="submit" value="Save Configuration" />
			<input	class="button" type="button" value="Return"
					onclick="<?php echo(xml_escape('parent.location="'.$Paths['view'].'"')); ?>" />
		</fieldset>
	</form>

</div>

<div class="BlueBox">

	<h2>tips</h2>

	<?php $Tips->Load(); ?>

</div>

<div class="BlueBox">

	<h2>edit crossword</h2>

	<form class="form" action="#">
		<fieldset>
			<label for="xwed_width">Width</label>
			<input	type="text" id="xwed_width" name="xwed_width" value="<?php echo($width); ?>" maxlength="2" />
			<br />
			<label for="xwed_height">Height</label>
			<input	type="text" id="xwed_height" name="xwed_height" value="<?php echo($height); ?>" maxlength="2" />
			<br />
			<input	class="button" type="button" value="Update Size"
					onclick="<?php echo(xml_escape('crosswordResize("xw", '.
								'document.getElementById("xwed_width"), '.
								'document.getElementById("xwed_height"));')); ?>" />
		</fieldset>
	</form>

	<form class="form" action="#">
		<?php
		$Grid->Load();
		?>
		<div style="clear:both" >
			<fieldset>
				<input	class="button" type="button" value="Save"
						onclick="<?php echo(xml_escape('crossword("xw").save("'.$Paths['save'].'");')); ?>" />
				<input	class="button" type="button" value="Return"
						onclick="<?php echo(xml_escape('parent.location="'.$Paths['view'].'"')); ?>" />
			</fieldset>
		</div>
	</form>

</div>
