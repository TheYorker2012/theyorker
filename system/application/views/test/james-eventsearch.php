<div class="BlueBox">
	<form method="get" action="<?php echo($target); ?>">
		<input name="search" type="text" value="<?php echo(xml_escape($search)); ?>" />
		<input type="submit" />
	</form>
</div>

<div class="BlueBox">
	<?php if (isset($results)) var_dump($results); ?>
</div>