<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>charity info</h2>
		<form class="form" action="/office/charity/domodify" method="post" >
			<fieldset>
				<?php
				echo('<input type="hidden" name="a_charityid" value="'.$charity['id'].'" />
				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
				<label for"a_name">Name:</label>
				<input type="text" name="a_name" value="'.xml_escape($charity['name']).'" />
				<label for="a_goal">Goal Total:</label>
				<input type="text" name="a_goal" value="'.xml_escape($charity['target']).'" />
				<input type="submit" value="Modify" class="submit" name="r_submit_modify" />');
				?>
			</fieldset>
		</form>
	</div>
	<a href="/office/charity/editinfo/<?php echo($charity['id']); ?>">Return to content</a>
</div>