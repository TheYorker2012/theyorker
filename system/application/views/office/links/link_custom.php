<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>Nominate Custom Link</h2>
		<form action="/office/links/customlink" method="post" enctype="multipart/form-data">
		<fieldset>
			<label for="lname"> Link Name: </label>
			<input type="text" id="lname" name="lname" value="" default />
			<br />
			<label for="lurl"> Link URL: </label>
			<input type="text" id="lurl" name="lurl" value="http://" />
			<br />
			<label for="upload">50x50 Image: </label>
			<input type="file" id="upload" name="upload" />
			<br />
			<input type="button" value="Back" class="button" onClick="window.location='/office/links';"> <input type="submit" value="Create Link" class="button">
		</fieldset>
		</form>
	</div>
</div>

