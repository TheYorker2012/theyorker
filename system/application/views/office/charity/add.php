<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>add charity</h2>
		<form class="form" action="/office/charity/charitymodify" method="post">
			<fieldset>
	<?php
		echo('			<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
	?>
			</fieldset>
			<fieldset>
				<label for="a_charityname">Name:</label>
				<input type="text" name="a_charityname" id="a_charityname" />
			<fieldset>
			</fieldset>
				<input type="submit" value="Add" class="button" name="r_submit_add" />
			</fieldset>
		</form>
	</div>
</div>