<?php
/**
 * @file views/admin/tools/test/static.php
 * @brief Static analyser page.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @param $Tests    array(name => description)
 */
?>
<script type="text/javascript">
	// <![CDATA[
	tests = <?php echo(js_literalise($Tests)); ?>;
	// ]]>
</script>
<div class="BlueBox">
	<h2>static analyser</h2>
	<?php /*
			<ul>
	<?php foreach ($Tests as $name => $description) { ?>
				<li title="<?php echo(xml_escape($description)); ?>">
					<a href="/admin/tools/test/static/text?tests=<?php echo(xml_escape($name)); ?>"><?php
					echo(xml_escape($name));
					?></a>
				</li>
	<?php } ?>
			</ul>
	*/ ?>
	<form>
		<fieldset>
			<?php foreach ($Tests as $name => $description) { ?>
				<label	for="test_<?php echo(xml_escape($name)); ?>"
						title="<?php echo(xml_escape($description)); ?>">
					<?php echo(xml_escape($name)); ?>
				</label>
				<input	type="checkbox"
						id="test_<?php echo(xml_escape($name)); ?>" />
			<?php } ?>
			<input	class="button" type="button" value="Run Tests"
					onclick="start_tests();" />
		</fieldset>
	</form>
	<h2>results</h2>
	<div id="results" style="overflow-y: scroll; height: 30em; font-size: small;">
	</div>
</div>
